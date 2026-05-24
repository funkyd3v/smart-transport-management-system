<?php

declare(strict_types=1);

namespace App\Modules\Admin\Services\Settings;

use App\Modules\Admin\DTOs\Settings\FinancialSettingDTO;
use App\Modules\Admin\Repositories\Settings\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

final class FinancialSettingService
{
    private const CACHE_KEY = 'settings.financial';

    public function __construct(private readonly SettingRepositoryInterface $repository) {}

    public function settings(): array
    {
        return Cache::remember(self::CACHE_KEY, 86400, function (): array {
            $stored = $this->repository->getGroupValues('financial');
            $settings = array_merge($this->defaults(), $stored);

            if (is_string($settings['default_payment_methods'] ?? null)) {
                $decoded = json_decode($settings['default_payment_methods'], true);
                $settings['default_payment_methods'] = is_array($decoded) ? $decoded : [];
            }

            $settings['due_reminder_days'] = (int) ($settings['due_reminder_days'] ?? 7);
            $settings['tax_rate'] = $settings['tax_rate'] !== null ? (float) $settings['tax_rate'] : 0.0;

            return $settings;
        });
    }

    public function update(FinancialSettingDTO $dto): void
    {
        $this->repository->upsertGroup('financial', [
            'invoice_prefix' => $dto->invoicePrefix,
            'invoice_number_format' => $dto->invoiceNumberFormat,
            'default_payment_methods' => json_encode($dto->defaultPaymentMethods, JSON_THROW_ON_ERROR),
            'due_reminder_days' => (string) $dto->dueReminderDays,
            'tax_rate' => $dto->taxRate !== null ? (string) $dto->taxRate : '0',
            'fiscal_year_start' => $dto->fiscalYearStart,
        ]);

        Cache::forget(self::CACHE_KEY);
    }

    private function defaults(): array
    {
        return [
            'invoice_prefix' => 'INV',
            'invoice_number_format' => 'PREFIX-YEAR-SEQ',
            'default_payment_methods' => ['cash', 'bank_transfer'],
            'due_reminder_days' => 7,
            'tax_rate' => 0,
            'fiscal_year_start' => '01-01',
        ];
    }
}
