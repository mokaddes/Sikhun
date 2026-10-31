<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SupportConversation;
use App\Models\SupportTicket;
use App\Services\SupportBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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

    public function handoff(Request $request): JsonResponse
    {
        $student = $request->user('web');

        if (! $student) {
            return response()->json([
                'message' => 'Please log in to request help from our support team.',
                'login_url' => route('login'),
            ], 401);
        }

        $data = $request->validate(['conversation_id' => ['required', 'integer']]);

        $ticket = DB::transaction(function () use ($data, $student) {
            $conversation = SupportConversation::query()
                ->whereKey($data['conversation_id'])
                ->where('student_id', $student->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $existingTicket = SupportTicket::query()
                ->where('conversation_id', $conversation->getKey())
                ->where('student_id', $student->getKey())
                ->first();

            if ($existingTicket) {
                $conversation->update(['bot_enabled' => false]);

                return $existingTicket;
            }

            $messages = $conversation->messages()->orderBy('id')->get();
            abort_if($messages->isEmpty(), 422, 'Send a message before requesting a human handoff.');

            $latestStudentMessage = $messages->last(
                fn ($message) => $message->sender_type === 'student'
                    && trim((string) $message->message) !== ''
            );
            abort_unless($latestStudentMessage, 422, 'A student message is required for handoff.');

            $ticket = SupportTicket::create([
                'student_id' => $student->getKey(),
                'conversation_id' => $conversation->getKey(),
                'subject' => Str::limit(trim($latestStudentMessage->message), 160),
                'priority' => 'normal',
                'status' => 'open',
                'sla_due_at' => Carbon::now()->addHours(48),
            ]);

            foreach ($messages as $message) {
                $ticket->messages()->create([
                    'sender_type' => $message->sender_type === 'student' ? 'student' : 'bot',
                    'body' => $message->message,
                ]);
            }

            $conversation->update(['bot_enabled' => false]);

            return $ticket;
        });

        return response()->json([
            'ticket_id' => $ticket->getKey(),
            'redirect_url' => url('/support/tickets/'.$ticket->getKey()),
        ]);
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
