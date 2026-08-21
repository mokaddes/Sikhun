<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Services\Payment\ZinipayService;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Server-to-server callback from ZiniPay. ZiniPay POSTs {invoice_id, ...}
 * here when a payment updates, so we can fulfill the order even if the
 * student closed their browser before the redirect fired.
 *
 * Auth follows the official example: ZiniPay sends our own API key back in
 * the Zinipay-Api-Key header — a request without the matching key is
 * rejected with 403. As a second layer, the invoice is ALWAYS re-verified
 * against ZiniPay's verify endpoint before anything is fulfilled, so even a
 * key leak can't forge a completion.
 */
class ZinipayWebhookController extends BaseApiController
{
    public function handle(Request $request, PurchaseService $purchases, ZinipayService $zinipay): JsonResponse
    {
        // First line of the method so EVERY hit is visible in logs. Never log
        // the key value itself — only whether it was present.
        Log::info('ZiniPay webhook received', [
            'method' => $request->method(),
            'has_key_header' => (bool) $request->header('Zinipay-Api-Key'),
            'body' => $request->all() ?: json_decode(trim($request->getContent()), true),
        ]);

        $headerKey = $request->header('Zinipay-Api-Key');

        if (! $headerKey) {
            Log::warning('ZiniPay webhook rejected: Zinipay-Api-Key header missing');
            return $this->error('Api key not found', [], 403);
        }

        if ($headerKey !== config('zinipay.api_key')) {
            Log::warning('ZiniPay webhook rejected: key mismatch');
            return $this->error('Unauthorized Action', [], 403);
        }

        $invoiceId = $request->input('invoice_id')
            ?? json_decode(trim($request->getContent()), true)['invoice_id']
            ?? null;

        if (! $invoiceId) {
            Log::warning('ZiniPay webhook rejected: invoice_id missing');
            return $this->error('invoice_id missing.', [], 422);
        }

        $order = Order::where('gateway_invoice_id', $invoiceId)->first();

        if (! $order) {
            // Acknowledge instead of 404 so ZiniPay doesn't retry an order
            // we will never recognise (e.g. created before this deploy).
            Log::warning('ZiniPay webhook for unknown invoice', ['invoice_id' => $invoiceId]);
            return $this->success(['status' => 'ignored'], 'Order not found.');
        }

        if ($order->status === 'completed') {
            return $this->success(['status' => 'completed'], 'Already processed.');
        }

        $verified = $zinipay->verifiedData($invoiceId);

        if (! $verified) {
            // 200 keeps ZiniPay from retry-storming; the student's success
            // redirect re-verifies and completes the order when it's paid.
            Log::info('ZiniPay webhook: invoice not completed yet', ['invoice_id' => $invoiceId]);
            return $this->success(['status' => 'pending'], 'Invoice not yet completed.');
        }

        $purchases->completeGatewayOrder($order, $verified['transaction_id'] ?? $invoiceId);
        Log::info('ZiniPay webhook: order completed', ['order_number' => $order->order_number, 'invoice_id' => $invoiceId]);

        return $this->success(['status' => 'completed']);
    }
}
