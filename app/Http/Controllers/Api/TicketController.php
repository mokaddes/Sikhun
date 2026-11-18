<?php

namespace App\Http\Controllers\Api;

use App\Models\AiSession;
use App\Models\Feedback;
use App\Models\SupportConversation;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TicketController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $tickets = SupportTicket::query()
            ->where('student_id', auth('sanctum')->id())
            ->with(['messages' => fn ($query) => $query->latest()->limit(1)])
            ->latest('updated_at')
            ->paginate(15);

        return $this->success($tickets);
    }

    public function show(SupportTicket $ticket): JsonResponse
    {
        abort_unless((int) $ticket->student_id === (int) auth('sanctum')->id(), 404);
        $ticket->load(['messages', 'conversation:id,status']);

        return $this->success($ticket);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:160'],
            'category' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:5000'],
            'conversation_id' => ['nullable', 'integer'],
        ]);

        $studentId = (int) auth('sanctum')->id();
        $conversationId = null;
        if (! empty($data['conversation_id'])) {
            $conversationId = SupportConversation::query()
                ->whereKey($data['conversation_id'])
                ->where('student_id', $studentId)
                ->value('id');
            abort_unless($conversationId, 404);
        }

        $ticket = DB::transaction(function () use ($data, $studentId, $conversationId) {
            $ticket = SupportTicket::create([
                'student_id' => $studentId,
                'conversation_id' => $conversationId,
                'subject' => $data['subject'],
                'category' => $data['category'] ?? null,
                'priority' => 'normal',
                'status' => 'open',
                'sla_due_at' => Carbon::now()->addHours(48),
            ]);
            $ticket->messages()->create([
                'sender_type' => 'student',
                'body' => $data['message'],
            ]);

            return $ticket;
        });

        return $this->success($ticket->load('messages'), 'Ticket created.', 201);
    }

    public function reply(Request $request, SupportTicket $ticket): JsonResponse
    {
        abort_unless((int) $ticket->student_id === (int) auth('sanctum')->id(), 404);
        abort_unless(in_array($ticket->status, ['open', 'pending'], true), 422);

        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);
        $msg = $ticket->messages()->create([
            'sender_type' => 'student',
            'body' => $data['message'],
        ]);
        if ($ticket->status === 'pending') {
            $ticket->update(['status' => 'open']);
        }

        return $this->success($msg, 'Reply sent.');
    }

    public function feedbackIndex(): JsonResponse
    {
        $feedback = Feedback::query()
            ->where('student_id', auth('sanctum')->id())
            ->latest()
            ->paginate(15);

        return $this->success($feedback);
    }

    public function feedbackStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:feedback,answer_report,question_report'],
            'target_type' => ['nullable', 'in:exam_question,flashcard,ai_answer,book'],
            'target_id' => ['nullable', 'integer'],
            'target_key' => ['nullable', 'string', 'max:100'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'message' => ['required_unless:type,answer_report', 'nullable', 'string', 'max:5000'],
        ]);

        $studentId = (int) auth('sanctum')->id();
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

        return $this->success($feedback, 'Feedback submitted', 201);
    }
}
