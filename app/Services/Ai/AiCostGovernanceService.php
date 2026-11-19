<?php

namespace App\Services\Ai;

use App\Models\AiLog;
use App\Models\Student;
use App\Services\SiteSettingService;

/**
 * Enforces monthly per-student AI spend ceilings so heavy usage on premium
 * models cannot erode subscription unit economics.
 */
class AiCostGovernanceService
{
    public function __construct(protected SiteSettingService $settings) {}

    public function monthlySpendUsd(Student $student): float
    {
        return (float) AiLog::query()
            ->where('student_id', $student->id)
            ->where('status', 'success')
            ->where('created_at', '>=', now()->startOfMonth())
            ->sum('cost');
    }

    public function monthlyLimitUsd(Student $student): ?float
    {
        $plan = $student->activeSubscription?->plan;
        if ($plan && $plan->monthly_ai_cost_limit_usd !== null && (float) $plan->monthly_ai_cost_limit_usd > 0) {
            return (float) $plan->monthly_ai_cost_limit_usd;
        }

        $globalLimit = $this->settings->get('ai_default_monthly_cost_limit_usd');
        if ($globalLimit !== null && is_numeric($globalLimit) && (float) $globalLimit > 0) {
            return (float) $globalLimit;
        }

        return null;
    }

    public function checkLimit(Student $student): void
    {
        $limit = $this->monthlyLimitUsd($student);
        if ($limit === null) {
            return;
        }

        $spent = $this->monthlySpendUsd($student);
        if ($spent >= $limit) {
            throw new \RuntimeException('Monthly AI usage budget reached for your current plan. Please upgrade or wait for the next billing cycle.');
        }
    }
}
