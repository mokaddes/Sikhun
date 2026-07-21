<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

/**
 * Broadcast on the student's own private channel — see routes/channels.php
 * for the auth check. Frontend listens via Laravel Echo (resources/js/echo.js),
 * configured for Reverb (a self-hosted, Pusher-protocol-compatible WebSocket
 * server — run it with `php artisan reverb:start`).
 */
class NewNotificationBroadcast implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public function __construct(public Notification $notification) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('student.'.$this->notification->student_id)];
    }

    public function broadcastAs(): string
    {
        return 'notification.new';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'type' => $this->notification->type,
            'title' => $this->notification->title,
            'body' => $this->notification->body,
            'created_at' => $this->notification->created_at->toIso8601String(),
        ];
    }
}
