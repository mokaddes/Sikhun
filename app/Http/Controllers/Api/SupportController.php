<?php

namespace App\Http\Controllers\Api;

use App\Models\SupportConversation;
use App\Services\SupportBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $request->validate(['message' => 'required|string|max:2000']);
        $conversation = $this->conversation();

        $conversation->messages()->create(['sender_type' => 'student', 'message' => $request->message]);

        if ($conversation->bot_enabled) {
            $this->bot->reply($conversation);
        }

        return $this->success(['messages' => $conversation->messages()->orderBy('id')->get()]);
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
