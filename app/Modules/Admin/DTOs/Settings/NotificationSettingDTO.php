<?php

declare(strict_types=1);

namespace App\Modules\Admin\DTOs\Settings;

use App\Modules\Admin\Http\Requests\Settings\UpdateNotificationSettingRequest;

final readonly class NotificationSettingDTO
{
    public function __construct(
        public bool $smsEnabled,
        public ?string $smsProvider,
        public ?string $smsApiKey,
        public ?string $smsSenderName,
        public bool $whatsappEnabled,
        public ?string $whatsappApiKey,
        public ?string $whatsappSenderNumber,
        public bool $lowStockAlertEnabled,
        public bool $duePaymentAlertEnabled,
        public bool $tripStatusAlertEnabled,
    ) {}

    public static function fromRequest(UpdateNotificationSettingRequest $request): self
    {
        return new self(
            smsEnabled: (bool) ($request->validated('sms_enabled') ?? false),
            smsProvider: $request->validated('sms_provider'),
            smsApiKey: $request->validated('sms_api_key'),
            smsSenderName: $request->validated('sms_sender_name'),
            whatsappEnabled: (bool) ($request->validated('whatsapp_enabled') ?? false),
            whatsappApiKey: $request->validated('whatsapp_api_key'),
            whatsappSenderNumber: $request->validated('whatsapp_sender_number'),
            lowStockAlertEnabled: (bool) ($request->validated('low_stock_alert_enabled') ?? false),
            duePaymentAlertEnabled: (bool) ($request->validated('due_payment_alert_enabled') ?? false),
            tripStatusAlertEnabled: (bool) ($request->validated('trip_status_alert_enabled') ?? false),
        );
    }
}
