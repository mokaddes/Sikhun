<?php

namespace App\Http\Controllers\Api;

use App\Models\AiSession;
use App\Models\Feedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeedbackController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $feedback = Feedback::query()
            ->where('student_id', auth('sanctum')->id())
            ->latest()
            ->paginate(15);

        return $this->success($feedback);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:feedback,answer_report,question_report'],
            'target_type' => ['nullable', 'in:exam_question,flashcard,ai_answer,book'],
            'target_id' => ['nullable', 'integer'],
            'target_key' => ['nullable', 'string', 'max:100'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'message' => ['required_unless:type,answer_report', 'nullable', 'string', 'max:5000'],
        ]);

        $studentId = auth('sanctum')->id();
        $context = null;

        if ($data['type'] === 'answer_report') {
            abort_unless(($data['target_type'] ?? null) === 'ai_answer', 422);
            abort_unless(isset($data['target_id'], $data['target_key']) && ctype_digit((string) $data['target_key']), 422);

            $session = AiSession::query()
                ->whereKey($data['target_id'])
                ->where('student_id', $studentId)
                ->firstOrFail();
            $messages = $session->messages ?? [];
            $answer = $messages[(int) $data['target_key']] ?? null;
            abort_unless(is_array($answer) && ($answer['role'] ?? null) === 'assistant', 422);

            $context = [
                'ai_session_id' => $session->id,
                'answer_index' => (int) $data['target_key'],
                'answer' => is_string($answer['content'] ?? null)
                    ? $answer['content']
                    : json_encode($answer['content'] ?? null, JSON_UNESCAPED_UNICODE),
            ];
        }

        $feedback = Feedback::create([
            'student_id' => $studentId,
            'type' => $data['type'],
            'target_type' => $data['target_type'] ?? null,
            'target_id' => $data['target_id'] ?? null,
            'target_key' => $data['target_key'] ?? null,
            'rating' => $data['rating'] ?? null,
            'message' => $data['message'] ?? null,
            'context' => $context,
            'status' => 'new',
        ]);

        return $this->success($feedback, 'Feedback submitted successfully', 201);
    }
}
