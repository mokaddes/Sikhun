<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Student;
use App\Models\StudentSubscription;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Admin-initiated subscription grant (no payment involved — the
     * student-facing purchase flow with wallet/gateway debit lands in
     * Phase 3). Expires any currently active subscription first so a
     * student never holds two "active" rows at once.
     */
    public function assign(Student $student, Plan $plan, int $months): StudentSubscription
    {
        return DB::transaction(function () use ($student, $plan, $months) {
            $student->subscriptions()->where('status', 'active')->update(['status' => 'expired']);

            $subscription = StudentSubscription::create([
                'student_id' => $student->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'ai_chat_minutes_remaining' => $plan->ai_chat_minutes,
                'ai_exam_count_remaining' => $plan->ai_exam_count,
                'started_at' => now(),
                'expires_at' => now()->addMonths($months),
            ]);

            foreach ($plan->gift_book_ids ?? [] as $bookId) {
                $student->bookShelf()->firstOrCreate(
                    ['book_id' => $bookId],
                    ['source' => 'subscription_gift', 'added_at' => now()]
                );
            }

            return $subscription;
        });
    }
}
