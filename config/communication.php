<?php

declare(strict_types=1);

use App\Modules\Communication\Channels\Email\Providers\EmailChannelService;
use App\Modules\Communication\Channels\InApp\Services\InAppChannelService;
use App\Modules\Communication\Channels\Push\Providers\PushChannelService;
use App\Modules\Communication\Channels\SMS\Providers\BulkSmsBdProvider;
use App\Modules\Communication\Channels\SMS\Providers\TwilioSmsProvider;
use App\Modules\Communication\Channels\SMS\Services\SmsChannelService;
use App\Modules\Communication\Channels\WhatsApp\Providers\WhatsAppChannelService;

return [
    'default_providers' => [
        'sms' => env('COMMUNICATION_SMS_PROVIDER', env('COMM_SMS_DEFAULT_PROVIDER', 'twilio')),
        'whatsapp' => env('COMM_WHATSAPP_DEFAULT_PROVIDER', ''),
        'email' => env('COMM_EMAIL_DEFAULT_PROVIDER', ''),
        'push' => env('COMM_PUSH_DEFAULT_PROVIDER', ''),
        'in_app' => env('COMM_INAPP_DEFAULT_PROVIDER', ''),
    ],

    'channels_map' => [
        'sms' => SmsChannelService::class,
        'whatsapp' => WhatsAppChannelService::class,
        'email' => EmailChannelService::class,
        'push' => PushChannelService::class,
        'in_app' => InAppChannelService::class,
    ],

    'providers_map' => [
        'sms' => [
            'twilio' => TwilioSmsProvider::class,
            'bulksmsbd' => BulkSmsBdProvider::class,
        ],
    ],

    'providers' => [
        'sms' => [
            'twilio' => [
                'account_sid' => env('TWILIO_ACCOUNT_SID', ''),
                'auth_token' => env('TWILIO_AUTH_TOKEN', ''),
                'from' => env('TWILIO_SMS_FROM', ''),
                'timeout' => (int) env('TWILIO_SMS_TIMEOUT', 10),
                'connect_timeout' => (int) env('TWILIO_SMS_CONNECT_TIMEOUT', 5),
                'retry_times' => (int) env('TWILIO_SMS_RETRY_TIMES', 2),
                'retry_sleep_ms' => (int) env('TWILIO_SMS_RETRY_SLEEP_MS', 250),
            ],
            'bulksmsbd' => [
                'endpoint' => env('BULKSMSBD_ENDPOINT', 'http://bulksmsbd.net/api/smsapi'),
                'api_key' => env('BULKSMSBD_API_KEY', ''),
                'sender_id' => env('BULKSMSBD_SENDER_ID', ''),
                'timeout' => (int) env('BULKSMSBD_TIMEOUT', 10),
                'connect_timeout' => (int) env('BULKSMSBD_CONNECT_TIMEOUT', 5),
                'retry_times' => (int) env('BULKSMSBD_RETRY_TIMES', 2),
                'retry_sleep_ms' => (int) env('BULKSMSBD_RETRY_SLEEP_MS', 250),
            ],
        ],
    ],

    'otp' => [
        'max_generate_per_window' => (int) env('COMM_OTP_MAX_GENERATE_PER_WINDOW', 5),
        'generate_window_minutes' => (int) env('COMM_OTP_GENERATE_WINDOW_MINUTES', 10),
        'max_verify_attempts' => (int) env('COMM_OTP_MAX_VERIFY_ATTEMPTS', 5),
    ],
];
