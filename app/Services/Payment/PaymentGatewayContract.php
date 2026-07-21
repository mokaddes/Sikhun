<?php

namespace App\Services\Payment;

use App\Models\Order;

/**
 * Every gateway (SSLCommerz now; bKash/Nagad later) implements this so
 * PurchaseService never has to know which one it's talking to.
 */
interface PaymentGatewayContract
{
    /**
     * Kicks off a hosted checkout session for the given order and returns
     * the URL the browser should be redirected to.
     */
    public function initiate(Order $order): string;

    /**
     * Verifies a callback/IPN payload against the gateway's validation
     * API (never trust the redirect alone — always re-validate server-side).
     */
    public function verify(array $payload): bool;
}
