<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ZiniPay hosted-invoice integration (https://zinipay.com/docs).
 * Requires ZINIPAY_API_KEY in .env — copy your Brand Key / API Key from the
 * dashboard (https://dash.zinipay.com, Brands menu).
 *
 * Flow: initiate() creates a hosted invoice and returns its payment_url. The
 * student pays on ZiniPay's checkout page, then ZiniPay redirects the browser
 * back to our success/cancel routes AND fires a server-side webhook. We never
 * trust the redirect alone — verify() re-checks the invoice via the verify
 * endpoint before any order is marked completed.
 */
class ZinipayService implements PaymentGatewayContract
{
    /**
     * The official examples authenticate different endpoints with different
     * header spellings (dash for checkout, underscore for verify). Send both
     * on every call so either handler finds what it expects.
     */
    private function headers(): array
    {
        return [
            'ZINIPAY-API-KEY' => config('zinipay.api_key'),
            'ZINIPAY_API_KEY' => config('zinipay.api_key'),
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ];
    }

    public function initiate(Order $order): string
    {
        $response = Http::withHeaders($this->headers())
            ->post(config('zinipay.api_url').'/v1/payment/create', [
                'cus_name' => $order->student->name,
                'cus_email' => $order->student->email,
                'amount' => (float) $order->amount,
                'metadata' => ['order_number' => $order->order_number],
                'redirect_url' => route('wallet.gateway.success').'?order_number='.$order->order_number,
                'cancel_url' => route('wallet.gateway.cancel').'?order_number='.$order->order_number,
                'webhook_url' => route('zinipay.webhook'),
            ]);

        $data = $response->json();

        if (($data['status'] ?? null) !== true) {
            Log::warning('ZiniPay invoice creation failed', ['response' => $data]);
            throw new \RuntimeException($data['message'] ?? 'Could not start payment session.');
        }

        // payment_url looks like https://secure.zinipay.com/payment/INVOICE_ID;
        // the API also echoes invoice_id directly — prefer that, fall back to
        // parsing it out of the payment_url.
        $invoiceId = $data['invoice_id'] ?? $data['invoiceId'] ?? $this->extractInvoiceId($data['payment_url']);
        $order->update(['gateway_invoice_id' => $invoiceId]);

        return $data['payment_url'];
    }

    public function verify(array $payload): bool
    {
        return $this->verifiedData($payload['invoice_id'] ?? null) !== null;
    }

    /**
     * Server-side verification of a ZiniPay invoice. Returns the full verify
     * response (including transaction_id) when the invoice is COMPLETED, or
     * null otherwise — never trust a browser redirect or webhook alone.
     */
    public function verifiedData(?string $invoiceId): ?array
    {
        if (! $invoiceId) {
            return null;
        }

        // Documented verification endpoint (see zinipay-example/app/Library/ZiniPay.php).
        $response = Http::withHeaders($this->headers())
            ->post(config('zinipay.api_url').'/api/verify-payment', [
                'invoice_id' => $invoiceId,
            ]);

        $data = $response->json();

        if (($data['status'] ?? null) !== 'COMPLETED') {
            Log::info('ZiniPay verify not completed', ['invoice_id' => $invoiceId, 'response' => $data]);
            return null;
        }

        return $data;
    }

    private function extractInvoiceId(string $paymentUrl): ?string
    {
        $path = parse_url($paymentUrl, PHP_URL_PATH);

        return $path ? basename($path) : null;
    }
}