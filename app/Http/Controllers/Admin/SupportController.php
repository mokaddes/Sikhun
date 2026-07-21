<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportConversation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupportController extends Controller
{
    public function index(Request $request): Response
    {
        $conversations = SupportConversation::with(['student:id,name,email', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return Inertia::render('Admin/Support/Index', [
            'conversations' => $conversations,
            'filters' => $request->only('status'),
        ]);
    }

    public function show(SupportConversation $conversation): Response
    {
        return Inertia::render('Admin/Support/Show', [
            'conversation' => $conversation->load(['student:id,name,email', 'messages']),
        ]);
    }

    public function reply(Request $request, SupportConversation $conversation): RedirectResponse
    {
        $request->validate(['message' => 'required|string|max:2000']);

        $conversation->messages()->create(['sender_type' => 'admin', 'message' => $request->message]);

        return back()->with('success', 'Reply sent.');
    }

    public function toggleBot(SupportConversation $conversation): RedirectResponse
    {
        $conversation->update(['bot_enabled' => ! $conversation->bot_enabled]);

        return back()->with('success', 'Bot status updated.');
    }

    public function close(SupportConversation $conversation): RedirectResponse
    {
        $conversation->update(['status' => 'resolved']);

        return back()->with('success', 'Conversation closed.');
    }
}
