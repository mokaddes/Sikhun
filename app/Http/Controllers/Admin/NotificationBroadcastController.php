<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BroadcastNotificationRequest;
use App\Models\ScheduledNotification;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class NotificationBroadcastController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Notifications/Index', [
            'scheduled' => ScheduledNotification::latest()->paginate(20),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Notifications/Create');
    }

    public function store(BroadcastNotificationRequest $request, \App\Services\NotificationService $notifications): RedirectResponse
    {
        $type = $request->input('type', 'admin_broadcast');
        $actionUrl = trim((string) $request->input('action_url', '')) ?: null;
        $sendEmail = (bool) $request->boolean('send_email');
        $sendNow = (bool) $request->boolean('send_now') || ! $request->filled('scheduled_for');

        $scheduled = ScheduledNotification::create([
            'type' => $type,
            'title' => $request->title,
            'body' => $request->body,
            'action_url' => $actionUrl,
            'send_email' => $sendEmail,
            'target_audience' => $request->target_audience,
            'scheduled_for' => $request->scheduled_for ?: now(),
        ]);

        if ($sendNow) {
            $sent = $notifications->sendToAudience(
                audience: $scheduled->target_audience,
                type: $scheduled->type,
                title: $scheduled->title,
                body: $scheduled->body,
                data: $actionUrl ? ['url' => $actionUrl] : [],
                mandatory: true,
                sendEmail: $sendEmail
            );
            $scheduled->update(['sent_at' => now()]);

            return redirect()->route('admin.notifications.index')->with('success', "Broadcast sent immediately to {$sent} student(s) (In-app + Push".($sendEmail ? ' + Email' : '').').');
        }

        return redirect()->route('admin.notifications.index')->with('success', 'Broadcast scheduled — it will go out at the scheduled time.');
    }
}
