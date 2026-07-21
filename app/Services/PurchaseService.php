<?php

namespace App\Services;

use App\Mail\OrderConfirmationMail;
use App\Models\Book;
use App\Models\Course;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Student;
use App\Services\Payment\PaymentGatewayContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Central checkout orchestrator. Every purchasable thing (book, subscription,
 * wallet top-up) flows through here so there is exactly one place that
 * creates Orders, debits wallets, and fulfills access — never scattered
 * across controllers.
 */
class PurchaseService
{
    public function __construct(
        private WalletService $wallet,
        private SubscriptionService $subscriptions,
        private ReferralService $referrals,
    ) {}

    /**
     * @return array{redirect_url: ?string, order: Order}
     */
    public function purchaseBook(Student $student, Book $book, string $method, ?PaymentGatewayContract $gateway = null): array
    {
        if ($book->is_free) {
            throw new \RuntimeException('This book is free — no purchase needed.');
        }

        $order = $this->createOrder($student, 'book', $book->id, $book->price, $method);

        if ($method === 'wallet') {
            $this->wallet->debit($student, (float) $book->price, 'book_purchase', $order->order_number, "Purchased: {$book->title}");
            $order->update(['status' => 'completed']);
            $this->fulfill($order);

            return ['redirect_url' => null, 'order' => $order];
        }

        // Gateway (SSLCommerz) — order stays 'pending' until the callback verifies it.
        $redirectUrl = $gateway->initiate($order);

        return ['redirect_url' => $redirectUrl, 'order' => $order];
    }

    public function purchaseCourse(Student $student, Course $course, string $method, ?PaymentGatewayContract $gateway = null): array
    {
        $order = $this->createOrder($student, 'course', $course->id, $course->price, $method);

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
        $amount = $plan->price_monthly * $months;
        $order = $this->createOrder($student, 'subscription', $plan->id, $amount, $method, ['months' => $months]);

        if ($method === 'wallet') {
            $this->wallet->debit($student, $amount, 'subscription_purchase', $order->order_number, "Subscribed: {$plan->name} ({$months}mo)");
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
     * Called from the SSLCommerz success callback after verify() has
     * confirmed the transaction server-side. Idempotent — completing an
     * already-completed order is a safe no-op.
     */
    public function completeGatewayOrder(Order $order, string $gatewayTransactionId): void
    {
        if ($order->status === 'completed') {
            return;
        }

        DB::transaction(function () use ($order, $gatewayTransactionId) {
            $order->update(['status' => 'completed', 'gateway_transaction_id' => $gatewayTransactionId]);
            $this->fulfill($order);
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
            'course' => $order->student->courseEnrollments()->firstOrCreate(
                ['course_id' => $order->orderable_id],
                ['progress_percentage' => 0]
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
            default => null,
        };

        if ($order->orderable_type !== 'wallet_recharge') {
            $this->referrals->rewardIfEligible($order->student);
            Mail::to($order->student->email)->send(new OrderConfirmationMail($order));
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
