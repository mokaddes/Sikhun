<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\RechargeRequest;
use App\Models\Book;
use App\Models\Course;
use App\Models\Order;
use App\Services\Payment\MhpayService;
use App\Services\Payment\PaymentGatewayResolver;
use App\Services\Payment\ZinipayService;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WalletController extends Controller
{
    public function index(): Response
    {
        $student = auth('web')->user();

        $pendingOrders = $student->orders()
            ->where('status', 'pending')
            ->latest()
            ->get(['id', 'order_number', 'orderable_type', 'amount', 'payment_method', 'gateway_transaction_id', 'created_at']);

        return Inertia::render('Student/Wallet/Index', [
            'balance' => $student->wallet_balance,
            'transactions' => $student->walletTransactions()->latest()->paginate(20),
            'pendingOrders' => $pendingOrders,
        ]);
    }

    public function recharge(RechargeRequest $request, PurchaseService $purchases, PaymentGatewayResolver $resolver): \Symfony\Component\HttpFoundation\Response
    {
        $student = auth('web')->user();

        $gateway = $resolver->resolve($request->method);

        $result = $purchases->initiateWalletRecharge(
            $student,
            (float) $request->amount,
            $request->method,
            $gateway
        );

        if ($request->method === 'manual') {
            $result['order']->update(['gateway_transaction_id' => $request->transaction_reference]);

            return back()->with('success', 'Recharge request submitted — an admin will approve it once the transfer is confirmed.');
        }

        return Inertia::location($result['redirect_url']);
    }

    /**
     * Gateway redirects the browser here after payment (MHPay or ZiniPay).
     * We never trust this alone — verifiedData() re-checks the transaction
     * server-side before any order is marked completed.
     */
    public function gatewaySuccess(
        Request $request,
        PurchaseService $purchases,
        ZinipayService $zinipay,
        MhpayService $mhpay
    ): RedirectResponse {
        $order = Order::where('order_number', $request->input('order_number'))
            ->orWhere('gateway_invoice_id', $request->input('invoice_id'))
            ->first();

        if (! $order || ! $order->gateway_invoice_id) {
            return redirect()->route('home')->with('error', 'Payment could not be verified.');
        }

        $verified = null;
        $transactionId = $order->gateway_invoice_id;

        if ($order->payment_method === 'mhpay') {
            $verified = $mhpay->verifiedData($order->gateway_invoice_id);
            if ($verified) {
                $transactionId = $verified['id'] ?? $order->gateway_invoice_id;
            }
        } elseif ($order->payment_method === 'zinipay') {
            $verified = $zinipay->verifiedData($order->gateway_invoice_id);
            if ($verified) {
                $transactionId = $verified['transaction_id'] ?? $order->gateway_invoice_id;
            }
        } else {
            // Auto-detect based on which gateway accepts this invoice ID
            $verified = $mhpay->verifiedData($order->gateway_invoice_id);
            if ($verified) {
                $order->update(['payment_method' => 'mhpay']);
                $transactionId = $verified['id'] ?? $order->gateway_invoice_id;
            } else {
                $verified = $zinipay->verifiedData($order->gateway_invoice_id);
                if ($verified) {
                    $order->update(['payment_method' => 'zinipay']);
                    $transactionId = $verified['transaction_id'] ?? $order->gateway_invoice_id;
                }
            }
        }

        if (! $verified) {
            return $this->redirectAfterGateway($order, 'error', 'Payment could not be verified.');
        }

        $purchases->completeGatewayOrder($order, $transactionId);

        if ($order->orderable_type === 'course' && $order->orderable_id) {
            session(["guest_enrolled_course_{$order->orderable_id}" => true]);
        }

        return $this->redirectAfterGateway($order, 'success', 'Payment successful!');
    }

    public function gatewayFail(Request $request): RedirectResponse
    {
        $order = Order::where('order_number', $request->input('order_number'))->first();

        return $this->redirectAfterGateway($order, 'error', 'Payment failed. Please try again.');
    }

    public function gatewayCancel(Request $request): RedirectResponse
    {
        $order = Order::where('order_number', $request->input('order_number'))->first();

        return $this->redirectAfterGateway($order, 'error', 'Payment cancelled.');
    }

    private function redirectAfterGateway(?Order $order, string $flashKey, string $message): RedirectResponse
    {
        if ($order) {
            // Send the student back to the thing they actually bought.
            // Previously every non-course order landed on the wallet page,
            // which made a successful book purchase look like a wallet top-up.
            if ($order->orderable_type === 'course') {
                $course = Course::find($order->orderable_id);
                if ($course) {
                    return redirect()->route('courses.show', $course->slug)->with($flashKey, $message);
                }
            }

            if (in_array($order->orderable_type, ['book', 'book_chapter'], true)) {
                $bookId = $order->orderable_type === 'book'
                    ? $order->orderable_id
                    : ($order->meta['book_id'] ?? null);

                $book = $bookId ? Book::find($bookId) : null;

                if ($book) {
                    return redirect()->route('library.show', $book->slug)->with($flashKey, $message);
                }
            }

            if ($order->orderable_type === 'invoice' && $order->orderable_id) {
                return redirect()->route('invoices.show', $order->orderable_id)->with($flashKey, $message);
            }

            if ($order->orderable_type === 'subscription') {
                return redirect()->route('dashboard')->with($flashKey, $message);
            }

            if ($order->orderable_type === 'wallet_recharge' && auth('web')->check()) {
                return redirect()->route('wallet.index')->with($flashKey, $message);
            }
        }

        if (auth('web')->check()) {
            return redirect()->route('wallet.index')->with($flashKey, $message);
        }

        return redirect()->route('courses.index')->with($flashKey, $message);
    }

    /**
     * Submit or verify transaction reference for a pending order.
     */
    public function verifyOrder(
        Request $request,
        Order $order,
        PurchaseService $purchases,
        MhpayService $mhpay,
        ZinipayService $zinipay
    ): RedirectResponse {
        $student = auth('web')->user();
        abort_unless($order->student_id === $student->id, 403);
        abort_unless($order->status === 'pending', 400, 'Order is not pending.');

        $request->validate([
            'transaction_id' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        $trxId = trim($request->input('transaction_id'));
        $order->update(['gateway_transaction_id' => $trxId]);

        // Attempt automated verification if invoice ID is present
        if ($order->gateway_invoice_id) {
            $verified = null;
            if ($order->payment_method === 'mhpay') {
                $verified = $mhpay->verifiedData($order->gateway_invoice_id);
            } elseif ($order->payment_method === 'zinipay') {
                $verified = $zinipay->verifiedData($order->gateway_invoice_id);
            } else {
                $verified = $mhpay->verifiedData($order->gateway_invoice_id)
                    ?: $zinipay->verifiedData($order->gateway_invoice_id);
            }

            if ($verified) {
                $purchases->completeGatewayOrder($order, $trxId);
                return back()->with('success', 'পেমেন্ট সফলভাবে যাচাই হয়েছে এবং আপনার অর্ডারটি সম্পন্ন হয়েছে!');
            }
        }

        return back()->with('success', "আপনার ট্রানজ্যাকশন আইডি ({$trxId}) সংরক্ষণ করা হয়েছে। এডমিন ভেরিফাই করে দ্রুত অনুমোদন করবেন।");
    }
}
