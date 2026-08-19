<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\CreateExamRequest;
use App\Http\Requests\Student\SubmitAnswerRequest;
use App\Models\ExamSession;
use App\Models\LeaderboardEntry;
use App\Services\BookAccessService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ExamController extends Controller
{
    public function index(): Response
    {
        $student = auth('web')->user();

        return Inertia::render('Student/Exams/Index', [
            'exams' => $student->examSessions()->latest()->paginate(15),
        ]);
    }

    public function createForm(): Response
    {
        $student = auth('web')->user();

        return Inertia::render('Student/Exams/Create', [
            'books' => $student->books()->get(['books.id', 'books.title']),
        ]);
    }

    public function create(CreateExamRequest $request, BookAccessService $access): RedirectResponse
    {
        $student = auth('web')->user();

        if ($request->source_type === 'book') {
            $book = \App\Models\Book::findOrFail($request->source_book_id);
            abort_unless($access->hasAccess($student, $book), 403);
        }

        $session = $student->examSessions()->create([
            'source_type' => $request->source_type,
            'source_book_id' => $request->source_book_id,
            'source_text' => $request->source_text,
            'config' => [
                'type' => $request->type,
                'count' => $request->count,
                'duration' => $request->duration,
                'mode' => $request->mode,
            ],
            'mode' => $request->mode,
            'status' => 'generating',
            'total' => $request->count,
        ]);

        \App\Jobs\GenerateExamQuestions::dispatch($session->id);

        return redirect()->route('exams.show', $session);
    }

    public function show(ExamSession $exam): Response
    {
        $this->authorizeExam($exam);

        return Inertia::render('Student/Exams/Show', [
            'exam' => $this->sanitized($exam),
        ]);
    }

    public function status(ExamSession $exam): JsonResponse
    {
        $this->authorizeExam($exam);

        return response()->json(['status' => $exam->status, 'exam' => $this->sanitized($exam)]);
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
            'time_taken_seconds' => $exam->started_at ? now()->diffInSeconds($exam->started_at) : null,
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

    public function result(ExamSession $exam): Response
    {
        $this->authorizeExam($exam);
        abort_unless($exam->status === 'completed', 404);

        return Inertia::render('Student/Exams/Result', ['exam' => $exam]);
    }

    public function pdf(ExamSession $exam)
    {
        $this->authorizeExam($exam);
        abort_unless($exam->status === 'completed', 404);

        $pdf = Pdf::loadView('pdf.exam-answer-sheet', ['exam' => $exam, 'student' => auth('web')->user()]);

        return $pdf->download("exam-{$exam->id}-answer-sheet.pdf");
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
