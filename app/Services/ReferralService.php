<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Referral;
use App\Models\ReferralEvent;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/** Referral funnel, fraud review, and idempotent wallet reward handling. */
class ReferralService
{
    public function recordClick(Request $request, string $code): ?ReferralEvent
    {
        $referrer = Student::where('referral_code', $code)->first();
        if (! $referrer) {
            return null;
        }

        $event = ReferralEvent::create([
            'referrer_student_id' => $referrer->id,
            'event_type' => 'click',
            'device_hash' => $this->deviceHash($request),
            'ip_hash' => $this->fingerprint($request->ip()),
            'session_hash' => $this->fingerprint($request->hasSession() ? $request->session()->getId() : null),
        ]);

        if ($request->hasSession()) {
            $request->session()->put('referral_click_id', $event->id);
        }

        return $event;
    }

    public function recordRegistration(Student $referee, Request $request): ?Referral
    {
        $phoneHash = $this->phoneHash($request->input('phone'));
        $referee->forceFill(['phone_hash' => $phoneHash])->save();

        $referrerId = $referee->referred_by_student_id;
        if (! $referrerId || $referrerId === $referee->id) {
            return null;
        }

        $referral = Referral::firstOrCreate(
            ['referrer_student_id' => $referrerId, 'referee_student_id' => $referee->id],
            ['status' => 'pending', 'fraud_status' => 'clear', 'registered_at' => now()]
        );

        if ($referral->wasRecentlyCreated) {
            $deviceHash = $this->deviceHash($request);
            $ipHash = $this->fingerprint($request->ip());
            $signals = $this->matchingSignals($referee, $phoneHash, $deviceHash, $ipHash);
            $held = in_array('same_phone', $signals, true)
                || in_array('same_device', $signals, true)
                || count($signals) > 1;

            $referral->update([
                'fraud_status' => $held ? 'held' : 'clear',
                'risk_signals' => $signals,
                'registered_at' => now(),
            ]);

            ReferralEvent::create([
                'referrer_student_id' => $referrerId,
                'referral_id' => $referral->id,
                'event_type' => 'registered',
                'phone_hash' => $phoneHash,
                'device_hash' => $deviceHash,
                'ip_hash' => $ipHash,
                'session_hash' => $this->fingerprint($request->hasSession() ? $request->session()->getId() : null),
            ]);

            $clickId = $request->hasSession() ? $request->session()->pull('referral_click_id') : null;
            if ($clickId) {
                ReferralEvent::whereKey($clickId)
                    ->where('referrer_student_id', $referrerId)
                    ->where('event_type', 'click')
                    ->whereNull('referral_id')
                    ->update(['referral_id' => $referral->id]);
            }
        }

        return $referral;
    }

    /** Called after a successfully fulfilled paid order; repeated calls are safe. */
    public function rewardIfEligible(Student $referee): void
    {
        if (! $referee->referred_by_student_id) {
            return;
        }

        DB::transaction(function () use ($referee) {
            $referral = Referral::where('referrer_student_id', $referee->referred_by_student_id)
                ->where('referee_student_id', $referee->id)
                ->lockForUpdate()
                ->first();

            $referral ??= Referral::create([
                'referrer_student_id' => $referee->referred_by_student_id,
                'referee_student_id' => $referee->id,
                'status' => 'pending',
                'fraud_status' => 'clear',
                'registered_at' => $referee->created_at,
            ]);

            if ($referral->status === 'rewarded' || $referral->fraud_status === 'rejected') {
                return;
            }

            if (! $referral->paid_at) {
                $referral->update(['paid_at' => now()]);
                ReferralEvent::firstOrCreate([
                    'referral_id' => $referral->id,
                    'event_type' => 'paid',
                ], ['referrer_student_id' => $referral->referrer_student_id]);
            }

            if ($referral->fraud_status === 'held') {
                return;
            }

            $this->creditReferral($referral, $referee);
        });
    }

