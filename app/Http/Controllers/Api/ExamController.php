<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\CreateExamRequest;
use App\Http\Requests\Student\SubmitAnswerRequest;
use App\Jobs\GenerateExamQuestions;
use App\Models\AiExplanation;
use App\Models\Book;
use App\Models\ExamSession;
use App\Models\LeaderboardEntry;
use App\Models\MyBook;
use App\Services\AccessGrantService;
use App\Services\Ai\AiProviderFactory;
use App\Services\BookAccessService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response as HttpResponse;

class ExamController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success(auth('sanctum')->user()->examSessions()->latest()->paginate(15));
    }

    public function createData(BookAccessService $access): JsonResponse
    {
        $student = auth('sanctum')->user();
        $books = Book::query()
            ->where(function ($query) use ($student) {
                $query->whereIn('id', $student->books()->select('books.id'))
                    ->orWhereIn('id', $student->ownedChapters()->select('book_id'));
            })
            ->get(['id', 'title'])
            ->map(function ($book) use ($student, $access) {
                $accessibleIds = $access->accessibleChapterIds($student, $book) ?? [];
                $book->setAttribute('chapters', $book->chapters()
                    ->whereIn('id', $accessibleIds)
                    ->get(['id', 'title', 'chapter_number']));

                return $book;
            });

        $myBooks = $student->myBooks()
            ->where('processing_status', 'completed')
            ->get(['id', 'title', 'total_pages']);

        return $this->success([
            'books' => $books,
            'my_books' => $myBooks,
        ]);
    }

    public function store(CreateExamRequest $request, BookAccessService $access): JsonResponse
    {
        $student = auth('sanctum')->user();
        abort_unless($student->hasActiveAiExamAccess(), 403, 'AI exam quota exhausted.');

        $chapterIds = [];
        $sourceType = $request->source_type;
        $sourceBookId = $request->source_book_id;
        $sourceText = $request->source_text;
        $title = $request->title ?: null;

        if ($sourceType === 'book') {
            if ($request->filled('source_my_book_id')) {
                $myBook = MyBook::findOrFail($request->source_my_book_id);
                abort_unless($myBook->student_id === $student->id, 403);
                abort_unless($myBook->processing_status === 'completed', 422, 'The selected book is not fully processed yet.');

                $pages = $myBook->pages()
                    ->whereNotNull('extracted_text')
                    ->where('extracted_text', '!=', '')
                    ->inRandomOrder()
                    ->limit(15)
                    ->get(['page_number', 'extracted_text'])
                    ->sortBy('page_number');

                abort_if($pages->isEmpty(), 422, 'নির্বাচিত বইটিতে কোনো পাঠ্য কনটেন্ট পাওয়া যায়নি।');

                $sourceType = 'paragraph';
                $sourceBookId = null;
                $sourceText = mb_substr($pages->map(fn ($p) => "[Page {$p->page_number}]\n".mb_substr($p->extracted_text, 0, 1200))->implode("\n\n"), 0, 8000);
                $title = $title ?: $myBook->title;
            } else {
                abort_unless($sourceBookId, 422, 'Please select a book.');
                $book = Book::findOrFail($sourceBookId);
                $accessibleIds = $access->accessibleChapterIds($student, $book);
                abort_unless($accessibleIds !== null, 403);

                $chapterIds = array_values(array_unique(array_map('intval', $request->input('chapter_ids', []))));
                abort_if(array_diff($chapterIds, $accessibleIds), 403);
            }
        }

        $session = $student->examSessions()->create([
            'title' => $title,
            'source_type' => $sourceType,
            'source_book_id' => $sourceBookId,
            'source_text' => $sourceText,
            'config' => ['title' => $title, 'type' => $request->type, 'count' => $request->count, 'duration' => $request->duration, 'mode' => $request->mode, 'chapter_ids' => $chapterIds],
            'mode' => $request->mode,
            'status' => 'generating',
            'total' => $request->count,
        ]);

        GenerateExamQuestions::dispatch($session->id);

        return $this->success($session, 'Exam generation started', 201);
    }

    public function status(ExamSession $exam): JsonResponse
    {
        $this->authorizeExam($exam);

        return $this->success([
            'id' => $exam->id,
            'status' => $exam->status,
            'total' => $exam->total,
        ]);
    }

    public function retry(ExamSession $exam): JsonResponse
    {
        $this->authorizeExam($exam);
        abort_unless($exam->status === 'failed', 409, 'Only failed exam generation can be retried.');
        abort_unless(auth('sanctum')->user()->hasActiveAiExamAccess(), 403, 'Your AI exam quota is exhausted.');

        $exam->update(['status' => 'generating', 'questions' => null]);
        GenerateExamQuestions::dispatch($exam->id);

        return $this->success($exam, 'Exam generation retried');
    }

    public function explanation(ExamSession $exam, int $question): JsonResponse
    {
        $this->authorizeExam($exam);
        $student = auth('sanctum')->user();
        abort_unless($student->canUseAiExplain(), 403, 'AI explanation limit reached.');

        $q = collect($exam->questions ?? [])->firstWhere('id', $question);
        abort_unless($q, 404);

        if (! empty($q['explanation'])) {
            return $this->success(['explanation' => $q['explanation']]);
        }

        $hash = hash('sha256', ($q['question'] ?? '').'|'.($q['correct_answer'] ?? ''));
        $cached = AiExplanation::where('question_hash', $hash)->first();
        if ($cached) {
            return $this->success(['explanation' => $cached->explanation]);
        }

        $prompt = "প্রশ্ন: ".($q['question'] ?? '')."\nসঠিক উত্তর: ".($q['correct_answer'] ?? '')."\nসংক্ষেপে বাংলায় ব্যাখ্যা দাও।";
        $explanation = AiProviderFactory::default('question_analysis')->complete([
            ['role' => 'user', 'content' => $prompt],
        ]);

        AiExplanation::updateOrCreate(
            ['question_hash' => $hash],
            ['explanation' => $explanation]
        );

        return $this->success(['explanation' => $explanation]);
    }

    public function pdf(ExamSession $exam): HttpResponse
    {
        $this->authorizeExam($exam);
        $pdf = Pdf::loadView('pdf.exam', ['exam' => $exam, 'student' => auth('sanctum')->user()]);

        return $pdf->download("exam-{$exam->id}.pdf");
    }

    public function show(ExamSession $exam): JsonResponse
    {
        $this->authorizeExam($exam);

        return $this->success($this->sanitized($exam));
    }

    public function complete(SubmitAnswerRequest $request, ExamSession $exam): JsonResponse
    {
        $this->authorizeExam($exam);
        abort_if($exam->status === 'completed', 400);

        $answers = $request->answers;
        $score = 0;
        foreach ($exam->questions as $q) {
            $given = $answers[$q['id']] ?? null;
            if ($given !== null && mb_strtolower(trim($given)) === mb_strtolower(trim($q['correct_answer'] ?? ''))) {
                $score++;
            }
        }

        $exam->update([
            'answers' => $answers,
            'score' => $score,
            'percentage' => $exam->total > 0 ? round(($score / $exam->total) * 100, 2) : 0,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $student = auth('sanctum')->user();

        // Free campaign/coupon access is unlimited — never consume paid
        // quota while a grant is active.
        if (! app(AccessGrantService::class)->hasActiveAccess($student)) {
            $student->increment('ai_trial_minutes_used');
            if ($sub = $student->activeSubscription) {
                $sub->decrement('ai_exam_count_remaining');
            }
        }

        if ($exam->mode === 'exam' && $exam->total >= 10) {
            $now = now();
            LeaderboardEntry::create([
                'student_id' => $student->id, 'exam_session_id' => $exam->id, 'book_id' => $exam->source_book_id,
                'student_type' => $student->type, 'score' => $exam->score, 'total' => $exam->total,
                'percentage' => $exam->percentage, 'questions_count' => $exam->total,
                'week_number' => (int) $now->format('W'), 'month_number' => (int) $now->format('n'), 'year' => (int) $now->format('Y'),
            ]);
        }

        return $this->success($exam, 'Exam completed');
    }

    public function result(ExamSession $exam): JsonResponse
    {
        $this->authorizeExam($exam);
        abort_unless($exam->status === 'completed', 404);

        return $this->success($exam);
    }

    private function sanitized(ExamSession $exam): array
    {
        $data = $exam->toArray();
        if ($exam->mode === 'exam' && $exam->status !== 'completed') {
            $data['questions'] = collect($exam->questions ?? [])->map(function ($q) {
                unset($q['correct_answer'], $q['explanation']);

                return $q;
            })->all();
        }

        return $data;
    }

    private function authorizeExam(ExamSession $exam): void
    {
        abort_unless($exam->student_id === auth('sanctum')->id(), 403);
    }
}
