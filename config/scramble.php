<?php

use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Dedoc\Scramble\SecurityDocumentation\MiddlewareAuthSecurityStrategy;

return [
    'security_strategy' => [
        MiddlewareAuthSecurityStrategy::class,
        [
            'middleware' => ['auth', 'auth:*', 'auth:sanctum'],
            'scheme' => SecurityScheme::http('bearer'),
        ],
    ],
];
