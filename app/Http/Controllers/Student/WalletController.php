<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\RechargeRequest;
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

    public function recharge(RechargeRequest $request, PurchaseService $purchases, ZinipayService $zinipay): RedirectResponse
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

        return redirect()->away($result['redirect_url']);
    }

    /**
     * ZiniPay redirects the browser here after payment. We never trust
     * this alone — verify() re-checks the transaction server-side before
     * any order is marked completed.
     */
    public function gatewaySuccess(Request $request, PurchaseService $purchases, ZinipayService $zinipay): RedirectResponse
    {
        $order = Order::where('order_number', $request->input('order_number'))
            ->orWhere('gateway_invoice_id', $request->input('invoice_id'))
            ->first();

        if (! $order || ! $order->gateway_invoice_id) {
            return redirect()->route('wallet.index')->with('error', 'Payment could not be verified.');
        }

        $verified = $zinipay->verifiedData($order->gateway_invoice_id);

        if (! $verified) {
            return redirect()->route('wallet.index')->with('error', 'Payment could not be verified.');
        }

        $purchases->completeGatewayOrder($order, $verified['transaction_id'] ?? $order->gateway_invoice_id);

        return redirect()->route('wallet.index')->with('success', 'Payment successful!');
    }

    public function gatewayFail(): RedirectResponse
    {
        return redirect()->route('wallet.index')->with('error', 'Payment failed. Please try again.');
    }

    public function gatewayCancel(): RedirectResponse
    {
        return redirect()->route('wallet.index')->with('error', 'Payment cancelled.');
    }
}
