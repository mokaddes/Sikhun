<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponRedemption;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class CouponService
{
    public function normalizeCode(?string $code): string
    {
        return mb_strtoupper(trim((string) $code));
    }

    public function normalizeTargetType(string $raw): string
    {
        return match (strtolower(trim($raw))) {
            'book', 'book_chapter', 'book_chapters', 'app\models\book', 'app\models\bookchapter' => 'book',
            'course', 'app\models\course' => 'course',
            'plan', 'subscription', 'custom_subscription', 'app\models\subscriptionplan' => 'plan',
            'invoice', 'app\models\invoice' => 'invoice',
            default => strtolower(trim($raw)),
        };
    }

    public function findByCode(string $code): ?Coupon
    {
        $normalized = $this->normalizeCode($code);
        if ($normalized === '') {
            return null;
        }

        return Coupon::query()
            ->where('coupon_kind', 'discount')
            ->whereRaw('UPPER(code) = ?', [$normalized])
            ->first();
    }

    /**
     * Validate a discount coupon against user, target type, product, min order,
     * and global + per-user limits, returning pricing breakdown.
     *
     * @return array{
     *   coupon: Coupon,
     *   coupon_id: int,
     *   code: string,
     *   name: string,
     *   discount_type: string,
     *   discount_value: float,
     *   subtotal: float,
     *   discount_amount: float,
     *   final_amount: float,
     *   is_free: bool
     * }
     *
     * @throws \RuntimeException
     */
    public function validateAndCalculate(
        Student $student,
        string $code,
        string $targetType,
        ?int $productId,
        float $subtotal
    ): array {
        $coupon = $this->findByCode($code);
        if (! $coupon) {
            throw new \RuntimeException('কুপন কোডটি সঠিক নয় বা ডিসকাউন্টের জন্য প্রযোজ্য নয়।');
        }

        if (! $coupon->is_active) {
            throw new \RuntimeException('এই কুপন কোডটি বর্তমানে নিষ্ক্রিয় রয়েছে।');
        }

        $now = now();
        if ($coupon->starts_at && $now->lt($coupon->starts_at)) {
            throw new \RuntimeException('এই কুপনটির ব্যবহারের সময় এখনো শুরু হয়নি।');
        }
        if ($coupon->ends_at && $now->gte($coupon->ends_at)) {
            throw new \RuntimeException('এই কুপন কোডটির মেয়াদ শেষ হয়ে গেছে।');
        }

        $allowedUserIds = array_values(array_unique(array_filter(array_map(
            'intval',
            array_merge(
                $coupon->student_id ? [$coupon->student_id] : [],
                is_array($coupon->allowed_student_ids) ? $coupon->allowed_student_ids : []
            )
        ))));

        if ($allowedUserIds !== [] && ! in_array((int) $student->id, $allowedUserIds, true)) {
            throw new \RuntimeException('এই কুপনটি শুধুমাত্র নির্ধারিত শিক্ষার্থীর জন্য প্রযোজ্য।');
        }

        $type = $this->normalizeTargetType($targetType);
        $allowedTypes = array_values(array_filter(array_map('strtolower', (array) $coupon->applicable_types)));

        if ($allowedTypes !== [] && ! in_array('all', $allowedTypes, true) && ! in_array($type, $allowedTypes, true)) {
            throw new \RuntimeException('এই কুপনটি নির্বাচিত ধরনের পণ্য বা সেবার জন্য প্রযোজ্য নয়।');
        }

        $productMap = is_array($coupon->applicable_product_ids) ? $coupon->applicable_product_ids : [];
        $specificIds = array_values(array_unique(array_filter(array_map('intval', (array) ($productMap[$type] ?? [])))));

        if ($specificIds !== []) {
            if ($productId === null || ! in_array((int) $productId, $specificIds, true)) {
                throw new \RuntimeException('এই কুপনটি নির্বাচিত আইটেমটির জন্য প্রযোজ্য নয়।');
            }
        } elseif ($allowedTypes === [] || in_array('all', $allowedTypes, true)) {
            // If specific products were chosen on other categories while type wasn't explicitly restricted,
            // only apply to those listed categories.
            $hasAnySpecific = collect($productMap)->flatten()->filter()->isNotEmpty();
            if ($hasAnySpecific) {
                throw new \RuntimeException('এই কুপনটি নির্বাচিত পণ্য বা সেবার জন্য প্রযোজ্য নয়।');
            }
        }

        $subtotal = max(0.0, round($subtotal, 2));
        if ($coupon->min_order_amount !== null && $subtotal < (float) $coupon->min_order_amount) {
            throw new \RuntimeException(sprintf(
                'এই কুপনটি ব্যবহারের জন্য ন্যূনতম ৳%s অর্ডার প্রয়োজন।',
                number_format((float) $coupon->min_order_amount, 0)
            ));
        }

        if ($coupon->max_uses !== null && (int) $coupon->uses_count >= (int) $coupon->max_uses) {
            throw new \RuntimeException('এই কুপনটির সর্বোচ্চ ব্যবহারের সীমা শেষ হয়ে গেছে।');
        }

        if ($coupon->max_uses_per_user !== null) {
            $userUses = $coupon->redemptions()->where('student_id', $student->id)->count();
            if ($userUses >= (int) $coupon->max_uses_per_user) {
                throw new \RuntimeException('আপনি এই কুপনটি সর্বোচ্চ অনুমোদিত বার ব্যবহার করে ফেলেছেন।');
            }
        }

        $value = max(0.0, (float) $coupon->discount_value);
        if ($coupon->discount_type === 'fixed') {
            $discount = min($subtotal, $value);
        } else {
            $discount = round(($subtotal * $value) / 100, 2);
            if ($coupon->max_discount_amount !== null && (float) $coupon->max_discount_amount > 0) {
                $discount = min($discount, (float) $coupon->max_discount_amount);
            }
            $discount = min($subtotal, $discount);
        }

        $discount = round(max(0.0, $discount), 2);
        $finalAmount = round(max(0.0, $subtotal - $discount), 2);

        return [
            'coupon' => $coupon,
            'coupon_id' => $coupon->id,
            'code' => (string) $coupon->code,
            'name' => $coupon->name,
            'discount_type' => $coupon->discount_type,
            'discount_value' => $value,
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'final_amount' => $finalAmount,
            'is_free' => $finalAmount <= 0,
        ];
    }

    /**
     * Atomically record coupon consumption with global and per-user locks.
     * Idempotent by order_id or invoice_id so gateway callbacks never double-count.
     *
     * @throws \RuntimeException
     */
    public function consume(
        int $couponId,
        Student $student,
        string $targetType,
        ?int $targetId,
        float $discountAmount,
        ?Order $order = null,
        ?Invoice $invoice = null
    ): CouponRedemption {
        return DB::transaction(function () use ($couponId, $student, $targetType, $targetId, $discountAmount, $order, $invoice) {
            if ($order) {
                $existing = CouponRedemption::where('coupon_id', $couponId)
                    ->where('order_id', $order->id)
                    ->first();
                if ($existing) {
                    return $existing;
                }
            }

            if ($invoice && ! $order) {
                $existing = CouponRedemption::where('coupon_id', $couponId)
                    ->where('invoice_id', $invoice->id)
                    ->first();
                if ($existing) {
                    return $existing;
                }
            }

            /** @var Coupon|null $coupon */
            $coupon = Coupon::whereKey($couponId)->lockForUpdate()->first();
            if (! $coupon || ! $coupon->is_active) {
                throw new \RuntimeException('কুপনটি আর সক্রিয় নেই।');
            }

            if ($coupon->max_uses !== null && (int) $coupon->uses_count >= (int) $coupon->max_uses) {
                throw new \RuntimeException('এই কুপনটির ব্যবহারের সীমা শেষ হয়ে গেছে।');
            }

            if ($coupon->max_uses_per_user !== null) {
                $userUses = $coupon->redemptions()->where('student_id', $student->id)->count();
                if ($userUses >= (int) $coupon->max_uses_per_user) {
                    throw new \RuntimeException('আপনার জন্য এই কুপনের ব্যবহারের সীমা পূর্ণ হয়েছে।');
                }
            }

            $coupon->increment('uses_count');

            return $coupon->redemptions()->create([
                'student_id' => $student->id,
                'order_id' => $order?->id,
                'invoice_id' => $invoice?->id,
                'target_type' => $this->normalizeTargetType($targetType),
                'target_id' => $targetId,
                'discount_amount' => round(max(0.0, $discountAmount), 2),
                'redeemed_at' => now(),
            ]);
        });
    }
}
