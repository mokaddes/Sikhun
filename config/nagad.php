<?php

return [
    'enabled' => env('NAGAD_MERCHANT_ENABLED', false),
    'sandbox' => env('NAGAD_SANDBOX', true),
    'base_url' => env('NAGAD_BASE_URL', 'http://sandbox.mynagad.com:10080/remote-payment-gateway-1.0/api/dfs'),
    'merchant_id' => env('NAGAD_MERCHANT_ID'),
    'merchant_number' => env('NAGAD_MERCHANT_NUMBER'),
    'public_key' => env('NAGAD_PUBLIC_KEY'),
    'private_key' => env('NAGAD_PRIVATE_KEY'),
];
