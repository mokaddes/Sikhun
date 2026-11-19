<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Services\Payment\BkashMerchantService;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class BkashWebhookController extends BaseApiController
{
    public function handle(Request $request, PurchaseService $purchases, BkashMerchantService $bkash): Response
    {
        $paymentId = $request->input('paymentID') ?? $request->query('paymentID');
        $status = strtolower((string) ($request->input('status') ?? $request->query('status', '')));
        $orderNumber = $request->input('order_number') ?? $request->query('order_number');

        if (! $paymentId && ! $orderNumber) {
            return $this->error('paymentID or order_number is required.', [], 422);
        }

        $order = Order::when($orderNumber, fn ($q) => $q->where('order_number', $orderNumber))
            ->when($paymentId, fn ($q) => $q->orWhere('gateway_invoice_id', $paymentId))
            ->first();

        if (! $order) {
            Log::warning('bKash callback for unknown order', ['paymentID' => $paymentId, 'order_number' => $orderNumber]);

            return $this->success(['status' => 'ignored'], 'Order not found.');
        }

        if ($order->status === 'completed') {
            return $request->isMethod('GET')
                ? redirect()->route('wallet.gateway.success', ['order_number' => $order->order_number])
                : $this->success(['status' => 'completed'], 'Already processed.');
        }

        if (in_array($status, ['cancel', 'cancelled', 'failure', 'failed'], true)) {
            $order->update(['status' => 'failed']);

            return $request->isMethod('GET')
                ? redirect()->route('wallet.gateway.cancel', ['order_number' => $order->order_number])
                : $this->success(['status' => 'failed'], 'Payment cancelled or failed.');
        }

        $targetPaymentId = $paymentId ?: $order->gateway_invoice_id;
        $executed = $bkash->executePayment((string) $targetPaymentId) ?? $bkash->queryPayment((string) $targetPaymentId);

        if (! $executed) {
            return $request->isMethod('GET')
                ? redirect()->route('wallet.gateway.fail', ['order_number' => $order->order_number])
                : $this->success(['status' => 'pending'], 'Payment not verified yet.');
        }

        $trxId = $executed['trxID'] ?? $targetPaymentId;
        $purchases->completeGatewayOrder($order, (string) $trxId);

        return $request->isMethod('GET')
            ? redirect()->route('wallet.gateway.success', ['order_number' => $order->order_number])
            : $this->success(['status' => 'completed', 'trx_id' => $trxId]);
    }
}
