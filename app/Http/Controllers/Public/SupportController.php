<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SupportConversation;
use App\Services\SupportBotService;
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
    public function __construct(private SupportBotService $bot) {}

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
            $this->bot->reply($conversation);
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
