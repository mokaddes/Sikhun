<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Services\Payment\NagadMerchantService;
use App\Services\PurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class NagadWebhookController extends BaseApiController
{
    public function handle(Request $request, PurchaseService $purchases, NagadMerchantService $nagad): Response
    {
        $paymentRefId = $request->input('payment_ref_id') ?? $request->query('payment_ref_id');
        $orderNumber = $request->input('order_id') ?? $request->query('order_number');
        $status = strtolower((string) ($request->input('status') ?? $request->query('status', '')));

        if (! $paymentRefId && ! $orderNumber) {
            return $this->error('payment_ref_id or order_number is required.', [], 422);
        }

        $order = Order::when($orderNumber, fn ($q) => $q->where('order_number', $orderNumber))
            ->when($paymentRefId, fn ($q) => $q->orWhere('gateway_invoice_id', $paymentRefId))
            ->first();

        if (! $order) {
            Log::warning('Nagad callback for unknown order', ['payment_ref_id' => $paymentRefId, 'order_number' => $orderNumber]);

            return $this->success(['status' => 'ignored'], 'Order not found.');
        }

        if ($order->status === 'completed') {
            return $request->isMethod('GET')
                ? redirect()->route('wallet.gateway.success', ['order_number' => $order->order_number])
                : $this->success(['status' => 'completed'], 'Already processed.');
        }

        if (in_array($status, ['cancelled', 'aborted', 'failed'], true)) {
            $order->update(['status' => 'failed']);

            return $request->isMethod('GET')
                ? redirect()->route('wallet.gateway.cancel', ['order_number' => $order->order_number])
                : $this->success(['status' => 'failed'], 'Payment cancelled or failed.');
        }

        $targetRefId = $paymentRefId ?: $order->gateway_invoice_id;
        $verified = $nagad->verifiedData((string) $targetRefId);

        if (! $verified) {
            return $request->isMethod('GET')
                ? redirect()->route('wallet.gateway.fail', ['order_number' => $order->order_number])
                : $this->success(['status' => 'pending'], 'Payment not verified yet.');
        }

        $trxId = $verified['issuerPaymentRefNo'] ?? $targetRefId;
        $purchases->completeGatewayOrder($order, (string) $trxId);

        return $request->isMethod('GET')
            ? redirect()->route('wallet.gateway.success', ['order_number' => $order->order_number])
            : $this->success(['status' => 'completed', 'trx_id' => $trxId]);
    }
}
