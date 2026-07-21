<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $student = auth('sanctum')->user();

        return $this->success([
            'items' => $student->notifications()->latest()->paginate(20),
            'unread_count' => $student->notifications()->whereNull('read_at')->count(),
        ]);
    }

    public function markRead(Notification $notification): JsonResponse
    {
        abort_unless($notification->student_id === auth('sanctum')->id(), 403);
        $notification->markAsRead();

        return $this->success(null, 'Marked as read');
    }

    public function markAllRead(): JsonResponse
    {
        auth('sanctum')->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return $this->success(null, 'All marked as read');
    }
}
