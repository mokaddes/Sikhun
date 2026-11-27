<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function index(Request $request): Response
    {
        $invoices = Invoice::query()
            ->where('student_id', $request->user('web')->getKey())
            ->withCount('items')
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(15);

        return Inertia::render('Student/Invoices/Index', [
            'invoices' => $invoices,
        ]);
    }

    public function show(Request $request, Invoice $invoice): Response
    {
        $this->authorizeOwner($request, $invoice);

        $invoice->load(['items', 'order']);

        return Inertia::render('Student/Invoices/Show', [
            'invoice' => $invoice,
        ]);
    }

    public function print(Request $request, Invoice $invoice)
    {
        $this->authorizeOwner($request, $invoice);

        $invoice->load(['items', 'student', 'order']);

        return view('invoices.print', [
            'invoice' => $invoice,
            'forAdmin' => false,
        ]);
    }

    public function pay(
        Request $request,
        Invoice $invoice,
        \App\Services\PurchaseService $purchases,
        \App\Services\Payment\PaymentGatewayResolver $resolver,
        \App\Services\WalletService $wallet,
        \App\Services\SubscriptionService $subscriptions,
        \App\Services\CouponService $coupons
    ): \Symfony\Component\HttpFoundation\Response {
        $this->authorizeOwner($request, $invoice);

        abort_unless(in_array($invoice->status, ['sent', 'overdue', 'draft'], true), 400, 'This invoice is already paid or cannot be paid.');

        $validated = $request->validate([
            'payment_method' => ['required', 'in:wallet,mhpay,bkash,nagad,coupon'],
            'coupon_code' => ['nullable', 'string', 'max:64'],
        ]);

        $student = $request->user('web');
        $method = $validated['payment_method'];
        $couponCode = trim((string) ($validated['coupon_code'] ?? ''));

        $payableTotal = (float) $invoice->total;
        $couponMeta = [];
        $appliedCoupon = null;

        if ($couponCode !== '') {
            try {
                $res = $coupons->validateAndCalculate($student, $couponCode, 'invoice', $invoice->id, $payableTotal);
                $payableTotal = $res['final_amount'];
                $appliedCoupon = $res;
                $couponMeta = [
                    'coupon_id' => $res['coupon_id'],
                    'coupon_code' => $res['code'],
                    'coupon_discount' => $res['discount_amount'],
                    'original_amount' => $res['subtotal'],
                    'coupon_target_type' => 'invoice',
                    'coupon_target_id' => $invoice->id,
                ];
            } catch (\RuntimeException $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        if ($method === 'coupon' && $payableTotal > 0) {
            return back()->with('error', 'এই কুপনটি পুরো ইনভয়েস মূল্য মওকুফ করেনি। বাকি টাকা পরিশোধের পদ্ধতি নির্বাচন করুন।');
        }

        if ($payableTotal <= 0 || $method === 'wallet') {
            if ($payableTotal > 0 && $student->wallet_balance < $payableTotal) {
                return back()->with('error', 'Insufficient wallet balance to pay this invoice.');
            }

            try {
                \Illuminate\Support\Facades\DB::transaction(function () use ($wallet, $student, $invoice, $subscriptions, $coupons, $payableTotal, $appliedCoupon) {
                    if ($payableTotal > 0) {
                        $wallet->debit(
                            $student,
                            $payableTotal,
                            'invoice_payment',
                            $invoice->invoice_number,
                            "Paid invoice {$invoice->invoice_number}"
                        );
                    }

                    $invUpdates = ['status' => 'paid'];
                    if ($appliedCoupon) {
                        $meta = is_array($invoice->meta) ? $invoice->meta : [];
                        $meta['coupon_id'] = $appliedCoupon['coupon_id'];
                        $meta['coupon_code'] = $appliedCoupon['code'];
                        $meta['coupon_discount'] = $appliedCoupon['discount_amount'];
                        $invUpdates['discount'] = round((float) $invoice->discount + $appliedCoupon['discount_amount'], 2);
                        $invUpdates['total'] = $payableTotal;
                        $invUpdates['meta'] = $meta;
                    }

                    $invoice->update($invUpdates);

                    if ($appliedCoupon) {
                        $coupons->consume(
                            $appliedCoupon['coupon_id'],
                            $student,
                            'invoice',
                            $invoice->id,
                            $appliedCoupon['discount_amount'],
                            null,
                            $invoice
                        );
                    }

                    if (! empty($invoice->meta['subscription_id'])) {
                        $sub = \App\Models\StudentSubscription::find($invoice->meta['subscription_id']);
                        if ($sub && $sub->status === 'active') {
                            $sub->update(['expires_at' => $sub->expires_at->addMonth()]);
                        } elseif ($sub) {
                            $subscriptions->assign($student, $sub->plan, 1);
                        }
                    } elseif (! empty($invoice->meta['plan_id'])) {
                        $plan = \App\Models\Plan::find($invoice->meta['plan_id']);
                        if ($plan) {
                            $subscriptions->assign($student, $plan, 1);
                        }
                    }

                    if (! empty($invoice->meta['course_id'])) {
                        $course = \App\Models\Course::find($invoice->meta['course_id']);
                        if ($course) {
                            $student->courseEnrollments()->firstOrCreate(
                                ['course_id' => $course->id],
                                ['progress_percentage' => 0]
                            );
                            app(\App\Services\CourseDeliveryService::class)->deliver($student, $course);
                        }
                    }

                    if (! empty($invoice->meta['book_id'])) {
                        $student->bookShelf()->firstOrCreate(
                            ['book_id' => (int) $invoice->meta['book_id']],
                            ['source' => 'purchased', 'added_at' => now()]
                        );
                    }
                });
            } catch (\RuntimeException $e) {
                return back()->with('error', $e->getMessage());
            }

            return back()->with('success', $payableTotal <= 0 ? 'কুপনের মাধ্যমে ইনভয়েসটি সম্পূর্ণ পরিশোধ হয়েছে!' : 'Invoice paid successfully via wallet!');
        }

        $gateway = $resolver->resolve($method);
        if (! $gateway) {
            return back()->with('error', 'Selected payment gateway is currently disabled.');
        }

        $order = \App\Models\Order::create([
            'student_id' => $student->id,
            'order_number' => 'SKH-INV-'.now()->format('ymd').'-'.strtoupper(\Illuminate\Support\Str::random(6)),
            'orderable_type' => 'invoice',
            'orderable_id' => $invoice->id,
            'amount' => $payableTotal,
            'payment_method' => $method,
            'status' => 'pending',
            'meta' => array_merge(['invoice_id' => $invoice->id, 'invoice_number' => $invoice->invoice_number], $couponMeta),
        ]);

        $redirectUrl = $gateway->initiate($order);

        return \Inertia\Inertia::location($redirectUrl);
    }

    private function authorizeOwner(Request $request, Invoice $invoice): void
    {
        abort_unless((int) $invoice->student_id === (int) $request->user('web')->getKey(), 403);
    }
}
