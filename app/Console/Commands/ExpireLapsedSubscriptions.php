<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\Student;
use App\Models\StudentSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireLapsedSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire-lapsed';

    protected $description = 'Expire lapsed subscriptions and move ended trials to the free plan';

    public function handle(): int
    {
        $expired = StudentSubscription::where('status', 'active')
            ->where('expires_at', '<=', now())
            ->update(['status' => 'expired']);

        $freePlan = Plan::where('is_free', true)->where('is_active', true)->first();
        $downgraded = 0;

        if ($freePlan) {
            Student::query()
                ->where(function ($query) {
                    $query->where(fn ($q) => $q->whereNotNull('trial_ends_at')->where('trial_ends_at', '<=', now()))
                        ->orWhere(fn ($q) => $q->whereNull('trial_ends_at')->where('created_at', '<=', now()->subDays(7)));
                })
                ->chunkById(200, function ($students) use ($freePlan, &$downgraded) {
                    foreach ($students as $student) {
                        $hasCurrentSubscription = $student->subscriptions()
                            ->where('status', 'active')
                            ->where('expires_at', '>', now())
                            ->exists();

                        if ($hasCurrentSubscription) {
                            continue;
                        }

                        DB::transaction(function () use ($student, $freePlan, &$downgraded) {
                            $lockedStudent = Student::whereKey($student->id)->lockForUpdate()->first();
                            $hasCurrentSubscription = $lockedStudent->subscriptions()
                                ->where('status', 'active')
                                ->where('expires_at', '>', now())
                                ->exists();

                            if ($hasCurrentSubscription) {
                                return;
                            }

                            $lockedStudent->subscriptions()->create([
                                'plan_id' => $freePlan->id,
                                'status' => 'active',
                                'ai_chat_minutes_remaining' => $freePlan->ai_chat_minutes,
                                'ai_exam_count_remaining' => $freePlan->ai_exam_count,
                                'ai_vision_remaining' => $freePlan->ai_vision_limit ?? 0,
                                'monthly_reuploads_remaining' => $freePlan->monthly_reupload_limit ?? 0,
                                'started_at' => now(),
                                'expires_at' => now()->addMonth(),
                            ]);

                            $downgraded++;
                        });
                    }
                });
        }

        $this->info("Marked {$expired} lapsed subscription(s) as expired; started {$downgraded} free plan period(s).");

        return self::SUCCESS;
    }
}
