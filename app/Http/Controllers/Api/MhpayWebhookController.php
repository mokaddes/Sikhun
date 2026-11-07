<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Services\Payment\MhpayService;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Server-to-server callback from MHPay.
 * MHPay dispatches HMAC-SHA256 signed webhooks for payment events (e.g. payment.paid).
 * Even with valid signature, we re-verify with MHPay status API before fulfillment.
 */
class MhpayWebhookController extends BaseApiController
{
    public function handle(Request $request, PurchaseService $purchases, MhpayService $mhpay): JsonResponse
    {
        Log::info('MHPay webhook received', [
            'method' => $request->method(),
            'event_id' => $request->header('X-Webhook-Id'),
            'has_sig' => (bool) $request->header('X-Webhook-Signature'),
            'payload' => $request->all() ?: json_decode(trim($request->getContent()), true),
        ]);

        $secret = config('mhpay.webhook_secret');
        $signatureHeader = $request->header('X-Webhook-Signature');

        if ($secret && $signatureHeader) {
            $rawBody = $request->getContent();
            $parts = explode(',', $signatureHeader);
            $timestamp = null;
            $signatures = [];

            foreach ($parts as $part) {
                $part = trim($part);
                if (str_starts_with($part, 't=')) {
                    $timestamp = (int) substr($part, 2);
                } elseif (str_starts_with($part, 'v1=')) {
                    $signatures[] = substr($part, 3);
                }
            }

            if (! $timestamp || empty($signatures) || abs(time() - $timestamp) > 300) {
                Log::warning('MHPay webhook rejected: stale or invalid signature timestamp');
                return $this->error('Stale or invalid signature', [], 403);
            }

            $expected = hash_hmac('sha256', $timestamp . '.' . $rawBody, $secret);
            $matched = false;
            foreach ($signatures as $sig) {
                if (hash_equals($expected, $sig)) {
                    $matched = true;
                    break;
                }
            }

            if (! $matched) {
                Log::warning('MHPay webhook rejected: signature mismatch');
                return $this->error('Unauthorized Action', [], 403);
            }
        }

        $body = $request->all() ?: json_decode(trim($request->getContent()), true) ?: [];

        $invoiceId = data_get($body, 'data.invoice.id')
            ?? data_get($body, 'invoice_id')
            ?? data_get($body, 'id');

        $orderNumber = data_get($body, 'data.invoice.reference')
            ?? data_get($body, 'reference')
            ?? data_get($body, 'data.metadata.order_number');

        if (! $invoiceId && ! $orderNumber) {
            Log::warning('MHPay webhook rejected: invoice_id / reference missing');
            return $this->error('Invoice reference missing.', [], 422);
        }

        $order = Order::when($orderNumber, fn ($q) => $q->where('order_number', $orderNumber))
            ->when($invoiceId, fn ($q) => $q->orWhere('gateway_invoice_id', $invoiceId))
            ->first();

        if (! $order) {
            Log::warning('MHPay webhook for unknown order', ['order_number' => $orderNumber, 'invoice_id' => $invoiceId]);
            return $this->success(['status' => 'ignored'], 'Order not found.');
        }

        if ($order->status === 'completed') {
            return $this->success(['status' => 'completed'], 'Already processed.');
        }

        // Authoritatively verify against MHPay's status endpoint
        $targetInvoiceId = $invoiceId ?? $order->gateway_invoice_id;
        $verified = $mhpay->verifiedData($targetInvoiceId);

        if (! $verified) {
            Log::info('MHPay webhook: invoice not yet verified by server', ['invoice_id' => $targetInvoiceId]);
            return $this->success(['status' => 'pending'], 'Invoice not yet verified.');
        }

        $transactionId = data_get($body, 'data.transaction.id')
            ?? data_get($verified, 'verification.transaction_id')
            ?? data_get($verified, 'id')
            ?? $targetInvoiceId;

        $purchases->completeGatewayOrder($order, $transactionId);

        Log::info('MHPay webhook: order successfully fulfilled', [
            'order_number' => $order->order_number,
            'invoice_id' => $targetInvoiceId,
            'transaction_id' => $transactionId,
        ]);

        return $this->success(['status' => 'completed']);
    }
}
