<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\CreateExamRequest;
use App\Http\Requests\Student\SubmitAnswerRequest;
use App\Jobs\GenerateExamQuestions;
use App\Models\Book;
use App\Models\ExamSession;
use App\Models\LeaderboardEntry;
use App\Services\BookAccessService;
use Illuminate\Http\JsonResponse;

class ExamController extends BaseApiController
{
    public function index(): JsonResponse
    {
        return $this->success(auth('sanctum')->user()->examSessions()->latest()->paginate(15));
    }

    public function store(CreateExamRequest $request, BookAccessService $access): JsonResponse
    {
        $student = auth('sanctum')->user();
        abort_unless($student->hasActiveAiAccess(), 403, 'AI trial exhausted.');

        if ($request->source_type === 'book') {
            abort_unless($access->hasAccess($student, Book::findOrFail($request->source_book_id)), 403);
        }

        $session = $student->examSessions()->create([
            'source_type' => $request->source_type,
            'source_book_id' => $request->source_book_id,
            'source_text' => $request->source_text,
            'config' => ['type' => $request->type, 'count' => $request->count, 'duration' => $request->duration, 'mode' => $request->mode],
            'mode' => $request->mode,
            'status' => 'generating',
            'total' => $request->count,
        ]);

        GenerateExamQuestions::dispatch($session->id);

        return $this->success($session, 'Exam generation started', 201);
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
        $student->increment('ai_trial_minutes_used');

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
