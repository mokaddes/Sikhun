<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Services\SiteSettingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Direct Nagad Merchant PGW integration.
 * Off by default; enabled and configured via Admin Settings once Merchant ID & RSA keys are ready.
 */
class NagadMerchantService implements PaymentGatewayContract
{
    public function __construct(protected SiteSettingService $settings) {}

    public function isEnabled(): bool
    {
        $flag = $this->settings->get('payment_nagad_enabled');
        if ($flag === null) {
            return (bool) config('nagad.enabled', false);
        }

        return filter_var($flag, FILTER_VALIDATE_BOOLEAN);
    }

    private function sandbox(): bool
    {
        $flag = $this->settings->get('payment_nagad_sandbox');
        if ($flag === null) {
            return (bool) config('nagad.sandbox', true);
        }

        return filter_var($flag, FILTER_VALIDATE_BOOLEAN);
    }

    private function baseUrl(): string
    {
        $custom = trim((string) $this->settings->get('payment_nagad_base_url', ''));
        if ($custom !== '') {
            return rtrim($custom, '/');
        }

        return $this->sandbox()
            ? 'http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs'
            : 'https://api.mynagad.com/api/dfs';
    }

    private function credentials(): array
    {
        return [
            'merchant_id' => (string) ($this->settings->get('payment_nagad_merchant_id') ?: config('nagad.merchant_id')),
            'merchant_number' => (string) ($this->settings->get('payment_nagad_merchant_number') ?: config('nagad.merchant_number')),
            'public_key' => (string) ($this->settings->get('payment_nagad_public_key') ?: config('nagad.public_key')),
            'private_key' => (string) ($this->settings->get('payment_nagad_private_key') ?: config('nagad.private_key')),
        ];
    }

    public function initiate(Order $order): string
    {
        if (! $this->isEnabled()) {
            throw new \RuntimeException('Nagad Merchant payment is currently disabled.');
        }

        $creds = $this->credentials();
        if (! $creds['merchant_id'] || ! $creds['public_key'] || ! $creds['private_key']) {
            throw new \RuntimeException('Nagad Merchant credentials are not configured in Admin Settings.');
        }

        $dateTime = now('Asia/Dhaka')->format('YmdHis');
        $challenge = Str::random(40);
        $sensitiveData = [
            'merchantId' => $creds['merchant_id'],
            'datetime' => $dateTime,
            'orderId' => (string) $order->order_number,
            'challenge' => $challenge,
        ];

        $encrypted = $this->encryptWithPublicKey(json_encode($sensitiveData), $creds['public_key']);
        $signature = $this->signWithPrivateKey(json_encode($sensitiveData), $creds['private_key']);

        $initUrl = "{$this->baseUrl()}/check-out/initialize/{$creds['merchant_id']}/{$order->order_number}";
        $initResponse = Http::withHeaders($this->headers())->post($initUrl, [
            'accountNumber' => $creds['merchant_number'],
            'dateTime' => $dateTime,
            'sensitiveData' => $encrypted,
            'signature' => $signature,
        ]);

        $initData = $initResponse->json();
        if (! $initResponse->successful() || empty($initData['sensitiveData'])) {
            Log::error('Nagad initialize failed', ['status' => $initResponse->status(), 'response' => $initData]);
            throw new \RuntimeException('Could not initialize Nagad payment session.');
        }

        $decrypted = json_decode($this->decryptWithPrivateKey($initData['sensitiveData'], $creds['private_key']), true) ?: [];
        $paymentRefId = $decrypted['paymentReferenceId'] ?? null;
        $challengeFromNagad = $decrypted['challenge'] ?? $challenge;

        if (! $paymentRefId) {
            throw new \RuntimeException('Nagad did not return a payment reference ID.');
        }

        $completeSensitive = [
            'merchantId' => $creds['merchant_id'],
            'orderId' => (string) $order->order_number,
            'currencyCode' => '050',
            'amount' => number_format((float) $order->amount, 2, '.', ''),
            'challenge' => $challengeFromNagad,
        ];

        $completeUrl = "{$this->baseUrl()}/check-out/complete/{$paymentRefId}";
        $completeResponse = Http::withHeaders($this->headers())->post($completeUrl, [
            'sensitiveData' => $this->encryptWithPublicKey(json_encode($completeSensitive), $creds['public_key']),
            'signature' => $this->signWithPrivateKey(json_encode($completeSensitive), $creds['private_key']),
            'merchantCallbackURL' => route('api.webhooks.nagad').'?order_number='.urlencode((string) $order->order_number),
        ]);

        $completeData = $completeResponse->json();
        if (! $completeResponse->successful() || empty($completeData['callBackUrl'])) {
            Log::error('Nagad complete failed', ['status' => $completeResponse->status(), 'response' => $completeData]);
            throw new \RuntimeException('Could not complete Nagad checkout initialization.');
        }

        $order->update([
            'gateway_invoice_id' => $paymentRefId,
            'payment_method' => 'nagad',
        ]);

        return (string) $completeData['callBackUrl'];
    }

    public function verify(array $payload): bool
    {
        $paymentRefId = $payload['payment_ref_id'] ?? $payload['paymentReferenceId'] ?? null;

        return $this->verifiedData($paymentRefId) !== null;
    }

    public function verifiedData(?string $paymentRefId): ?array
    {
        if (! $paymentRefId) {
            return null;
        }

        try {
            $response = Http::withHeaders($this->headers())
                ->get("{$this->baseUrl()}/verify/payment/{$paymentRefId}");

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            return ($data['status'] ?? null) === 'Success' ? $data : null;
        } catch (\Throwable $e) {
            Log::error('Nagad verify failed', ['error' => $e->getMessage()]);

            return null;
        }
    }

    private function headers(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-KM-Api-Version' => 'v-0.2.0',
            'X-KM-IP-V4' => request()?->ip() ?? '127.0.0.1',
            'X-KM-Client-Type' => 'PC_WEB',
        ];
    }

    private function encryptWithPublicKey(string $data, string $publicKey): string
    {
        $pem = str_contains($publicKey, 'BEGIN PUBLIC KEY')
            ? $publicKey
            : "-----BEGIN PUBLIC KEY-----\n".chunk_split(trim($publicKey), 64, "\n")."-----END PUBLIC KEY-----";

        if (! openssl_public_encrypt($data, $encrypted, $pem)) {
            throw new \RuntimeException('Nagad RSA public key encryption failed.');
        }

        return base64_encode($encrypted);
    }

    private function decryptWithPrivateKey(string $data, string $privateKey): string
    {
        $pem = str_contains($privateKey, 'BEGIN')
            ? $privateKey
            : "-----BEGIN RSA PRIVATE KEY-----\n".chunk_split(trim($privateKey), 64, "\n")."-----END RSA PRIVATE KEY-----";

        if (! openssl_private_decrypt(base64_decode($data), $decrypted, $pem)) {
            throw new \RuntimeException('Nagad RSA private key decryption failed.');
        }

        return (string) $decrypted;
    }

    private function signWithPrivateKey(string $data, string $privateKey): string
    {
        $pem = str_contains($privateKey, 'BEGIN')
            ? $privateKey
            : "-----BEGIN RSA PRIVATE KEY-----\n".chunk_split(trim($privateKey), 64, "\n")."-----END RSA PRIVATE KEY-----";

        if (! openssl_sign($data, $signature, $pem, OPENSSL_ALGO_SHA256)) {
            throw new \RuntimeException('Nagad RSA signing failed.');
        }

        return base64_encode($signature);
    }
}
