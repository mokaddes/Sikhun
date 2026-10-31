<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\SupportConversation;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    public function index(Request $request): Response
    {
        $tickets = SupportTicket::query()
            ->where('student_id', $request->user('web')->getKey())
            ->with(['messages' => fn ($query) => $query->latest()->limit(1)])
            ->latest('updated_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Student/Tickets/Index', ['tickets' => $tickets]);
    }

    public function show(Request $request, SupportTicket $ticket): Response
    {
        $this->authorizeOwner($request, $ticket);
        $ticket->load(['messages', 'conversation:id,status']);

        return Inertia::render('Student/Tickets/Show', ['ticket' => $ticket]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:160'],
            'category' => ['nullable', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:5000'],
            'conversation_id' => ['nullable', 'integer'],
        ]);

        $studentId = $request->user('web')->getKey();
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

        return redirect('/support/tickets/'.$ticket->id)
            ->with('success', app()->getLocale() === 'bn' ? 'টিকিট তৈরি হয়েছে।' : 'Ticket created.');
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $this->authorizeOwner($request, $ticket);
        abort_unless(in_array($ticket->status, ['open', 'pending'], true), 422);

        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);
        $ticket->messages()->create([
            'sender_type' => 'student',
            'body' => $data['message'],
        ]);
        if ($ticket->status === 'pending') {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', app()->getLocale() === 'bn' ? 'উত্তর পাঠানো হয়েছে।' : 'Reply sent.');
    }

    private function authorizeOwner(Request $request, SupportTicket $ticket): void
    {
        abort_unless((int) $ticket->student_id === (int) $request->user('web')->getKey(), 404);
    }
}
