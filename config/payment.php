<?php

declare(strict_types=1);

use App\Modules\Payment\Gateways\Offline\OfflineGateway;
use App\Modules\Payment\Gateways\Bkash\BkashGateway;
use App\Modules\Payment\Gateways\SSLCommerz\SSLCommerzGateway;

return [
    'currency' => env('PAYMENT_CURRENCY', 'BDT'),

    'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'offline'),

    'gateways' => [
        'offline' => OfflineGateway::class,
        'sslcommerz' => SSLCommerzGateway::class,
        'bkash' => BkashGateway::class,
    ],

    'bkash' => [
        'base_url' => env('BKASH_BASE_URL', ''),
        'app_key' => env('BKASH_APP_KEY', ''),
        'app_secret' => env('BKASH_APP_SECRET', ''),
        'username' => env('BKASH_USERNAME', ''),
        'password' => env('BKASH_PASSWORD', ''),
        'callback_url' => env('BKASH_CALLBACK_URL', env('APP_URL', 'http://localhost')),
        'mode' => env('BKASH_MODE', '0011'),
        'intent' => env('BKASH_INTENT', 'authorization'),
        'currency' => env('BKASH_CURRENCY', 'BDT'),
        'merchant_invoice_prefix' => env('BKASH_MERCHANT_INVOICE_PREFIX', 'BK-'),
        'timeout' => (int) env('BKASH_TIMEOUT', 15),
    ],

    'sslcommerz' => [
        'sandbox' => (bool) env('SSLCOMMERZ_SANDBOX', true),
        'store_id' => env('SSLCOMMERZ_STORE_ID', ''),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD', ''),
    ],
];
