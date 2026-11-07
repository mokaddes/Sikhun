<?php

namespace App\Services\Payment;

class PaymentGatewayResolver
{
    public function resolve(?string $method): ?PaymentGatewayContract
    {
        return match ($method) {
            'mhpay' => app(MhpayService::class),
            'zinipay' => app(ZinipayService::class),
            default => null,
        };
    }
}
