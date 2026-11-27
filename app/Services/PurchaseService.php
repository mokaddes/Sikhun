<?php

namespace App\Services;

use App\Services\Payment\PaymentGatewayContract;
use App\Mail\OrderConfirmationMail;
use App\Models\Book;
use App\Models\BookChapter;
use App\Models\Course;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Student;
use App\Services\CourseDeliveryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PurchaseService
{
    public function __construct(
        protected WalletService $wallet,
        protected SubscriptionService $subscriptions,
        protected ReferralService $referrals,
        protected InvoiceService $invoices,
        protected CourseDeliveryService $courseDelivery,
        protected CouponService $coupons
    ) {}

    public function purchaseBook(Student $student, Book $book, string $method, ?PaymentGatewayContract $gateway = null, ?string $couponCode = null): array
    {
        [$amount, $couponMeta] = $this->applyDiscountCoupon($student, $couponCode, 'book', $book->id, (float) $book->price);
        if ($method === 'coupon' && $amount > 0) {
            throw new \RuntimeException('এই কুপনটি পুরো মূল্য মওকুফ করেনি। পেমেন্টের মাধ্যম নির্বাচন করুন।');
        }
        $effectiveMethod = $amount <= 0 ? 'free' : $method;
        $order = $this->createOrder($student, 'book', $book->id, $amount, $effectiveMethod, $couponMeta);

        if ($effectiveMethod === 'free' || $amount <= 0) {
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        if ($effectiveMethod === 'wallet') {
            $this->wallet->debit($student, $amount, 'book_purchase', $order->order_number, "Purchased book: {$book->title}");
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        $redirectUrl = $gateway->initiate($order);

        return ['redirect_url' => $redirectUrl, 'order' => $order];
    }

    public function purchaseChapter(Student $student, BookChapter $chapter, string $method, ?PaymentGatewayContract $gateway = null, ?string $couponCode = null): array
    {
        [$amount, $couponMeta] = $this->applyDiscountCoupon($student, $couponCode, 'book', (int) $chapter->book_id, (float) $chapter->price);
        if ($method === 'coupon' && $amount > 0) {
            throw new \RuntimeException('এই কুপনটি পুরো মূল্য মওকুফ করেনি। পেমেন্টের মাধ্যম নির্বাচন করুন।');
        }
        $effectiveMethod = $amount <= 0 ? 'free' : $method;
        $meta = array_merge(['book_id' => $chapter->book_id], $couponMeta);
        $order = $this->createOrder($student, 'book_chapter', $chapter->id, $amount, $effectiveMethod, $meta);

        if ($effectiveMethod === 'free' || $amount <= 0) {
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        if ($effectiveMethod === 'wallet') {
            $this->wallet->debit($student, $amount, 'chapter_purchase', $order->order_number, "Purchased chapter: {$chapter->title}");
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        $redirectUrl = $gateway->initiate($order);

        return ['redirect_url' => $redirectUrl, 'order' => $order];
    }

    /**
     * Purchase multiple chapters of a book in a single transaction.
     */
    public function purchaseChapters(Student $student, Book $book, array $chapters, string $method, ?PaymentGatewayContract $gateway = null, ?string $couponCode = null): array
    {
        $ownedChapterIds = $student->ownedChapters()->where('book_id', $book->id)->pluck('chapter_id')->all();
        $unowned = array_filter($chapters, fn ($c) => ! in_array($c->id, $ownedChapterIds, true));

        if (empty($unowned)) {
            throw new \RuntimeException('You already own all selected chapters.');
        }

        $totalPrice = 0.0;
        $chapterIds = [];
        foreach ($unowned as $ch) {
            $totalPrice += (float) ($ch->price ?? 0);
            $chapterIds[] = $ch->id;
        }

        $count = count($chapterIds);
        [$amount, $couponMeta] = $this->applyDiscountCoupon($student, $couponCode, 'book', $book->id, $totalPrice);
        if ($method === 'coupon' && $amount > 0) {
            throw new \RuntimeException('এই কুপনটি পুরো মূল্য মওকুফ করেনি। পেমেন্টের মাধ্যম নির্বাচন করুন।');
        }
        $effectiveMethod = $amount <= 0 ? 'free' : $method;
        $meta = array_merge([
            'book_id' => $book->id,
            'chapter_ids' => $chapterIds,
            'count' => $count,
        ], $couponMeta);
        $order = $this->createOrder($student, 'book_chapters', $book->id, $amount, $effectiveMethod, $meta);

        if ($effectiveMethod === 'free' || $amount <= 0) {
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        if ($effectiveMethod === 'wallet') {
            $this->wallet->debit($student, $amount, 'chapter_purchase', $order->order_number, "Purchased {$count} chapters of {$book->title}");
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        $redirectUrl = $gateway->initiate($order);

        return ['redirect_url' => $redirectUrl, 'order' => $order];
    }

    public function purchaseCourse(Student $student, Course $course, string $method, ?PaymentGatewayContract $gateway = null, array $meta = [], ?string $couponCode = null): array
    {
        [$amount, $couponMeta] = $this->applyDiscountCoupon($student, $couponCode, 'course', $course->id, (float) $course->price);
        if ($method === 'coupon' && $amount > 0) {
            throw new \RuntimeException('এই কুপনটি পুরো মূল্য মওকুফ করেনি। পেমেন্টের মাধ্যম নির্বাচন করুন।');
        }
        $effectiveMethod = $amount <= 0 ? 'free' : $method;
        $order = $this->createOrder($student, 'course', $course->id, $amount, $effectiveMethod, array_merge($meta, $couponMeta));

        if ($effectiveMethod === 'free' || $amount <= 0) {
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        if ($effectiveMethod === 'wallet') {
            $this->wallet->debit($student, $amount, 'course_purchase', $order->order_number, "Enrolled: {$course->title}");
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        $redirectUrl = $gateway->initiate($order);

        return ['redirect_url' => $redirectUrl, 'order' => $order];
    }

    public function purchaseSubscription(Student $student, Plan $plan, int $months, string $method, ?PaymentGatewayContract $gateway = null, ?string $couponCode = null): array
    {
        if ($plan->price_total > 0 && ($months === (int) $plan->duration_months || $months <= 1)) {
            $amount = (float) $plan->price_total;
            $baseAmount = $amount;
            $months = (int) ($plan->duration_months ?: 1);
        } else {
            $baseAmount = (float) $plan->price_monthly * $months;
            $discountFactor = match ($months) {
                3 => 0.90,
                6 => 0.80,
                12 => 0.70,
                default => 1.0,
            };
            $amount = round($baseAmount * $discountFactor, 2);
        }

        // Instant fulfillment for Free Plan (0 Tk)
        if ($amount <= 0 || $plan->is_free || $method === 'free') {
            $order = $this->createOrder($student, 'subscription', $plan->id, 0.0, 'free', ['months' => $months]);
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        [$finalAmount, $couponMeta] = $this->applyDiscountCoupon($student, $couponCode, 'plan', $plan->id, $amount);
        if ($method === 'coupon' && $finalAmount > 0) {
            throw new \RuntimeException('এই কুপনটি পুরো মূল্য মওকুফ করেনি। পেমেন্টের মাধ্যম নির্বাচন করুন।');
        }
        $effectiveMethod = $finalAmount <= 0 ? 'free' : $method;
        $order = $this->createOrder($student, 'subscription', $plan->id, $finalAmount, $effectiveMethod, array_merge([
            'months' => $months,
            'base_amount' => $baseAmount,
            'discount' => round($baseAmount - $finalAmount, 2),
        ], $couponMeta));

        if ($effectiveMethod === 'free' || $finalAmount <= 0) {
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        if ($effectiveMethod === 'wallet') {
            $this->wallet->debit($student, $finalAmount, 'subscription_purchase', $order->order_number, "Subscribed: {$plan->name} ({$months}mo)");
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        $redirectUrl = $gateway->initiate($order);

        return ['redirect_url' => $redirectUrl, 'order' => $order];
    }

    public function purchaseCustomSubscription(Student $student, array $config, int $months, string $method, ?PaymentGatewayContract $gateway = null, ?string $couponCode = null): array
    {
        $rawAmount = (float) ($config['total_price'] ?? 199) * $months;
        [$finalAmount, $couponMeta] = $this->applyDiscountCoupon($student, $couponCode, 'plan', null, $rawAmount);
        if ($method === 'coupon' && $finalAmount > 0) {
            throw new \RuntimeException('এই কুপনটি পুরো মূল্য মওকুফ করেনি। পেমেন্টের মাধ্যম নির্বাচন করুন।');
        }
        $effectiveMethod = $finalAmount <= 0 ? 'free' : $method;
        $order = $this->createOrder($student, 'custom_subscription', 0, $finalAmount, $effectiveMethod, array_merge([
            'months' => $months,
            'custom_config' => $config,
        ], $couponMeta));

        if ($effectiveMethod === 'free' || $finalAmount <= 0) {
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        if ($effectiveMethod === 'wallet') {
            $this->wallet->debit($student, $finalAmount, 'custom_subscription_purchase', $order->order_number, "Custom Plan Subscribed ({$months}mo)");
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        $redirectUrl = $gateway->initiate($order);

        return ['redirect_url' => $redirectUrl, 'order' => $order];
    }

    public function initiateWalletRecharge(Student $student, float $amount, string $method, ?PaymentGatewayContract $gateway = null): array
    {
        $order = $this->createOrder($student, 'wallet_recharge', null, $amount, $method);

        if ($method === 'manual') {
            // Stays pending — an admin approves it from /admin/orders once the
            // bank transfer / bKash-to-personal-number payment is confirmed.
            return ['redirect_url' => null, 'order' => $order];
        }

        $redirectUrl = $gateway->initiate($order);

        return ['redirect_url' => $redirectUrl, 'order' => $order];
    }

    /**
     * Called from the payment callback / webhook after verify() has
     * confirmed the transaction server-side. Atomic & idempotent.
     */
    public function completeGatewayOrder(Order $order, string $gatewayTransactionId): void
    {
        DB::transaction(function () use ($order, $gatewayTransactionId) {
            $lockedOrder = Order::whereKey($order->id)->lockForUpdate()->first();
            if (! $lockedOrder || $lockedOrder->status === 'completed') {
                return;
            }

            $lockedOrder->update(['status' => 'completed', 'gateway_transaction_id' => $gatewayTransactionId]);
            $this->fulfill($lockedOrder);
        });
    }

    /**
     * Delivers whatever the order actually paid for. Centralized here so
     * both the wallet-instant path and the gateway-callback path share
     * identical fulfillment logic.
     */
    public function fulfill(Order $order): void
    {
        match ($order->orderable_type) {
            'book' => $order->student->bookShelf()->firstOrCreate(
                ['book_id' => $order->orderable_id],
                ['source' => 'purchased', 'added_at' => now()]
            ),
            'book_chapter' => $order->student->ownedChapters()->firstOrCreate(
                ['book_id' => $order->meta['book_id'], 'chapter_id' => $order->orderable_id],
                ['source' => 'purchased', 'price' => $order->amount, 'purchased_at' => now()]
            ),
            'book_chapters' => (function () use ($order) {
                $chapterIds = $order->meta['chapter_ids'] ?? [];
                $bookId = $order->meta['book_id'] ?? $order->orderable_id;
                foreach ($chapterIds as $cId) {
                    $ch = \App\Models\BookChapter::find($cId);
                    $order->student->ownedChapters()->firstOrCreate(
                        ['book_id' => $bookId, 'chapter_id' => $cId],
                        ['source' => 'purchased', 'price' => $ch ? (float) $ch->price : 0, 'purchased_at' => now()]
                    );
                }
            })(),
            'course' => $order->student->courseEnrollments()->firstOrCreate(
                ['course_id' => $order->orderable_id],
                ['progress_percentage' => 0]
            ),
            'custom_subscription' => $this->subscriptions->assignCustom(
                $order->student,
                $order->meta['custom_config'] ?? [],
                (int) ($order->meta['months'] ?? 1)
            ),
            'subscription' => $this->subscriptions->assign(
                $order->student,
                Plan::findOrFail($order->orderable_id),
                (int) ($order->meta['months'] ?? 1)
            ),
            'wallet_recharge' => $this->wallet->credit(
                $order->student,
                (float) $order->amount,
                'wallet_recharge',
                $order->order_number,
                'Wallet recharge via '.$order->payment_method
            ),
            'invoice' => (function () use ($order) {
                $invoice = \App\Models\Invoice::find($order->orderable_id);
                if ($invoice) {
                    $invUpdates = ['status' => 'paid'];
                    if (! empty($order->meta['coupon_id']) && ! empty($order->meta['coupon_discount'])) {
                        $couponDiscount = (float) $order->meta['coupon_discount'];
                        $newDiscount = round((float) $invoice->discount + $couponDiscount, 2);
                        $newTotal = max(0.0, round((float) $invoice->total - $couponDiscount, 2));
                        $meta = is_array($invoice->meta) ? $invoice->meta : [];
                        $meta['coupon_id'] = (int) $order->meta['coupon_id'];
                        $meta['coupon_code'] = $order->meta['coupon_code'] ?? null;
                        $meta['coupon_discount'] = $couponDiscount;
                        $invUpdates['discount'] = $newDiscount;
                        $invUpdates['total'] = $newTotal;
                        $invUpdates['meta'] = $meta;
                    }
                    $invoice->update($invUpdates);
                    if (! empty($invoice->meta['subscription_id'])) {
                        $sub = \App\Models\StudentSubscription::find($invoice->meta['subscription_id']);
                        if ($sub && $sub->status === 'active') {
                            $sub->update(['expires_at' => $sub->expires_at->addMonth()]);
                        } elseif ($sub) {
                            $this->subscriptions->assign($order->student, $sub->plan, 1);
                        }
                    } elseif (! empty($invoice->meta['plan_id'])) {
                        $plan = \App\Models\Plan::find($invoice->meta['plan_id']);
                        if ($plan) {
                            $this->subscriptions->assign($order->student, $plan, 1);
                        }
                    }
                    if (! empty($invoice->meta['course_id'])) {
                        $course = Course::find($invoice->meta['course_id']);
                        if ($course) {
                            $order->student->courseEnrollments()->firstOrCreate(
                                ['course_id' => $course->id],
                                ['progress_percentage' => 0]
                            );
                            $this->courseDelivery->deliver($order->student, $course, $order);
                        }
                    }
                    if (! empty($invoice->meta['book_id'])) {
                        $order->student->bookShelf()->firstOrCreate(
                            ['book_id' => (int) $invoice->meta['book_id']],
                            ['source' => 'purchased', 'added_at' => now()]
                        );
                    }
                }
            })(),
            default => null,
        };

        if (! empty($order->meta['coupon_id']) && $order->orderable_type !== 'wallet_recharge') {
            $this->coupons->consume(
                (int) $order->meta['coupon_id'],
                $order->student,
                (string) ($order->meta['coupon_target_type'] ?? $order->orderable_type),
                isset($order->meta['coupon_target_id']) ? (int) $order->meta['coupon_target_id'] : $order->orderable_id,
                (float) ($order->meta['coupon_discount'] ?? 0),
                $order,
                $order->orderable_type === 'invoice' ? \App\Models\Invoice::find($order->orderable_id) : null
            );
        }

        // Link-based courses hand the student the product (the link) by email —
        // video courses need nothing here, their content lives in the lessons.
        if ($order->orderable_type === 'course') {
            $course = Course::find($order->orderable_id);

            if ($course) {
                $this->courseDelivery->deliver($order->student, $course, $order);
            }
        }

        if ($order->orderable_type !== 'wallet_recharge' && $order->orderable_type !== 'invoice') {
            $this->referrals->rewardIfEligible($order->student);
            try {
                Mail::to($order->student->email)->send(new OrderConfirmationMail($order));
            } catch (\Throwable $e) {
                Log::warning("Order confirmation mail delivery failed: " . $e->getMessage());
            }
        }

        // Invoicing must never break fulfilment: a mail/DB hiccup while
        // writing the receipt is logged, not surfaced to the buyer.
        // Orders paying an existing invoice already have an invoice record.
        if ($order->orderable_type !== 'invoice') {
            try {
                $this->invoices->createForOrder($order->refresh());
            } catch (\Throwable $e) {
                Log::error('Invoice creation failed', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * @return array{0: float, 1: array}
     */
    private function applyDiscountCoupon(Student $student, ?string $couponCode, string $targetType, ?int $productId, float $subtotal): array
    {
        $code = trim((string) $couponCode);
        if ($code === '') {
            return [$subtotal, []];
        }

        $res = $this->coupons->validateAndCalculate($student, $code, $targetType, $productId, $subtotal);

        return [
            $res['final_amount'],
            [
                'coupon_id' => $res['coupon_id'],
                'coupon_code' => $res['code'],
                'coupon_discount' => $res['discount_amount'],
                'original_amount' => $res['subtotal'],
                'coupon_target_type' => $this->coupons->normalizeTargetType($targetType),
                'coupon_target_id' => $productId,
            ],
        ];
    }

    private function createOrder(Student $student, string $type, ?int $orderableId, float $amount, string $method, array $extra = []): Order
    {
        return Order::create([
            'student_id' => $student->id,
            'order_number' => 'SKH-'.now()->format('ymd').'-'.strtoupper(Str::random(6)),
            'orderable_type' => $type,
            'orderable_id' => $orderableId,
            'amount' => $amount,
            'payment_method' => $method,
            'meta' => $extra ?: null,
            'status' => 'pending',
        ]);
    }
}