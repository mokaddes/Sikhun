<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * MHPay Bangladesh MFS payment gateway integration.
 * Supports automated transaction detection, matching, and authoritative verification
 * for bKash, Nagad, Rocket, Upay, and tap.
 */
class MhpayService implements PaymentGatewayContract
{
    private function baseUrl(): string
    {
        return rtrim(config('mhpay.api_url', 'https://mhpay.sikhun.com'), '/');
    }

    private function apiKey(): string
    {
        return (string) config('mhpay.api_key');
    }

    public function initiate(Order $order): string
    {
        $amountMinor = (int) round(((float) $order->amount) * 100);

        $payload = [
            'reference' => (string) $order->order_number,
            'amount_minor' => $amountMinor,
            'currency' => 'BDT',
            'customer_ref' => $order->student?->email ?? $order->student?->name ?? 'Student',
            'metadata' => [
                'order_number' => $order->order_number,
                'student_id' => $order->student_id,
                'return_url' => route('wallet.gateway.success') . '?order_number=' . $order->order_number,
                'cancel_url' => route('wallet.gateway.cancel') . '?order_number=' . $order->order_number,
            ],
        ];

        $response = Http::withToken($this->apiKey())
            ->acceptJson()
            ->asJson()
            ->post($this->baseUrl() . '/api/v1/invoices', $payload);

        if (! $response->successful()) {
            Log::error('MHPay invoice creation failed', [
                'status' => $response->status(),
                'response' => $response->json() ?? $response->body(),
                'payload' => $payload,
            ]);
            throw new \RuntimeException('Could not initialize MHPay payment session: ' . ($response->json('message') ?? 'Provider error'));
        }

        $data = $response->json();
        $invoiceId = $data['id'] ?? null;

        if (! $invoiceId) {
            throw new \RuntimeException('MHPay did not return a valid invoice identifier.');
        }

        $order->update([
            'gateway_invoice_id' => $invoiceId,
            'payment_method' => 'mhpay',
        ]);

        return $this->baseUrl() . '/checkout/pay/' . $invoiceId;
    }

    public function verify(array $payload): bool
    {
        $invoiceId = $payload['invoice_id'] ?? null;
        return $this->verifiedData($invoiceId) !== null;
    }

    /**
     * Re-verify payment authoritatively with MHPay API server-side.
     */
    public function verifiedData(?string $invoiceId): ?array
    {
        if (! $invoiceId) {
            return null;
        }

        try {
            $response = Http::withToken($this->apiKey())
                ->acceptJson()
                ->get($this->baseUrl() . '/api/v1/invoices/' . $invoiceId . '/status');

            if (! $response->successful()) {
                Log::warning('MHPay verify failed with status ' . $response->status(), ['invoice_id' => $invoiceId]);
                return null;
            }

            $data = $response->json();

            if (($data['status'] ?? null) !== 'paid') {
                Log::info('MHPay invoice status not paid yet', ['invoice_id' => $invoiceId, 'status' => $data['status'] ?? null]);
                return null;
            }

            return $data;
        } catch (\Throwable $e) {
            Log::error('MHPay verification exception', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
