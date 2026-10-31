<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\CreateExamRequest;
use App\Http\Requests\Student\SubmitAnswerRequest;
use App\Jobs\GenerateExamQuestions;
use App\Models\AiExplanation;
use App\Models\BookChapter;
use App\Models\BookChunk;
use App\Models\ExamSession;
use App\Models\LeaderboardEntry;
use App\Services\Ai\AiProviderFactory;
use App\Services\BookAccessService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExamController extends Controller
{
    public function index(\Illuminate\Http\Request $request): Response
    {
        $student = auth('web')->user();

        $query = $student->examSessions()->with('book:id,title');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('source_text', 'like', "%{$search}%");
            });
        }

        if ($status = $request->query('status')) {
            if (in_array($status, ['completed', 'in_progress', 'generating', 'failed'])) {
                $query->where('status', $status);
            } elseif (in_array($status, ['practice', 'exam'])) {
                $query->where('mode', $status);
            }
        }

        $stats = [
            'total' => $student->examSessions()->count(),
            'completed' => $student->examSessions()->where('status', 'completed')->count(),
            'avg_score' => round((float) ($student->examSessions()->where('status', 'completed')->avg('percentage') ?? 0), 1),
            'highest_score' => round((float) ($student->examSessions()->where('status', 'completed')->max('percentage') ?? 0), 1),
        ];

        return Inertia::render('Student/Exams/Index', [
            'exams' => $query->latest()->paginate(12)->withQueryString(),
            'stats' => $stats,
            'filters' => [
                'search' => $request->query('search', ''),
                'status' => $request->query('status', 'all'),
            ],
        ]);
    }

    public function createForm(BookAccessService $access): Response
    {
        $student = auth('web')->user();
        $books = \App\Models\Book::query()
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

        return Inertia::render('Student/Exams/Create', ['books' => $books]);
    }

    public function create(CreateExamRequest $request, BookAccessService $access): RedirectResponse
    {
        $student = auth('web')->user();
        abort_unless($student->hasActiveAiExamAccess(), 403, 'Your AI exam quota is exhausted.');

        $chapterIds = [];
        if ($request->source_type === 'book') {
            $book = \App\Models\Book::findOrFail($request->source_book_id);
            $accessibleIds = $access->accessibleChapterIds($student, $book);
            abort_unless($accessibleIds !== null, 403);

            $chapterIds = array_values(array_unique(array_map('intval', $request->input('chapter_ids', []))));
            abort_if(array_diff($chapterIds, $accessibleIds), 403);
        }

        $session = $student->examSessions()->create([
            'title' => $request->title ?: null,
            'source_type' => $request->source_type,
            'source_book_id' => $request->source_book_id,
            'source_text' => $request->source_text,
            'config' => [
                'title' => $request->title ?: null,
                'type' => $request->type,
                'count' => $request->count,
                'duration' => $request->duration,
                'mode' => $request->mode,
                'chapter_ids' => $chapterIds,
            ],
            'mode' => $request->mode,
            'status' => 'generating',
            'total' => $request->count,
        ]);

        GenerateExamQuestions::dispatch($session->id);

        return redirect()->route('exams.show', $session);
    }

    public function retry(ExamSession $exam): RedirectResponse
    {
        $this->authorizeExam($exam);
        abort_unless($exam->status === 'failed', 409, 'Only failed exam generation can be retried.');
        abort_unless(auth('web')->user()->hasActiveAiExamAccess(), 403, 'Your AI exam quota is exhausted.');

        $claimed = ExamSession::query()
            ->whereKey($exam->id)
            ->where('status', 'failed')
            ->update([
                'status' => 'generating',
                'questions' => null,
                'answers' => null,
                'score' => 0,
                'percentage' => 0,
                'started_at' => null,
                'completed_at' => null,
                'time_taken_seconds' => null,
                'total' => (int) ($exam->config['count'] ?? 0),
            ]);
        abort_unless($claimed === 1, 409, 'This exam generation has already been retried.');

        try {
            GenerateExamQuestions::dispatch($exam->id);
        } catch (\Throwable $exception) {
            $exam->update(['status' => 'failed']);
            throw $exception;
        }

        return redirect()->route('exams.show', $exam);
    }

    public function show(ExamSession $exam, BookAccessService $access): Response
    {
        $this->authorizeExam($exam);
        $data = $this->sanitized($exam);
        $data['questions'] = $this->withBookReferences($exam, $data['questions'] ?? [], $access);

        return Inertia::render('Student/Exams/Show', [
            'exam' => $data,
            'canExplain' => auth('web')->user()->canUseAiExplain(),
        ]);
    }

    public function status(ExamSession $exam, BookAccessService $access): JsonResponse
    {
        $this->authorizeExam($exam);
        $data = $this->sanitized($exam);
        $data['questions'] = $this->withBookReferences($exam, $data['questions'] ?? [], $access);

        return response()->json(['status' => $exam->status, 'exam' => $data]);
    }

    public function complete(SubmitAnswerRequest $request, ExamSession $exam): RedirectResponse
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
            'time_taken_seconds' => $exam->started_at ? max(0, (int) now()->diffInSeconds($exam->started_at)) : null,
        ]);

        $student = auth('web')->user();

        // Free campaign/coupon access is unlimited — never consume paid
        // quota while a grant is active.
        if (! app(\App\Services\AccessGrantService::class)->hasActiveAccess($student)) {
            $student->increment('ai_trial_minutes_used');
            if ($sub = $student->activeSubscription) {
                $sub->decrement('ai_exam_count_remaining');
            }
        }

        if ($exam->mode === 'exam' && $exam->total >= 10) {
            $now = now();
            LeaderboardEntry::create([
                'student_id' => $student->id,
                'exam_session_id' => $exam->id,
                'subject' => $exam->book?->subject,
                'book_id' => $exam->source_book_id,
                'student_type' => $student->type,
                'score' => $exam->score,
                'total' => $exam->total,
                'percentage' => $exam->percentage,
                'questions_count' => $exam->total,
                'week_number' => (int) $now->format('W'),
                'month_number' => (int) $now->format('n'),
                'year' => (int) $now->format('Y'),
            ]);
        }

        return redirect()->route('exams.result', $exam);
    }

    public function result(ExamSession $exam, BookAccessService $access): Response
    {
        $this->authorizeExam($exam);
        abort_unless($exam->status === 'completed', 404);

        $questions = $this->withBookReferences($exam, $exam->questions ?? [], $access);

        return Inertia::render('Student/Exams/Result', [
            'exam' => array_merge($exam->toArray(), ['questions' => $questions]),
            'canExplain' => auth('web')->user()->canUseAiExplain(),
        ]);
    }

    public function explanation(Request $request, ExamSession $exam, int $question, BookAccessService $access): StreamedResponse
    {
        $this->authorizeExam($exam);
        $isPracticeInProgress = $exam->status === 'in_progress' && $exam->mode === 'practice';
        abort_unless($exam->status === 'completed' || $isPracticeInProgress, 404);

        $validated = $request->validate([
            'student_answer' => ['nullable', 'string', 'max:4000'],
        ]);

        $student = auth('web')->user();
        abort_unless($student->canUseAiExplain(), 403, 'AI explanations require a paid plan.');

        $item = collect($exam->questions ?? [])->first(
            fn (array $item) => (string) ($item['id'] ?? '') === (string) $question
        );
        abort_unless($item, 404);

        $studentAnswer = $isPracticeInProgress
            ? ($validated['student_answer'] ?? null)
            : ($exam->answers[$question] ?? $exam->answers[(string) $question] ?? null);
        $correctAnswer = (string) ($item['correct_answer'] ?? '');
        $sourceContext = trim((string) $exam->source_text);

        if ($exam->source_type === 'book') {
            $sourceContext = '';
            $chunkId = filter_var($item['book_ref']['chunk_id'] ?? null, FILTER_VALIDATE_INT);
            $chunk = $chunkId !== false
                ? BookChunk::query()->where('book_id', $exam->source_book_id)->find($chunkId)
                : null;

            if ($chunk) {
                if ($chunk->page_number) {
                    abort_unless($access->canAccessPage($student, $exam->book, (int) $chunk->page_number), 403);
                } elseif ($chunk->chapter_id) {
                    $chapter = BookChapter::query()
                        ->where('book_id', $exam->source_book_id)
                        ->find($chunk->chapter_id);
                    abort_unless($chapter && $access->canAccessChapter($student, $chapter), 403);
                }

                $chapterName = $chunk->chapter?->title;
                $sourceContext = '[Book: '.($exam->book?->title ?? 'Source book')
                    .($chapterName ? ' | Chapter: '.$chapterName : '')
                    .($chunk->page_number ? ' | Page: '.$chunk->page_number : '')
                    ."]\n".mb_substr(trim((string) $chunk->content), 0, 6000);
            }
        } else {
            $sourceContext = mb_substr($sourceContext, 0, 6000);
        }

        $sourceHash = hash('sha256', $sourceContext);
        $cacheKey = hash('sha256', json_encode([
            'question' => (string) ($item['question'] ?? ''),
            'correct_answer' => $correctAnswer,
            'student_answer' => $studentAnswer,
            'source_hash' => $sourceHash,
        ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        $cached = AiExplanation::query()
            ->where('cache_key', $cacheKey)
            ->where('created_at', '>=', now()->subDay())
            ->value('explanation');

        return response()->stream(function () use ($cached, $cacheKey, $sourceHash, $item, $correctAnswer, $studentAnswer, $sourceContext, $student) {
            $send = static function (array $event): void {
                echo 'data: '.json_encode($event, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n\n";
                if (ob_get_level() > 0) {
                    @ob_flush();
                }
                flush();
            };

            if ($cached !== null) {
                $send(['content' => $cached, 'done' => true]);

                return;
            }

            $answerLabel = $studentAnswer === null || $studentAnswer === ''
                ? 'No answer provided'
                : (string) $studentAnswer;
            $messages = [
                ['role' => 'system', 'content' => 'You are a clear, supportive exam tutor. Explain why the correct answer is right, assess the student answer as correct or incorrect, and address a likely misconception when useful. Use the supplied source as evidence and do not invent citations. Reply in the language used by the question.'],
                ['role' => 'user', 'content' => "Question:\n".($item['question'] ?? '')."\n\nStudent answer:\n{$answerLabel}\n\nCorrect answer:\n{$correctAnswer}\n\nSource material:\n".($sourceContext !== '' ? $sourceContext : 'No source excerpt is available.')],
            ];

            $explanation = '';
            try {
                $provider = AiProviderFactory::default('exam_gen', [
                    'student' => $student,
                    'student_id' => $student->id,
                ]);

                foreach ($provider->stream($messages, ['temperature' => 0.3, 'max_tokens' => 800]) as $chunk) {
                    $explanation .= $chunk;
                    $send(['content' => $chunk, 'done' => false]);
                }

                if (trim($explanation) === '') {
                    $send(['error' => 'AI explanation is temporarily unavailable.', 'done' => true]);

                    return;
                }

                AiExplanation::query()->updateOrCreate(
                    ['cache_key' => $cacheKey],
                    [
                        'question' => (string) ($item['question'] ?? ''),
                        'correct_answer' => $correctAnswer,
                        'student_answer' => $studentAnswer,
                        'source_hash' => $sourceHash,
                        'explanation' => $explanation,
                        'created_at' => now(),
                    ]
                );
                $send(['content' => '', 'done' => true]);
            } catch (\Throwable $exception) {
                report($exception);
                $send(['error' => 'AI explanation is temporarily unavailable.', 'done' => true]);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function pdf(ExamSession $exam)
    {
        $this->authorizeExam($exam);
        abort_unless($exam->status === 'completed', 404);

        $pdf = Pdf::loadView('pdf.exam-answer-sheet', ['exam' => $exam, 'student' => auth('web')->user()]);

        return $pdf->download("exam-{$exam->id}-answer-sheet.pdf");
    }

    private function withBookReferences(ExamSession $exam, array $questions, BookAccessService $access): array
    {
        $book = $exam->book;

        return collect($questions)->map(function (array $question) use ($book, $access) {
            $bookRef = $question['book_ref'] ?? null;
            $chunkId = filter_var(is_array($bookRef) ? ($bookRef['chunk_id'] ?? null) : null, FILTER_VALIDATE_INT);

            if (! $book || ! is_array($bookRef) || $chunkId === false) {
                return $question;
            }

            $chunk = $book->chunks()->with('chapter')->find($chunkId);
            if (! $chunk) {
                return $question;
            }

            $chapter = $chunk->chapter;
            $page = $chunk->page_number;
            $question['book_ref'] = [
                'chunk_id' => $chunk->id,
                'chapter_id' => $chunk->chapter_id,
                'book' => $book->title,
                'chapter' => $chapter?->title ?? ($bookRef['chapter'] ?? null),
                'page' => $page,
            ];

            if ($page && $access->canAccessPage(auth('web')->user(), $book, (int) $page)) {
                $question['book_ref']['reader_url'] = route('reader.show', [
                    'book' => $book->id,
                    'page' => $page,
                ]);
            } elseif (! $page && $chapter && $chapter->start_page
                && $access->canAccessChapter(auth('web')->user(), $chapter)) {
                $question['book_ref']['page'] = $chapter->start_page;
                $question['book_ref']['reader_url'] = route('reader.show', [
                    'book' => $book->id,
                    'page' => $chapter->start_page,
                ]);
            }

            return $question;
        })->all();
    }

    /**
     * Strip answer keys before the exam is completed — practice mode
     * reveals per-question via a separate mechanism client-side; exam mode
     * must never let a student inspect network responses for the key.
     */
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
        abort_unless($exam->student_id === Auth::guard('web')->id(), 403);
    }
}
