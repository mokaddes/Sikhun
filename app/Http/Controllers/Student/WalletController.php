<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\RechargeRequest;
use App\Models\Book;
use App\Models\Course;
use App\Models\Order;
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

        return Inertia::render('Student/Wallet/Index', [
            'balance' => $student->wallet_balance,
            'transactions' => $student->walletTransactions()->latest()->paginate(20),
        ]);
    }

    public function recharge(RechargeRequest $request, PurchaseService $purchases, ZinipayService $zinipay): \Symfony\Component\HttpFoundation\Response
    {
        $student = auth('web')->user();

        $result = $purchases->initiateWalletRecharge(
            $student,
            (float) $request->amount,
            $request->method,
            $request->method === 'zinipay' ? $zinipay : null
        );

        if ($request->method === 'manual') {
            $result['order']->update(['gateway_transaction_id' => $request->transaction_reference]);

            return back()->with('success', 'Recharge request submitted — an admin will approve it once the transfer is confirmed.');
        }

        return Inertia::location($result['redirect_url']);
    }

    /**
     * ZiniPay redirects the browser here after payment. We never trust
     * this alone — verify() re-checks the transaction server-side before
     * any order is marked completed.
     */
    public function gatewaySuccess(Request $request, PurchaseService $purchases, ZinipayService $zinipay): RedirectResponse
    {
        $orderNumber = $request->input('order_number');
        $invoiceId = $request->input('invoice_id');

        // Never run a where() with a null value: `gateway_invoice_id = null`
        // would match the first pending order that has no invoice yet.
        if (! $orderNumber && ! $invoiceId) {
            return redirect()->route('home')->with('error', 'Payment could not be verified.');
        }

        $order = Order::query()
            ->when($orderNumber, fn ($query) => $query->where('order_number', $orderNumber))
            ->when($invoiceId, fn ($query) => $query->where('gateway_invoice_id', $invoiceId))
            ->first();

        if (! $order || ! $order->gateway_invoice_id) {
            return redirect()->route('home')->with('error', 'Payment could not be verified.');
        }

        $verified = $zinipay->verifiedData($order->gateway_invoice_id);

        if (! $verified) {
            return $this->redirectAfterGateway($order, 'error', 'Payment could not be verified.');
        }

        $purchases->completeGatewayOrder($order, $verified['transaction_id'] ?? $order->gateway_invoice_id);

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

            if ($order->orderable_type === 'subscription') {
                return redirect()->route('dashboard')->with($flashKey, $message);
            }

            if ($order->orderable_type === 'wallet_recharge' && auth('web')->check()) {
                return redirect()->route('wallet.index')->with($flashKey, $message);
            }
        }

        if (auth('web')->check()) {
            return redirect()->route('dashboard')->with($flashKey, $message);
        }

        return redirect()->route('courses.index')->with($flashKey, $message);
    }
}
