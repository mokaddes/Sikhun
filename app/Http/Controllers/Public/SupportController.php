<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SupportConversation;
use App\Services\Ai\AiProviderFactory;
use App\Services\SiteSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Powers the floating SupportWidget on every page (see PublicLayout /
 * StudentLayout). Works for guests (tracked via a session-stored token —
 * no login required) and logged-in students (tracked via student_id)
 * transparently, so the same widget/endpoint serves both.
 */
class SupportController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $conversation = $this->findOrCreateConversation($request);

        return response()->json([
            'conversation_id' => $conversation->id,
            'messages' => $conversation->messages()->orderBy('id')->get(),
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $conversation = $this->findOrCreateConversation($request);

        $conversation->messages()->create([
            'sender_type' => 'student',
            'message' => $request->message,
        ]);

        if ($conversation->bot_enabled) {
            try {
                $provider = AiProviderFactory::default('support_bot');
                $systemPrompt = app(SiteSettingService::class)->get(
                    'support_bot_system_prompt',
                    'You are a friendly support assistant for Sikhun.com, a Bangladeshi AI education platform. Answer briefly and helpfully in the language the user writes in. If you cannot help, say a human will follow up soon.'
                );

                $history = $conversation->messages()->orderBy('id')->get()
                    ->map(fn ($m) => ['role' => $m->sender_type === 'student' ? 'user' : 'assistant', 'content' => $m->message]);

                $reply = $provider->chat(array_merge(
                    [['role' => 'system', 'content' => $systemPrompt]],
                    $history->all()
                ), ['max_tokens' => 400]);

                $conversation->messages()->create(['sender_type' => 'bot', 'message' => $reply]);
            } catch (\Throwable $e) {
                $conversation->messages()->create([
                    'sender_type' => 'bot',
                    'message' => 'Sorry, I\'m having trouble right now — a team member will follow up soon.',
                ]);
            }
        }

        return response()->json(['messages' => $conversation->messages()->orderBy('id')->get()]);
    }

    private function findOrCreateConversation(Request $request): SupportConversation
    {
        $student = auth('web')->user();

        if ($student) {
            return SupportConversation::firstOrCreate(
                ['student_id' => $student->id, 'status' => 'open'],
                ['bot_enabled' => true]
            );
        }

        $guestToken = $request->session()->get('support_guest_token');

        if (! $guestToken) {
            $guestToken = Str::random(40);
            $request->session()->put('support_guest_token', $guestToken);
        }

        return SupportConversation::firstOrCreate(
            ['guest_token' => $guestToken, 'status' => 'open'],
            ['bot_enabled' => true]
        );
    }
}
