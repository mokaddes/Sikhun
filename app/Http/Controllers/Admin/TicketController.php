<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    public function index(Request $request): Response
    {
        $query = SupportTicket::query()
            ->with(['student:id,name,email', 'assignedAdmin:id,name'])
            ->with(['messages' => fn ($messages) => $messages->latest()->limit(1)])
            ->orderByRaw("CASE WHEN status IN ('open', 'pending') AND sla_due_at < ? THEN 0 ELSE 1 END", [now()])
            ->orderBy('sla_due_at')
            ->latest('updated_at');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($tickets) use ($search) {
                $tickets->where('subject', 'like', "%{$search}%")
                    ->orWhereHas('student', fn ($student) => $student
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        return Inertia::render('Admin/Tickets/Index', [
            'tickets' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only('status', 'priority', 'search'),
        ]);
    }

    public function show(SupportTicket $ticket): Response
    {
        $ticket->load(['student:id,name,email', 'assignedAdmin:id,name', 'messages', 'conversation:id,status']);

        return Inertia::render('Admin/Tickets/Show', [
            'ticket' => $ticket,
            'admins' => \App\Models\Admin::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate(['message' => ['required', 'string', 'max:5000']]);
        $ticket->messages()->create([
            'sender_type' => 'admin',
            'body' => $data['message'],
        ]);
        if (in_array($ticket->status, ['open', 'pending'], true)) {
            $ticket->update(['status' => 'pending']);
        }

        return back()->with('success', app()->getLocale() === 'bn' ? 'উত্তর পাঠানো হয়েছে।' : 'Reply sent.');
    }

    public function update(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:open,pending,resolved,closed'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'assigned_admin_id' => ['nullable', 'integer', 'exists:admins,id'],
        ]);

        $ticket->update($data);

        return back()->with('success', app()->getLocale() === 'bn' ? 'টিকিট আপডেট হয়েছে।' : 'Ticket updated.');
    }
}
