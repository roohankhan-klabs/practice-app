<?php

return [
    'environment' => env('SAFEPAY_ENV', 'sandbox'),

    'base_url' => env(
        'SAFEPAY_BASE_URL',
        env('SAFEPAY_ENV', 'sandbox') === 'production'
            ? 'https://api.getsafepay.com'
            : 'https://sandbox.api.getsafepay.com'
    ),

    'api_key' => env('SAFEPAY_API_KEY'),

    'merchant_secret' => env('SAFEPAY_MERCHANT_SECRET'),

    'webhook_secret' => env('SAFEPAY_WEBHOOK_SECRET'),

    'checkout_success_url' => env(
        'SAFEPAY_CHECKOUT_SUCCESS_URL',
        rtrim(env('APP_URL', 'http://localhost'), '/').'/safepay/success'
    ),

    'checkout_cancel_url' => env(
        'SAFEPAY_CHECKOUT_CANCEL_URL',
        rtrim(env('APP_URL', 'http://localhost'), '/').'/safepay/failed'
    ),

    'checkout_origin' => env(
        'SAFEPAY_CHECKOUT_ORIGIN',
        env('APP_URL', 'http://localhost')
    ),

];
