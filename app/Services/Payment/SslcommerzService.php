<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Real SSLCommerz sandbox/live integration (session API v4 + validator API).
 * Requires SSLCOMMERZ_STORE_ID / SSLCOMMERZ_STORE_PASSWORD in .env — get free
 * sandbox credentials at https://developer.sslcommerz.com.
 *
 * Flow: initiate() posts to the session API and returns SSLCommerz's hosted
 * GatewayPageURL. The student pays there, SSLCommerz redirects back to our
 * success/fail/cancel routes AND fires an async IPN — verify() re-checks the
 * transaction server-side via the validator API before we ever trust it,
 * since the browser redirect alone is spoofable.
 */
class SslcommerzService implements PaymentGatewayContract
{
    public function initiate(Order $order): string
    {
        $response = Http::asForm()->post(config('sslcommerz.session_url'), [
            'store_id' => config('sslcommerz.store_id'),
            'store_passwd' => config('sslcommerz.store_password'),
            'total_amount' => $order->amount,
            'currency' => 'BDT',
            'tran_id' => $order->order_number,
            'success_url' => route('wallet.gateway.success'),
            'fail_url' => route('wallet.gateway.fail'),
            'cancel_url' => route('wallet.gateway.cancel'),
            'cus_name' => $order->student->name,
            'cus_email' => $order->student->email,
            'cus_add1' => 'Dhaka',
            'cus_phone' => '01700000000',
            'cus_country' => 'Bangladesh',
            'shipping_method' => 'NO',
            'product_name' => ucfirst(str_replace('_', ' ', $order->orderable_type)),
            'product_category' => 'Education',
            'product_profile' => 'general',
        ]);

        $data = $response->json();

        if (($data['status'] ?? null) !== 'SUCCESS') {
            Log::warning('SSLCommerz session init failed', ['response' => $data]);
            throw new \RuntimeException($data['failedreason'] ?? 'Could not start payment session.');
        }

        return $data['GatewayPageURL'];
    }

    public function verify(array $payload): bool
    {
        $valId = $payload['val_id'] ?? null;

        if (! $valId) {
            return false;
        }

        $response = Http::get(config('sslcommerz.validation_url'), [
            'val_id' => $valId,
            'store_id' => config('sslcommerz.store_id'),
            'store_passwd' => config('sslcommerz.store_password'),
            'format' => 'json',
        ]);

        $data = $response->json();

        return in_array($data['status'] ?? null, ['VALID', 'VALIDATED'], true);
    }
}
