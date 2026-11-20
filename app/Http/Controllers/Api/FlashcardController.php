<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\GenerateFlashcardsRequest;
use App\Jobs\GenerateFlashcards;
use App\Models\Book;
use App\Models\Flashcard;
use App\Models\FlashcardSet;
use App\Models\MyBook;
use App\Models\QuestionBank;
use App\Models\QuestionSubject;
use App\Services\AccessGrantService;
use App\Services\Ai\FlashcardSourceService;
use App\Services\BookAccessService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;

class FlashcardController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success(auth('sanctum')->user()->flashcardSets()->withCount('flashcards')->latest()->get());
    }

    public function createData(BookAccessService $access): JsonResponse
    {
        $student = auth('sanctum')->user();
        $canUseQuestionBank = in_array($student->type, ['admission', 'job'], true);
        $studentSector = $student->type === 'job' ? 'job' : 'admission';

        $books = Book::query()
            ->where(function ($query) use ($student) {
                $query->whereIn('id', $student->books()->select('books.id'))
                    ->orWhereIn('id', $student->ownedChapters()->select('book_id'));
            })
            ->get(['id', 'title', 'processing_status'])
            ->map(function (Book $book) use ($student, $access) {
                $accessibleIds = $access->accessibleChapterIds($student, $book) ?? [];
                $book->setAttribute('chapters', $book->chapters()
                    ->whereIn('id', $accessibleIds)
                    ->get(['id', 'title', 'chapter_number']));

                return $book;
            });

        $myBooks = $student->myBooks()
            ->where('processing_status', 'completed')
            ->get(['id', 'title', 'total_pages']);

        $subjects = $canUseQuestionBank
            ? QuestionSubject::active()
                ->where(function ($q) use ($studentSector) {
                    $q->where('segment', $studentSector)->orWhere('segment', 'both');
                })
                ->orderBy('name_bn')
                ->get(['id', 'name_bn', 'name_en', 'segment'])
            : collect();

        $exams = $canUseQuestionBank
            ? QuestionBank::published()
                ->where('segment', $studentSector)
                ->whereNotNull('origin_tag')
                ->selectRaw('origin_tag, exam_name, count(*) as count')
                ->groupBy('origin_tag', 'exam_name')
                ->having('count', '>', 0)
                ->orderByDesc('count')
                ->limit(30)
                ->get()
            : collect();

        return $this->success([
            'books' => $books,
            'my_books' => $myBooks,
            'subjects' => $subjects,
            'exams' => $exams,
            'can_use_question_bank' => $canUseQuestionBank,
        ]);
    }

    public function store(GenerateFlashcardsRequest $request, FlashcardSourceService $sources): JsonResponse
    {
        $student = auth('sanctum')->user();
        abort_unless($student->hasActiveAiAccess(), 403, 'AI trial exhausted.');

        $sourceType = $request->input('source_type', 'topic');
        $sourceText = '';
        $sourceLabel = null;
        $sourceReferences = [];

        if ($sourceType === 'book' || $request->source_book_id || $request->source_my_book_id) {
            if ($request->source_my_book_id) {
                $myBook = MyBook::findOrFail($request->source_my_book_id);
                $source = $sources->fromMyBook($myBook, $student);
            } else {
                $source = $sources->fromBook(
                    Book::findOrFail($request->source_book_id),
                    $student,
                    $request->input('chapter_ids', []),
                );
            }
            $sourceText = $source['content'];
            $sourceLabel = $source['source_label'];
            $sourceReferences = $source['references'];
        } elseif ($sourceType === 'question_bank') {
            abort_unless(in_array($student->type, ['admission', 'job'], true), 403, 'Question Bank flashcards are currently available only for Admission and Job students.');
            $source = $sources->fromQuestionBank(
                (int) $request->subject_id,
                $request->origin_tag,
                $student->type
            );
            $sourceText = $source['content'];
            $sourceLabel = $source['source_label'];
        } elseif ($sourceType === 'paragraph') {
            $source = $sources->fromParagraph((string) $request->source_text);
            $sourceText = $source['content'];
            $sourceLabel = $source['source_label'];
        } else {
            $topic = $request->source_topic ?: $request->title;
            $source = $sources->fromTopic((string) $topic);
            $sourceText = $source['content'];
            $sourceLabel = $source['source_label'];
        }

        $set = $student->flashcardSets()->create([
            'title' => $request->title,
            'source_label' => $sourceLabel,
            'generation_status' => 'generating',
            'generation_data' => [
                'source_type' => $sourceType,
                'source_book_id' => $request->source_book_id,
                'source_my_book_id' => $request->source_my_book_id,
                'chapter_ids' => $request->input('chapter_ids', []),
                'source_text' => $sourceType === 'paragraph' ? $sourceText : null,
                'count' => (int) $request->count,
            ],
        ]);
        GenerateFlashcards::dispatch($set->id, $sourceText, (int) $request->count, $sourceReferences);

        if (! app(AccessGrantService::class)->hasActiveAccess($student)) {
            $student->increment('ai_trial_minutes_used');
        }

        return $this->success($set, 'Flashcard generation started', 201);
    }

    public function retry(FlashcardSet $set, FlashcardSourceService $sources): JsonResponse
    {
        $this->authorizeSet($set);
        abort_unless($set->generation_status === 'failed', 409, 'Only failed flashcard sets can be retried.');

        $data = $set->generation_data ?? [];
        $count = (int) ($data['count'] ?? 15);
        $sourceText = (string) ($data['source_text'] ?? $set->title);
        $sourceReferences = [];

        if (($data['source_type'] ?? null) === 'book' && ! empty($data['source_book_id'])) {
            $source = $sources->fromBook(
                Book::findOrFail($data['source_book_id']),
                auth('sanctum')->user(),
                $data['chapter_ids'] ?? [],
            );
            $sourceText = $source['content'];
            $sourceReferences = $source['references'];
        }

        $set->update(['generation_status' => 'generating']);
        GenerateFlashcards::dispatch($set->id, $sourceText, $count, $sourceReferences);

        return $this->success($set, 'Flashcard generation retried');
    }

    public function destroy(FlashcardSet $set): JsonResponse
    {
        $this->authorizeSet($set);
        $set->flashcards()->delete();
        $set->delete();

        return $this->success(null, 'Flashcard set deleted');
    }

    public function pdf(FlashcardSet $set): HttpResponse
    {
        $this->authorizeSet($set);
        $set->load('flashcards');
        $pdf = Pdf::loadView('pdf.flashcards', ['set' => $set, 'student' => auth('sanctum')->user()]);

        return $pdf->download("flashcards-{$set->id}.pdf");
    }

    public function show(FlashcardSet $set): JsonResponse
    {
        $this->authorizeSet($set);

        return $this->success($set->load('flashcards'));
    }

    public function review(Request $request, FlashcardSet $set, Flashcard $flashcard): JsonResponse
    {
        $this->authorizeSet($set);
        $request->validate(['result' => 'required|in:known,review_again']);

        $flashcard->increment('review_count');
        $flashcard->update([
            'last_result' => $request->result,
            'next_review_at' => $request->result === 'known' ? now()->addDays(2 ** min($flashcard->review_count, 6)) : now()->addHour(),
        ]);

        return $this->success($flashcard);
    }

    private function authorizeSet(FlashcardSet $set): void
    {
        abort_unless($set->student_id === auth('sanctum')->id(), 403);
    }
}
