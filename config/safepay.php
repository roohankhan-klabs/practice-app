<?php

return [

    'base_url' => env(
        'SAFEPAY_BASE_URL',
        'https://sandbox.api.getsafepay.com'
    ),

    'api_key' => env('SAFEPAY_API_KEY'),

    'merchant_secret' => env('SAFEPAY_MERCHANT_SECRET'),

    'webhook_secret' => env('SAFEPAY_WEBHOOK_SECRET'),

];