    public function review(Referral $referral, Admin $admin, string $decision): void
    {
        if (! in_array($decision, ['approve', 'reject'], true)) {
            throw new \InvalidArgumentException('Unsupported referral review decision.');
        }

        DB::transaction(function () use ($referral, $admin, $decision) {
            $locked = Referral::whereKey($referral->id)->lockForUpdate()->firstOrFail();
            if ($locked->status === 'rewarded' || $locked->fraud_status !== 'held') {
                return;
            }

            $locked->update([
                'fraud_status' => $decision === 'approve' ? 'approved' : 'rejected',
                'reviewed_at' => now(),
                'reviewed_by_admin_id' => $admin->id,
            ]);

            if ($decision === 'approve' && $locked->paid_at) {
                $this->creditReferral($locked, $locked->referee);
            }
        });
    }

    private function creditReferral(Referral $referral, Student $referee): void
    {
        if ($referral->status === 'rewarded') {
            return;
        }

        $settings = app(SiteSettingService::class);
        $maxPerMonth = (int) $settings->get('max_referral_per_month', 10);
        if ($maxPerMonth <= 0) {
            return;
        }

        $rewardedThisMonth = Referral::where('referrer_student_id', $referral->referrer_student_id)
            ->where('status', 'rewarded')
            ->whereMonth('rewarded_at', now()->month)
            ->whereYear('rewarded_at', now()->year)
            ->count();
        if ($rewardedThisMonth >= $maxPerMonth) {
            return;
        }

        $milestoneNumber = Referral::where('referrer_student_id', $referral->referrer_student_id)
            ->where('status', 'rewarded')
            ->count() + 1;
        [$referrerReward, $refereeReward] = $this->rewardsForMilestone($settings, $milestoneNumber);

        $wallet = app(WalletService::class);
        $referee->refresh();
        $wallet->credit($referral->referrer, $referrerReward, 'referral_bonus', null, 'Referral reward — referred '.$referee->name);
        $wallet->credit($referee, $refereeReward, 'referral_bonus', null, 'Referral reward — welcome bonus');

        $referral->update([
            'status' => 'rewarded',
            'referrer_reward' => $referrerReward,
            'referee_reward' => $refereeReward,
            'rewarded_at' => now(),
        ]);
    }

    private function rewardsForMilestone(SiteSettingService $settings, int $milestoneNumber): array
    {
        $referrerReward = (float) $settings->get('referrer_reward_amount', 20);
        $refereeReward = (float) $settings->get('referee_reward_amount', 20);
        $rawTiers = $settings->get('referral_reward_tiers', '');
        $tiers = is_array($rawTiers) ? $rawTiers : json_decode((string) $rawTiers, true);

        if (! is_array($tiers)) {
            return [$referrerReward, $refereeReward];
        }

        usort($tiers, fn ($a, $b) => (int) ($a['milestone'] ?? 0) <=> (int) ($b['milestone'] ?? 0));
        foreach ($tiers as $tier) {
            if ((int) ($tier['milestone'] ?? 0) <= $milestoneNumber) {
                $referrerReward = (float) $tier['referrer_reward'];
                $refereeReward = (float) $tier['referee_reward'];
            }
        }

        return [$referrerReward, $refereeReward];
    }

    private function matchingSignals(Student $referee, ?string $phoneHash, ?string $deviceHash, ?string $ipHash): array
    {
        $signals = [];
        if ($phoneHash && Student::where('id', '!=', $referee->id)->where('phone_hash', $phoneHash)->exists()) {
            $signals[] = 'same_phone';
        }

        $prior = ReferralEvent::where('event_type', 'registered')->whereNotNull('referral_id');
        if ($deviceHash && (clone $prior)->where('device_hash', $deviceHash)->exists()) {
            $signals[] = 'same_device';
        }
        if ($ipHash && (clone $prior)->where('ip_hash', $ipHash)->exists()) {
            $signals[] = 'same_ip';
        }

        return $signals;
    }

    private function phoneHash(?string $phone): ?string
    {
        $normalized = preg_replace('/\D+/', '', (string) $phone);

        return $normalized !== '' ? $this->fingerprint($normalized) : null;
    }

    private function deviceHash(Request $request): ?string
    {
        return $this->fingerprint($request->header('X-Device-ID') ?: $request->cookie('referral_device_id'));
    }

    private function fingerprint(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return hash_hmac('sha256', Str::lower(trim($value)), (string) config('app.key'));
    }
}
