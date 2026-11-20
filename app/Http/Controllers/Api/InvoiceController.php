<?php

namespace App\Http\Controllers\Api;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Student;
use App\Models\StudentSubscription;
use App\Services\Payment\PaymentGatewayResolver;
use App\Services\SubscriptionService;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InvoiceController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $invoices = Invoice::query()
            ->where('student_id', auth('sanctum')->id())
            ->withCount('items')
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(15);

        return $this->success($invoices);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        abort_unless((int) $invoice->student_id === (int) auth('sanctum')->id(), 404);
        $invoice->load(['items', 'order']);

        return $this->success($invoice);
    }

    public function pay(
        Request $request,
        Invoice $invoice,
        PaymentGatewayResolver $resolver,
        WalletService $wallet,
        SubscriptionService $subscriptions
    ): JsonResponse {
        abort_unless((int) $invoice->student_id === (int) auth('sanctum')->id(), 404);
        abort_unless(in_array($invoice->status, ['sent', 'overdue', 'draft'], true), 400, 'This invoice is already paid or cannot be paid.');

        $validated = $request->validate([
            'payment_method' => ['required', 'in:wallet,mhpay,bkash,nagad'],
        ]);

        /** @var Student $student */
        $student = auth('sanctum')->user();
        $method = $validated['payment_method'];

        if ($method === 'wallet') {
            if ($student->wallet_balance < $invoice->total) {
                return $this->error('Insufficient wallet balance to pay this invoice.', [], 422);
            }

            DB::transaction(function () use ($wallet, $student, $invoice, $subscriptions) {
                $wallet->debit(
                    $student,
                    (float) $invoice->total,
                    'invoice_payment',
                    $invoice->invoice_number,
                    "Paid invoice {$invoice->invoice_number}"
                );

                $invoice->update(['status' => 'paid']);

                if (! empty($invoice->meta['subscription_id'])) {
                    $sub = StudentSubscription::find($invoice->meta['subscription_id']);
                    if ($sub && $sub->status === 'active') {
                        $sub->update(['expires_at' => $sub->expires_at->addMonth()]);
                    } elseif ($sub) {
                        $subscriptions->assign($student, $sub->plan, 1);
                    }
                } elseif (! empty($invoice->meta['plan_id'])) {
                    $plan = Plan::find($invoice->meta['plan_id']);
                    if ($plan) {
                        $subscriptions->assign($student, $plan, 1);
                    }
                }
            });

            return $this->success(['invoice' => $invoice->fresh(), 'redirect_url' => null], 'Invoice paid successfully via wallet!');
        }

        $order = Order::create([
            'student_id' => $student->id,
            'order_number' => 'SKH-INV-'.now()->format('ymd').'-'.strtoupper(Str::random(6)),
            'orderable_type' => 'invoice',
            'orderable_id' => $invoice->id,
            'amount' => $invoice->total,
            'payment_method' => $method,
            'status' => 'pending',
            'meta' => ['invoice_id' => $invoice->id, 'invoice_number' => $invoice->invoice_number],
        ]);

        $gateway = $resolver->resolve($method);
        $redirectUrl = $gateway->initiate($order);

        return $this->success(['order' => $order, 'redirect_url' => $redirectUrl], 'Redirect to complete payment');
    }
}
