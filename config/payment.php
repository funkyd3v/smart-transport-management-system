<?php

declare(strict_types=1);

use App\Modules\Payment\Gateways\Offline\OfflineGateway;
use App\Modules\Payment\Gateways\SSLCommerz\SSLCommerzGateway;

return [
    'currency' => env('PAYMENT_CURRENCY', 'BDT'),

    'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'offline'),

    'gateways' => [
        'offline' => OfflineGateway::class,
        'sslcommerz' => SSLCommerzGateway::class,
    ],

    'sslcommerz' => [
        'sandbox' => (bool) env('SSLCOMMERZ_SANDBOX', true),
        'store_id' => env('SSLCOMMERZ_STORE_ID', ''),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD', ''),
    ],
];
