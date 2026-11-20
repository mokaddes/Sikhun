<?php

namespace App\Services\Payment;

use App\Services\SiteSettingService;

class PaymentGatewayResolver
{
    public function __construct(protected SiteSettingService $settings) {}

    public function resolve(?string $method): ?PaymentGatewayContract
    {
        return match ($method) {
            'mhpay' => $this->isMhpayEnabled() ? app(MhpayService::class) : null,
            'bkash' => app(BkashMerchantService::class)->isEnabled() ? app(BkashMerchantService::class) : null,
            'nagad' => app(NagadMerchantService::class)->isEnabled() ? app(NagadMerchantService::class) : null,
            default => null,
        };
    }

    public function isMhpayEnabled(): bool
    {
        $flag = $this->settings->get('payment_mhpay_enabled');

        return $flag === null ? true : filter_var($flag, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Enabled gateway identifiers for frontend & mobile checkout selectors.
     *
     * @return array<int, array{key: string, label: string, description: string}>
     */
    public function enabledMethods(): array
    {
        $methods = [];

        if ($this->isMhpayEnabled()) {
            $methods[] = [
                'key' => 'mhpay',
                'label' => 'MHPay (bKash / Nagad / Rocket)',
                'description' => 'bKash, Nagad, Rocket & Upay via MHPay',
            ];
        }

        if (app(BkashMerchantService::class)->isEnabled()) {
            $methods[] = [
                'key' => 'bkash',
                'label' => 'bKash Merchant',
                'description' => 'Direct bKash Checkout',
            ];
        }

        if (app(NagadMerchantService::class)->isEnabled()) {
            $methods[] = [
                'key' => 'nagad',
                'label' => 'Nagad Merchant',
                'description' => 'Direct Nagad Checkout',
            ];
        }

        return $methods;
    }
}
