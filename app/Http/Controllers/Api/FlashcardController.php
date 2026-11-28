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
        $canUseQuestionBank = true;
        $validSectors = ['hsc', 'admission', 'job', 'ssc'];

        $sectorMeta = [
            'hsc' => ['key' => 'hsc', 'label_bn' => 'এইচএসসি (HSC)', 'label_en' => 'HSC'],
            'admission' => ['key' => 'admission', 'label_bn' => 'বিশ্ববিদ্যালয় ভর্তি', 'label_en' => 'Admission'],
            'job' => ['key' => 'job', 'label_bn' => 'বিসিএস ও চাকরি', 'label_en' => 'BCS & Jobs'],
            'ssc' => ['key' => 'ssc', 'label_bn' => 'এসএসসি (SSC)', 'label_en' => 'SSC'],
        ];

        $sectorCounts = QuestionBank::published()
            ->whereIn('segment', $validSectors)
            ->selectRaw('segment, COUNT(*) as count')
            ->groupBy('segment')
            ->pluck('count', 'segment');

        $qbSectors = [];
        foreach ($validSectors as $secKey) {
            $cnt = (int) ($sectorCounts[$secKey] ?? 0);
            if ($cnt > 0) {
                $qbSectors[] = array_merge($sectorMeta[$secKey], ['count' => $cnt]);
            }
        }

        $studentSector = (in_array($student->type, $validSectors, true) && (int) ($sectorCounts[$student->type] ?? 0) > 0)
            ? $student->type
            : ($qbSectors[0]['key'] ?? 'admission');

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

        $subjectsBySector = [];
        foreach (array_column($qbSectors, 'key') as $secKey) {
            $subjectsBySector[$secKey] = QuestionSubject::active()
                ->whereHas('questions', fn ($q) => $q->published()->where('segment', $secKey))
                ->withCount(['questions' => fn ($q) => $q->published()->where('segment', $secKey)])
                ->orderBy('name_bn')
                ->get(['id', 'name_bn', 'name_en', 'segment'])
                ->values();
        }

        $subjects = $subjectsBySector[$studentSector] ?? collect();
        if ($subjects->isEmpty()) {
            $subjects = QuestionSubject::active()
                ->whereHas('questions', fn ($q) => $q->published())
                ->withCount(['questions' => fn ($q) => $q->published()])
                ->orderBy('name_bn')
                ->get(['id', 'name_bn', 'name_en', 'segment']);
        }

        $subjectExams = QuestionBank::published()
            ->whereNotNull('origin_tag')
            ->whereNotNull('subject_id')
            ->selectRaw('segment, subject_id, origin_tag, MAX(exam_name) as exam_name, COUNT(*) as count')
            ->groupBy('segment', 'subject_id', 'origin_tag')
            ->having('count', '>', 0)
            ->orderByDesc('count')
            ->get();

        $exams = QuestionBank::published()
            ->where('segment', $studentSector)
            ->whereNotNull('origin_tag')
            ->selectRaw('origin_tag, MAX(exam_name) as exam_name, COUNT(*) as count')
            ->groupBy('origin_tag')
            ->having('count', '>', 0)
            ->orderByDesc('count')
            ->limit(30)
            ->get();

        return $this->success([
            'books' => $books,
            'my_books' => $myBooks,
            'subjects' => $subjects,
            'exams' => $exams,
            'qb_sectors' => $qbSectors,
            'subjects_by_sector' => $subjectsBySector,
            'subject_exams' => $subjectExams,
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
            $validSectors = ['admission', 'job', 'hsc', 'ssc'];
            $sector = $request->input('sector') ?: (in_array($student->type, $validSectors, true) ? $student->type : null);
            $sampleLimit = max(25, min(50, (int) $request->count * 2));
            $source = $sources->fromQuestionBank(
                (int) $request->subject_id,
                $request->origin_tag,
                $sector,
                $sampleLimit
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
                'subject_id' => $request->subject_id ? (int) $request->subject_id : null,
                'origin_tag' => $request->origin_tag,
                'sector' => $request->input('sector'),
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
        } elseif (($data['source_type'] ?? null) === 'question_bank' && ! empty($data['subject_id'])) {
            $source = $sources->fromQuestionBank(
                (int) $data['subject_id'],
                $data['origin_tag'] ?? null,
                $data['sector'] ?? null,
                max(25, min(50, $count * 2)),
            );
            $sourceText = $source['content'];
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
