<?php

namespace App\Http\Controllers\Api;

use App\Models\SupportConversation;
use App\Models\SupportTicket;
use App\Services\SupportBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SupportController extends BaseApiController
{
    public function __construct(private SupportBotService $bot) {}

    public function show(): JsonResponse
    {
        $conversation = $this->conversation();

        return $this->success(['conversation_id' => $conversation->id, 'messages' => $conversation->messages()->orderBy('id')->get()]);
    }

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'locale' => ['nullable', 'in:bn,en'],
        ]);
        $conversation = $this->conversation();

        $conversation->messages()->create(['sender_type' => 'student', 'message' => $data['message']]);

        if ($conversation->bot_enabled) {
            $this->bot->reply($conversation, $data['locale'] ?? app()->getLocale());
        }

        return $this->success(['messages' => $conversation->messages()->orderBy('id')->get()]);
    }

    public function handoff(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $student = auth('sanctum')->user();
        $conversation = $this->conversation();

        $lastStudentMessage = $conversation->messages()
            ->where('sender_type', 'student')
            ->latest('id')
            ->value('message');

        $body = trim((string) ($data['message'] ?? $lastStudentMessage ?? 'Student requested human support handoff.'));
        $subject = trim((string) ($data['subject'] ?? '')) ?: mb_substr($body, 0, 80);

        $ticket = DB::transaction(function () use ($student, $conversation, $subject, $body) {
            $ticket = SupportTicket::create([
                'student_id' => $student->id,
                'conversation_id' => $conversation->id,
                'subject' => $subject,
                'category' => 'bot_handoff',
                'priority' => 'normal',
                'status' => 'open',
                'sla_due_at' => Carbon::now()->addHours(48),
            ]);

            $ticket->messages()->create([
                'sender_type' => 'student',
                'body' => $body,
            ]);

            return $ticket;
        });

        return $this->success($ticket, 'Support ticket created', 201);
    }

    private function conversation(): SupportConversation
    {
        $student = auth('sanctum')->user();

        return SupportConversation::firstOrCreate(
            ['student_id' => $student->id, 'status' => 'open'],
            ['bot_enabled' => true]
        );
    }
}
