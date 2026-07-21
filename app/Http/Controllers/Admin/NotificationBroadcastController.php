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

    public function store(BroadcastNotificationRequest $request): RedirectResponse
    {
        ScheduledNotification::create([
            'type' => 'admin_broadcast',
            'title' => $request->title,
            'body' => $request->body,
            'target_audience' => $request->target_audience,
            'scheduled_for' => $request->scheduled_for ?: now(),
        ]);

        return redirect()->route('admin.notifications.index')->with('success', 'Broadcast scheduled — it will go out within 5 minutes.');
    }
}
