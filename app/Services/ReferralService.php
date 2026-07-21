<?php

namespace App\Services;

use App\Models\Referral;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

/**
 * Rewards both sides of a referral pair the first time the *referee* makes
 * a paid purchase (REQ-REF-04/05/06). Safe to call on every completed
 * order — it no-ops once a pair has already been rewarded, and no-ops
 * entirely if the student wasn't referred by anyone.
 */
class ReferralService
{
    public function rewardIfEligible(Student $referee): void
    {
        if (! $referee->referred_by_student_id) {
            return;
        }

        $referral = Referral::firstOrCreate(
            ['referrer_student_id' => $referee->referred_by_student_id, 'referee_student_id' => $referee->id],
            ['status' => 'pending']
        );

        if ($referral->status === 'rewarded') {
            return;
        }

        $settings = app(\App\Services\SiteSettingService::class);
        $maxPerMonth = (int) $settings->get('max_referral_per_month', 10);
        $rewardedThisMonth = Referral::where('referrer_student_id', $referee->referred_by_student_id)
            ->where('status', 'rewarded')
            ->whereMonth('rewarded_at', now()->month)
            ->whereYear('rewarded_at', now()->year)
            ->count();

        if ($rewardedThisMonth >= $maxPerMonth) {
            return;
        }

        $referrerReward = (float) $settings->get('referrer_reward_amount', 20);
        $refereeReward = (float) $settings->get('referee_reward_amount', 20);

        DB::transaction(function () use ($referral, $referee, $referrerReward, $refereeReward) {
            $wallet = app(WalletService::class);

            $wallet->credit($referral->referrer, $referrerReward, 'referral_bonus', null, 'Referral reward — referred '.$referee->name);
            $wallet->credit($referee, $refereeReward, 'referral_bonus', null, 'Referral reward — welcome bonus');

            $referral->update([
                'status' => 'rewarded',
                'referrer_reward' => $referrerReward,
                'referee_reward' => $refereeReward,
                'rewarded_at' => now(),
            ]);
        });
    }
}
