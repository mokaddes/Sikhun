<?php

return [
    'api_key' => env('ZINIPAY_API_KEY'),
    'is_sandbox' => env('ZINIPAY_IS_SANDBOX', true),

    // Same base URL for sandbox and live; the key type decides the environment.
    'api_url' => env('ZINIPAY_API_URL', 'https://api.zinipay.com'),
];