<?php

namespace App\Services;

use App\Events\NewNotificationBroadcast;
use App\Models\Student;
use App\Models\StudentNotificationPreference;

class NotificationService
{
    /**
     * The fixed set of notification types the platform sends. Used to
     * drive both the student preferences page and the admin broadcast
     * form — one source of truth so the two never drift apart.
     */
    public const TYPES = [
        'general_knowledge' => 'Daily educational fact',
        'exam_reminder' => 'Upcoming exam reminders',
        'subscription_expiry' => 'Subscription expiry warnings',
        'wallet_low_balance' => 'Low wallet balance alerts',
        'referral_reward' => 'Referral reward credited',
        'book_recommendation' => 'New book recommendations',
        'leaderboard_rank_change' => 'Leaderboard rank changes',
        'course_new_lesson' => 'New course lessons available',
        'support_reply' => 'Support conversation replies',
        'admin_broadcast' => 'Announcements from Sikhun.com',
    ];

    private const MAX_PER_DAY = 3;

    /**
     * Creates the notification row + broadcasts it live over Reverb —
     * but only if the student has this type enabled and hasn't already
     * hit today's cap (REQ-NOTIF-07).
     */
    public function send(Student $student, string $type, string $title, string $body, array $data = []): bool
    {
        if (! $this->isEnabled($student, $type)) {
            return false;
        }

        if ($student->notifications()->whereDate('created_at', today())->count() >= self::MAX_PER_DAY) {
            return false;
        }

        $notification = $student->notifications()->create([
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);

        broadcast(new NewNotificationBroadcast($notification))->toOthers();

        return true;
    }

    private function isEnabled(Student $student, string $type): bool
    {
        $pref = StudentNotificationPreference::where('student_id', $student->id)->where('type', $type)->first();

        // No row yet = default enabled (matches the DB column default).
        return $pref?->is_enabled ?? true;
    }
}
