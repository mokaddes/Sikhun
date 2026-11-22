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
    public function assign(Student $student, Plan $plan, int $months, bool $notify = true): StudentSubscription
    {
        $subscription = DB::transaction(function () use ($student, $plan, $months) {
            $student->subscriptions()->where('status', 'active')->update(['status' => 'expired']);

            return StudentSubscription::create([
                'student_id' => $student->id,
                'plan_id' => $plan->id,
                'status' => 'active',
                'ai_chat_minutes_remaining' => $plan->ai_chat_minutes,
                'ai_exam_count_remaining' => $plan->ai_exam_count,
                'ai_vision_remaining' => $plan->ai_vision_limit ?? 0,
                'monthly_reuploads_remaining' => $plan->monthly_reupload_limit ?? 0,
                'started_at' => now(),
                'expires_at' => now()->addMonths($months),
            ]);
        });

        if ($notify && ! $plan->is_free && (float) $plan->price_monthly > 0) {
            try {
                app(NotificationService::class)->send(
                    student: $student,
                    type: 'plan_update',
                    title: "প্ল্যান অ্যাক্টিভ হয়েছে: {$plan->name}",
                    body: "আপনার অ্যাকাউন্টে {$plan->name} প্ল্যান ({$months} মাস) সফলভাবে চালু হয়েছে। মেয়াদ: {$subscription->expires_at->format('d M Y')}।",
                    data: ['url' => '/subscription/plans', 'plan_id' => $plan->id],
                    mandatory: true
                );
            } catch (\Throwable $e) {
                // Non-fatal
            }
        }

        return $subscription;
    }

    public function assignCustom(Student $student, array $config, int $months = 1): StudentSubscription
    {
        return DB::transaction(function () use ($student, $config, $months) {
            $student->subscriptions()->where('status', 'active')->update(['status' => 'expired']);

            $basePlan = Plan::where('slug', 'pro')->first() ?? Plan::first();

            return StudentSubscription::create([
                'student_id' => $student->id,
                'plan_id' => $basePlan->id,
                'status' => 'active',
                'ai_chat_minutes_remaining' => (int) ($config['minutes'] ?? 300),
                'ai_exam_count_remaining' => (int) ($config['quizzes'] ?? 20),
                'ai_vision_remaining' => (int) ($config['images'] ?? 50),
                'monthly_reuploads_remaining' => (int) ($config['reuploads'] ?? 5),
                'started_at' => now(),
                'expires_at' => now()->addMonths($months),
            ]);
        });
    }
}
