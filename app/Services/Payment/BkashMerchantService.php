<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Services\SiteSettingService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Direct bKash Tokenized Checkout Merchant integration.
 * Off by default; enabled and configured via Admin Settings once Merchant ID & keys are ready.
 */
class BkashMerchantService implements PaymentGatewayContract
{
    public function __construct(protected SiteSettingService $settings) {}

    public function isEnabled(): bool
    {
        $flag = $this->settings->get('payment_bkash_enabled');
        if ($flag === null) {
            return (bool) config('bkash.enabled', false);
        }

        return filter_var($flag, FILTER_VALIDATE_BOOLEAN);
    }

    private function sandbox(): bool
    {
        $flag = $this->settings->get('payment_bkash_sandbox');
        if ($flag === null) {
            return (bool) config('bkash.sandbox', true);
        }

        return filter_var($flag, FILTER_VALIDATE_BOOLEAN);
    }

    private function baseUrl(): string
    {
        $custom = trim((string) $this->settings->get('payment_bkash_base_url', ''));
        if ($custom !== '') {
            return rtrim($custom, '/');
        }

        return $this->sandbox()
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta';
    }

    private function credentials(): array
    {
        return [
            'app_key' => (string) ($this->settings->get('payment_bkash_app_key') ?: config('bkash.app_key')),
            'app_secret' => (string) ($this->settings->get('payment_bkash_app_secret') ?: config('bkash.app_secret')),
            'username' => (string) ($this->settings->get('payment_bkash_username') ?: config('bkash.username')),
            'password' => (string) ($this->settings->get('payment_bkash_password') ?: config('bkash.password')),
        ];
    }

    public function grantToken(): string
    {
        $creds = $this->credentials();
        if (! $creds['app_key'] || ! $creds['app_secret'] || ! $creds['username'] || ! $creds['password']) {
            throw new \RuntimeException('bKash Merchant credentials are not configured in Admin Settings.');
        }

        return Cache::remember('bkash_merchant_id_token', 3000, function () use ($creds) {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'username' => $creds['username'],
                'password' => $creds['password'],
            ])->post($this->baseUrl().'/tokenized/checkout/token/grant', [
                'app_key' => $creds['app_key'],
                'app_secret' => $creds['app_secret'],
            ]);

            if (! $response->successful() || ! $response->json('id_token')) {
                Log::error('bKash token grant failed', ['status' => $response->status(), 'body' => $response->json() ?? $response->body()]);
                throw new \RuntimeException('Failed to authenticate with bKash Merchant API.');
            }

            return (string) $response->json('id_token');
        });
    }

    public function initiate(Order $order): string
    {
        if (! $this->isEnabled()) {
            throw new \RuntimeException('bKash Merchant payment is currently disabled.');
        }

        $creds = $this->credentials();
        $token = $this->grantToken();

        $callbackUrl = route('api.webhooks.bkash').'?order_number='.urlencode((string) $order->order_number);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'authorization' => $token,
            'x-app-key' => $creds['app_key'],
        ])->post($this->baseUrl().'/tokenized/checkout/create', [
            'mode' => '0011',
            'payerReference' => (string) ($order->student?->phone ?: $order->student?->email ?: $order->student_id),
            'callbackURL' => $callbackUrl,
            'amount' => number_format((float) $order->amount, 2, '.', ''),
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => (string) $order->order_number,
        ]);

        $data = $response->json();
        if (! $response->successful() || empty($data['bkashURL']) || empty($data['paymentID'])) {
            Log::error('bKash create payment failed', ['status' => $response->status(), 'response' => $data]);
            throw new \RuntimeException('Could not initialize bKash payment: '.($data['statusMessage'] ?? 'Gateway error'));
        }

        $order->update([
            'gateway_invoice_id' => $data['paymentID'],
            'payment_method' => 'bkash',
        ]);

        return (string) $data['bkashURL'];
    }

    public function executePayment(string $paymentId): ?array
    {
        $creds = $this->credentials();
        $token = $this->grantToken();

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'authorization' => $token,
            'x-app-key' => $creds['app_key'],
        ])->post($this->baseUrl().'/tokenized/checkout/execute', [
            'paymentID' => $paymentId,
        ]);

        if (! $response->successful()) {
            return null;
        }

        $data = $response->json();

        return ($data['transactionStatus'] ?? null) === 'Completed' ? $data : null;
    }

    public function verify(array $payload): bool
    {
        $paymentId = $payload['paymentID'] ?? $payload['payment_id'] ?? null;
        if (! $paymentId) {
            return false;
        }

        return $this->queryPayment($paymentId) !== null;
    }

    public function queryPayment(string $paymentId): ?array
    {
        try {
            $creds = $this->credentials();
            $token = $this->grantToken();

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'authorization' => $token,
                'x-app-key' => $creds['app_key'],
            ])->post($this->baseUrl().'/tokenized/checkout/payment/status', [
                'paymentID' => $paymentId,
            ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            return ($data['transactionStatus'] ?? null) === 'Completed' ? $data : null;
        } catch (\Throwable $e) {
            Log::error('bKash queryPayment failed', ['error' => $e->getMessage()]);

            return null;
        }
    }
}
