<?php

namespace App\Http\Controllers\Api;

use App\Models\PushSubscription;
use App\Models\StudentNotificationPreference;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends BaseApiController
{
    public function show(): JsonResponse
    {
        return $this->success(auth('sanctum')->user());
    }

    public function update(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => 'sometimes|string|max:255']);
        $student = auth('sanctum')->user();
        $student->update($data);

        return $this->success($student);
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate(['current_password' => 'required', 'password' => 'required|confirmed|min:8']);
        $student = auth('sanctum')->user();

        if (! Hash::check($request->current_password, $student->password)) {
            return $this->error('Current password is incorrect.', [], 422);
        }

        $student->update(['password' => $request->password]);

        return $this->success(null, 'Password updated');
    }

    public function updateTheme(Request $request): JsonResponse
    {
        $request->validate(['theme_mode' => 'required|in:light,dark,system']);
        auth('sanctum')->user()->update(['theme_mode' => $request->theme_mode]);

        return $this->success(null, 'Theme updated');
    }

    public function updateNotifications(Request $request): JsonResponse
    {
        $data = $request->validate([
            'preferences' => 'required|array',
            'preferences.*.type' => 'required|string',
            'preferences.*.is_enabled' => 'required|boolean',
            'preferences.*.preferred_time' => 'nullable|string',
        ]);

        $student = auth('sanctum')->user();
        foreach ($data['preferences'] as $pref) {
            StudentNotificationPreference::updateOrCreate(
                ['student_id' => $student->id, 'type' => $pref['type']],
                ['is_enabled' => $pref['is_enabled'], 'preferred_time' => $pref['preferred_time'] ?? '07:00']
            );
        }

        return $this->success(null, 'Preferences updated');
    }

    /** Mobile app registers its FCM device token here for push notifications. */
    public function registerFcmToken(Request $request): JsonResponse
    {
        $request->validate(['fcm_token' => 'required|string']);
        $student = auth('sanctum')->user();

        PushSubscription::updateOrCreate(
            ['student_id' => $student->id, 'channel' => 'fcm', 'token' => $request->fcm_token],
            []
        );

        return $this->success(null, 'Device registered for push notifications');
    }
}
