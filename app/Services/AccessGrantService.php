<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\FreeCampaign;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

/**
 * Single source of truth for coupon-granted and free-campaign access.
 * Every "can this student get in without a package (or beyond it)" check
 * goes through here so the limitation cannot be bypassed by going through
 * a different controller, middleware or API route.
 *
 * A student has grant access when:
 *   - a live free campaign covers them (all / selected), OR
 *   - a live coupon covers them (direct assign or redeemed code).
 */
class AccessGrantService
{
    /**
     * Does the student currently have full free access via a campaign or coupon?
     */
    public function hasActiveAccess(Student $student): bool
    {
        return $this->activeCampaignFor($student) !== null
            || $this->activeCouponFor($student) !== null;
    }

    /**
     * First live campaign covering this student, if any.
     */
    public function activeCampaignFor(Student $student): ?FreeCampaign
    {
        return FreeCampaign::query()
            ->liveFor($student)
            ->latest()
            ->first();
    }

    /**
     * First live coupon covering this student, if any.
     */
    public function activeCouponFor(Student $student): ?Coupon
    {
        return Coupon::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('student_id')->orWhere('student_id', $student->id))
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->get()
            ->first(fn (Coupon $coupon) => $coupon->isActiveForStudent($student));
    }

    /**
     * Redeem a public coupon code. Returns null with no error messages when
     * the code is invalid/expired/exhausted/already used — the caller shows
     * a friendly message.
     *
     * @throws \RuntimeException on failure with a human-readable reason.
     */
    public function redeem(Student $student, string $code): CouponRedemption
    {
        $coupon = Coupon::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>', now()))
            ->first();

        if (! $coupon) {
            throw new \RuntimeException('This coupon code is invalid, expired, or no longer available.');
        }

        if ($coupon->redemptions()->where('student_id', $student->id)->exists()) {
            throw new \RuntimeException('You have already used this coupon code.');
        }

        if ($coupon->max_uses !== null && $coupon->uses_count >= $coupon->max_uses) {
            throw new \RuntimeException('This coupon code has reached its usage limit.');
        }

        $grantedUntil = $coupon->duration_days
            ? now()->addDays($coupon->duration_days)
            : $coupon->ends_at;

        return DB::transaction(function () use ($coupon, $student, $grantedUntil) {
            $coupon->increment('uses_count');

            return $coupon->redemptions()->create([
                'student_id' => $student->id,
                'granted_until' => $grantedUntil,
                'redeemed_at' => now(),
            ]);
        });
    }

    /**
     * Direct-assign a coupon to one student (admin action). If the coupon is
     * duration-based a redemption row is minted so the expiry is measurable;
     * calendar-window coupons need no row (the window itself governs).
     */
    public function assign(Student $student, Coupon $coupon): ?CouponRedemption
    {
        if ((int) $coupon->student_id !== (int) $student->id) {
            $coupon->update(['student_id' => $student->id]);
        }

        if (! $coupon->duration_days) {
            return null;
        }

        return DB::transaction(function () use ($coupon, $student) {
            $coupon->increment('uses_count');

            return $coupon->redemptions()->updateOrCreate(
                ['student_id' => $student->id],
                [
                    'granted_until' => now()->addDays($coupon->duration_days),
                    'redeemed_at' => now(),
                ]
            );
        });
    }

    /**
     * Human-readable summary of active grants for a student's UI.
     */
    public function accessSummary(Student $student): array
    {
        $campaign = $this->activeCampaignFor($student);
        $coupon = $this->activeCouponFor($student);

        $couponEnd = $coupon
            ? $coupon->redemptions()->where('student_id', $student->id)->latest('redeemed_at')->value('granted_until')
            : null;

        return [
            'hasAccess' => $campaign !== null || $coupon !== null,
            'campaign' => $campaign ? [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'ends_at' => $campaign->ends_at,
            ] : null,
            'coupon' => $coupon ? [
                'id' => $coupon->id,
                'name' => $coupon->name,
                'ends_at' => $couponEnd ?? $coupon->ends_at,
            ] : null,
        ];
    }
}
