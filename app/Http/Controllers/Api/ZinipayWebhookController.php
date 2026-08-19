<?php

namespace App\Http\Controllers\Api;

use App\Services\Payment\ZinipayService;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Server-to-server callback from ZiniPay. ZiniPay POSTs {invoice_id, status}
 * here when a payment updates, so we can fulfill the order even if the
 * student closed their browser before the redirect fired. Security note:
 * this payload is forgeable, so we ALWAYS re-verify the invoice against
 * ZiniPay's verify endpoint before fulfilling anything.
 */
class ZinipayWebhookController extends BaseApiController
{
    public function handle(Request $request, PurchaseService $purchases, ZinipayService $zinipay): JsonResponse
    {
        $order = \App\Models\Order::where('gateway_invoice_id', $request->input('invoice_id'))->first();

        if (! $order) {
            return $this->error('Order not found.', [], 404);
        }

        $verified = $zinipay->verifiedData($order->gateway_invoice_id);

        if (! $verified) {
            return $this->success(['status' => 'pending'], 'Invoice not yet completed.');
        }

        $purchases->completeGatewayOrder($order, $verified['transaction_id'] ?? $order->gateway_invoice_id);

        return $this->success(['status' => 'completed']);
    }
}