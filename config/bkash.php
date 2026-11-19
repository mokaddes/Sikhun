<?php

return [
    'enabled' => env('BKASH_MERCHANT_ENABLED', false),
    'sandbox' => env('BKASH_SANDBOX', true),
    'base_url' => env('BKASH_BASE_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'),
    'app_key' => env('BKASH_APP_KEY'),
    'app_secret' => env('BKASH_APP_SECRET'),
    'username' => env('BKASH_USERNAME'),
    'password' => env('BKASH_PASSWORD'),
];
