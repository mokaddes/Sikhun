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
            'token' => ['nullable', 'string', 'max:512'],
            'subscription' => ['nullable'],
        ]);

        $student = auth('web')->user();
        $token = trim((string) ($request->input('token') ?? ''));

        if ($token === '' && $request->filled('subscription')) {
            $sub = $request->input('subscription');
            $token = is_array($sub) ? json_encode($sub) : trim((string) $sub);
        }

        if ($token === '') {
            return response()->json(['success' => false, 'message' => 'FCM token is required.'], 422);
        }

        // Save under channel='fcm' so PushNotificationService dispatches to it.
        // Using (student_id, channel, token) allows a student to receive pushes across multiple devices/browsers.
        $existing = PushSubscription::where('student_id', $student->id)
            ->where('channel', 'fcm')
            ->where('token', $token)
            ->first();

        if ($existing) {
            $existing->touch();
        } else {
            PushSubscription::create([
                'student_id' => $student->id,
                'channel' => 'fcm',
                'token' => $token,
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function unsubscribePush(Request $request): JsonResponse
    {
        $student = auth('web')->user();
        $token = trim((string) $request->input('token', ''));

        $query = PushSubscription::where('student_id', $student->id)->where('channel', 'fcm');
        if ($token !== '') {
            $query->where('token', $token);
        }
        $query->delete();

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
