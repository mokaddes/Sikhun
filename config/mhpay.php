<?php

return [
    'api_url' => rtrim(env('MHPAY_API_URL', 'https://mhpay.sikhun.com'), '/'),
    'api_key' => env('MHPAY_API_KEY'),
    'webhook_secret' => env('MHPAY_WEBHOOK_SECRET'),
];
