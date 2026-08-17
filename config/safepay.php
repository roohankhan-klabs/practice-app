<?php

return [

    'base_url' => env(
        'SAFEPAY_BASE_URL',
        'https://sandbox.api.getsafepay.com'
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

];
