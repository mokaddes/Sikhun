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
        protected CourseDeliveryService $courseDelivery
    ) {}

    public function purchaseBook(Student $student, Book $book, string $method, ?PaymentGatewayContract $gateway = null): array
    {
        $order = $this->createOrder($student, 'book', $book->id, $book->price, $method);

        if ($method === 'wallet') {
            $this->wallet->debit($student, (float) $book->price, 'book_purchase', $order->order_number, "Purchased book: {$book->title}");
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        $redirectUrl = $gateway->initiate($order);

        return ['redirect_url' => $redirectUrl, 'order' => $order];
    }

    public function purchaseChapter(Student $student, BookChapter $chapter, string $method, ?PaymentGatewayContract $gateway = null): array
    {
        $order = $this->createOrder($student, 'book_chapter', $chapter->id, $chapter->price, $method, ['book_id' => $chapter->book_id]);

        if ($method === 'wallet') {
            $this->wallet->debit($student, (float) $chapter->price, 'chapter_purchase', $order->order_number, "Purchased chapter: {$chapter->title}");
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
    public function purchaseChapters(Student $student, Book $book, array $chapters, string $method, ?PaymentGatewayContract $gateway = null): array
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
        $order = $this->createOrder($student, 'book_chapters', $book->id, $totalPrice, $method, [
            'book_id' => $book->id,
            'chapter_ids' => $chapterIds,
            'count' => $count,
        ]);

        if ($method === 'wallet') {
            $this->wallet->debit($student, $totalPrice, 'chapter_purchase', $order->order_number, "Purchased {$count} chapters of {$book->title}");
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        $redirectUrl = $gateway->initiate($order);

        return ['redirect_url' => $redirectUrl, 'order' => $order];
    }

    public function purchaseCourse(Student $student, Course $course, string $method, ?PaymentGatewayContract $gateway = null, array $meta = []): array
    {
        $order = $this->createOrder($student, 'course', $course->id, $course->price, $method, $meta);

        if ($method === 'wallet') {
            $this->wallet->debit($student, (float) $course->price, 'course_purchase', $order->order_number, "Enrolled: {$course->title}");
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        $redirectUrl = $gateway->initiate($order);

        return ['redirect_url' => $redirectUrl, 'order' => $order];
    }

    public function purchaseSubscription(Student $student, Plan $plan, int $months, string $method, ?PaymentGatewayContract $gateway = null): array
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
        $order = $this->createOrder($student, 'subscription', $plan->id, $amount, $method, [
            'months' => $months,
            'base_amount' => $baseAmount,
            'discount' => round($baseAmount - $amount, 2),
        ]);

        if ($method === 'wallet') {
            $this->wallet->debit($student, $amount, 'subscription_purchase', $order->order_number, "Subscribed: {$plan->name} ({$months}mo)");
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        $redirectUrl = $gateway->initiate($order);

        return ['redirect_url' => $redirectUrl, 'order' => $order];
    }

    public function purchaseCustomSubscription(Student $student, array $config, int $months, string $method, ?PaymentGatewayContract $gateway = null): array
    {
        $amount = (float) ($config['total_price'] ?? 199) * $months;
        $order = $this->createOrder($student, 'custom_subscription', 0, $amount, $method, [
            'months' => $months,
            'custom_config' => $config,
        ]);

        if ($method === 'wallet') {
            $this->wallet->debit($student, $amount, 'custom_subscription_purchase', $order->order_number, "Custom Plan Subscribed ({$months}mo)");
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
                    $invoice->update(['status' => 'paid']);
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
                }
            })(),
            default => null,
        };

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