<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\PushSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function subscribePush(Request $request): JsonResponse
    {
        $request->validate([
            'subscription' => 'required',
        ]);

        $student = auth('web')->user();
        $token = is_array($request->subscription) ? json_encode($request->subscription) : (string) $request->subscription;

        PushSubscription::updateOrCreate(
            ['student_id' => $student->id, 'channel' => 'web'],
            ['token' => $token]
        );

        return response()->json(['success' => true]);
    }

    public function index(): JsonResponse
    {
        $student = auth('web')->user();

        return response()->json([
            'data' => $student->notifications()->latest()->limit(30)->get(),
            'unread_count' => $student->notifications()->whereNull('read_at')->count(),
        ]);
    }

    public function markRead(Notification $notification): JsonResponse
    {
        abort_unless($notification->student_id === auth('web')->id(), 403);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        auth('web')->user()->notifications()->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function destroy(Notification $notification): JsonResponse
    {
        abort_unless((int) $notification->student_id === (int) auth('web')->id(), 404);
        $notification->delete();

        return response()->json(['success' => true]);
    }

    public function destroyAll(): JsonResponse
    {
        auth('web')->user()->notifications()->delete();

        return response()->json(['success' => true]);
    }
}
