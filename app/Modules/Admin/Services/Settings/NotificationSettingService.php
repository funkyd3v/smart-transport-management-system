<?php

declare(strict_types=1);

namespace App\Modules\Admin\Services\Settings;

use App\Modules\Admin\DTOs\Settings\NotificationSettingDTO;
use App\Modules\Admin\Repositories\Settings\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

final class NotificationSettingService
{
    private const CACHE_KEY = 'settings.notification';

    public function __construct(private readonly SettingRepositoryInterface $repository) {}

    public function settings(): array
    {
        $settings = $this->rawSettings();

        return [
            ...$settings,
            'sms_api_key_masked' => $this->maskEncrypted($settings['sms_api_key'] ?? null),
            'whatsapp_api_key_masked' => $this->maskEncrypted($settings['whatsapp_api_key'] ?? null),
        ];
    }

    public function rawSettings(): array
    {
        return Cache::remember(self::CACHE_KEY, 86400, function (): array {
            $stored = $this->repository->getGroupValues('notification');
            $settings = array_merge($this->defaults(), $stored);

            return [
                'sms_enabled' => filter_var($settings['sms_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'sms_provider' => $settings['sms_provider'] ?? null,
                'sms_api_key' => $settings['sms_api_key'] ?? null,
                'sms_sender_name' => $settings['sms_sender_name'] ?? null,
                'whatsapp_enabled' => filter_var($settings['whatsapp_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'whatsapp_api_key' => $settings['whatsapp_api_key'] ?? null,
                'whatsapp_sender_number' => $settings['whatsapp_sender_number'] ?? null,
                'low_stock_alert_enabled' => filter_var($settings['low_stock_alert_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'due_payment_alert_enabled' => filter_var($settings['due_payment_alert_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'trip_status_alert_enabled' => filter_var($settings['trip_status_alert_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ];
        });
    }

    public function update(NotificationSettingDTO $dto): void
    {
        $existing = $this->rawSettings();

        $smsApiKey = $this->resolveEncryptedValue($dto->smsApiKey, $existing['sms_api_key'] ?? null);
        $whatsappApiKey = $this->resolveEncryptedValue($dto->whatsappApiKey, $existing['whatsapp_api_key'] ?? null);

        $this->repository->upsertGroup('notification', [
            'sms_enabled' => $dto->smsEnabled ? '1' : '0',
            'sms_provider' => $dto->smsProvider,
            'sms_api_key' => $smsApiKey,
            'sms_sender_name' => $dto->smsSenderName,
            'whatsapp_enabled' => $dto->whatsappEnabled ? '1' : '0',
            'whatsapp_api_key' => $whatsappApiKey,
            'whatsapp_sender_number' => $dto->whatsappSenderNumber,
            'low_stock_alert_enabled' => $dto->lowStockAlertEnabled ? '1' : '0',
            'due_payment_alert_enabled' => $dto->duePaymentAlertEnabled ? '1' : '0',
            'trip_status_alert_enabled' => $dto->tripStatusAlertEnabled ? '1' : '0',
        ]);

        Cache::forget(self::CACHE_KEY);
    }

    private function resolveEncryptedValue(?string $submittedValue, ?string $existingEncrypted): ?string
    {
        if ($submittedValue === null || $submittedValue === '') {
            return $existingEncrypted;
        }

        return encrypt($submittedValue);
    }

    private function maskEncrypted(?string $encrypted): ?string
    {
        if ($encrypted === null || $encrypted === '') {
            return null;
        }

        try {
            $plain = (string) decrypt($encrypted);
        } catch (\Throwable) {
            return null;
        }

        $suffix = substr($plain, -4);

        return '••••••••••••'.$suffix;
    }

    private function defaults(): array
    {
        return [
            'sms_enabled' => false,
            'sms_provider' => null,
            'sms_api_key' => null,
            'sms_sender_name' => null,
            'whatsapp_enabled' => false,
            'whatsapp_api_key' => null,
            'whatsapp_sender_number' => null,
            'low_stock_alert_enabled' => false,
            'due_payment_alert_enabled' => false,
            'trip_status_alert_enabled' => false,
        ];
    }
}
