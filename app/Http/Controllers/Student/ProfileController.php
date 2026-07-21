<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentNotificationPreference;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        $student = auth('web')->user();
        $existing = $student->notificationPreferences()->get()->keyBy('type');

        $preferences = collect(NotificationService::TYPES)->map(function ($label, $type) use ($existing) {
            return [
                'type' => $type,
                'label' => $label,
                'is_enabled' => $existing[$type]->is_enabled ?? true,
                'preferred_time' => $existing[$type]->preferred_time ?? '07:00',
            ];
        })->values();

        return Inertia::render('Student/Profile/Index', [
            'student' => $student,
            'preferences' => $preferences,
        ]);
    }

    /** Called from the Pinia theme store — persists across devices, not just localStorage. */
    public function updateTheme(Request $request): JsonResponse
    {
        $request->validate(['theme_mode' => 'required|in:light,dark,system']);
        auth('web')->user()->update(['theme_mode' => $request->theme_mode]);

        return response()->json(['success' => true]);
    }

    public function updateLeaderboardOptOut(Request $request): RedirectResponse
    {
        $request->validate(['opt_out' => 'required|boolean']);
        auth('web')->user()->update(['leaderboard_opt_out' => $request->boolean('opt_out')]);

        return back()->with('success', 'Preference saved.');
    }

    public function updateNotificationPreferences(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'preferences' => 'required|array',
            'preferences.*.type' => 'required|string',
            'preferences.*.is_enabled' => 'required|boolean',
            'preferences.*.preferred_time' => 'nullable|string',
        ]);

        $student = auth('web')->user();

        foreach ($data['preferences'] as $pref) {
            StudentNotificationPreference::updateOrCreate(
                ['student_id' => $student->id, 'type' => $pref['type']],
                ['is_enabled' => $pref['is_enabled'], 'preferred_time' => $pref['preferred_time'] ?? '07:00']
            );
        }

        return back()->with('success', 'Notification preferences saved.');
    }
}
