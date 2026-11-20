<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Student\RechargeRequest;
use App\Models\Order;
use App\Services\Payment\PaymentGatewayResolver;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends BaseApiController
{
    public function index(): JsonResponse
    {
        $student = auth('sanctum')->user();

        return $this->success([
            'balance' => $student->wallet_balance,
            'pending_orders' => $student->orders()
                ->where('status', 'pending')
                ->latest()
                ->get(['id', 'order_number', 'orderable_type', 'amount', 'payment_method', 'gateway_transaction_id', 'created_at']),
        ]);
    }

    public function transactions(): JsonResponse
    {
        return $this->success(auth('sanctum')->user()->walletTransactions()->latest()->paginate(20));
    }

    public function recharge(RechargeRequest $request, PurchaseService $purchases, PaymentGatewayResolver $resolver): JsonResponse
    {
        $student = auth('sanctum')->user();

        $gateway = $resolver->resolve($request->method);

        $result = $purchases->initiateWalletRecharge(
            $student,
            (float) $request->amount,
            $request->method,
            $gateway
        );

        if ($request->method === 'manual') {
            $result['order']->update(['gateway_transaction_id' => $request->transaction_reference]);
        }

        return $this->success($result);
    }

    public function verifyOrder(Request $request, Order $order, PurchaseService $purchases, PaymentGatewayResolver $resolver): JsonResponse
    {
        abort_unless((int) $order->student_id === (int) auth('sanctum')->id(), 403);

        if ($order->status === 'completed') {
            return $this->success($order, 'This order is already completed.');
        }

        $validated = $request->validate([
            'transaction_id' => ['nullable', 'string', 'max:120'],
        ]);

        if (! empty($validated['transaction_id'])) {
            $order->update(['gateway_transaction_id' => trim($validated['transaction_id'])]);
        }

        if (in_array($order->payment_method, ['mhpay', 'bkash', 'nagad'], true)) {
            $gateway = $resolver->resolve($order->payment_method);
            if ($gateway && $gateway->verify(['invoice_id' => $order->gateway_invoice_id, 'paymentID' => $order->gateway_invoice_id, 'payment_ref_id' => $order->gateway_invoice_id])) {
                $purchases->completeGatewayOrder($order, (string) ($order->gateway_transaction_id ?: $order->gateway_invoice_id ?: $order->order_number));

                return $this->success($order->fresh(), 'Payment verified and order completed!');
            }
        }

        return $this->success($order->fresh(), 'Transaction reference saved. Verification is pending.');
    }
}
