<?php

namespace App\Http\Controllers\Api;

use App\Models\SupportConversation;
use App\Services\Ai\AiProviderFactory;
use App\Services\SiteSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportController extends BaseApiController
{
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
            try {
                $provider = AiProviderFactory::default('support_bot');
                $systemPrompt = app(SiteSettingService::class)->get(
                    'support_bot_system_prompt',
                    'You are a friendly support assistant for Sikhun.com. Answer briefly and helpfully.'
                );

                $history = $conversation->messages()->orderBy('id')->get()
                    ->map(fn ($m) => ['role' => $m->sender_type === 'student' ? 'user' : 'assistant', 'content' => $m->message]);

                $reply = $provider->chat(array_merge([['role' => 'system', 'content' => $systemPrompt]], $history->all()), ['max_tokens' => 400]);
                $conversation->messages()->create(['sender_type' => 'bot', 'message' => $reply]);
            } catch (\Throwable $e) {
                $conversation->messages()->create(['sender_type' => 'bot', 'message' => 'Sorry, having trouble right now — a team member will follow up.']);
            }
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
