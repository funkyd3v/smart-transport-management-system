<?php

declare(strict_types=1);

namespace App\Modules\Admin\Services\Settings;

use App\Modules\Admin\DTOs\Settings\GeneralSettingDTO;
use App\Modules\Admin\Repositories\Settings\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

final class GeneralSettingService
{
    private const CACHE_KEY = 'settings.general';

    public function __construct(private readonly SettingRepositoryInterface $repository) {}

    public function settings(): array
    {
        return Cache::remember(self::CACHE_KEY, 86400, function (): array {
            $stored = $this->repository->getGroupValues('general');

            $settings = array_merge($this->defaults(), $stored);
            $settings['company_logo_url'] = filled($settings['company_logo'])
                ? Storage::url((string) $settings['company_logo'])
                : null;

            return $settings;
        });
    }

    public function update(GeneralSettingDTO $dto): void
    {
        $currentSettings = $this->settings();
        $logoPath = $currentSettings['company_logo'] ?? null;

        if ($dto->companyLogo !== null) {
            if (filled($logoPath)) {
                Storage::disk('public')->delete((string) $logoPath);
            }

            $logoPath = $dto->companyLogo->store('settings', 'public');
        }

        $this->repository->upsertGroup('general', [
            'company_name' => $dto->companyName,
            'company_address' => $dto->companyAddress,
            'contact_number' => $dto->contactNumber,
            'email_address' => $dto->emailAddress,
            'currency_symbol' => $dto->currencySymbol,
            'timezone' => $dto->timezone,
            'date_format' => $dto->dateFormat,
            'company_logo' => $logoPath,
        ]);

        Cache::forget(self::CACHE_KEY);
    }

    private function defaults(): array
    {
        return [
            'company_name' => (string) config('app.name'),
            'company_address' => '',
            'contact_number' => '',
            'email_address' => '',
            'currency_symbol' => '৳',
            'timezone' => (string) config('app.timezone'),
            'date_format' => 'd/m/Y',
            'company_logo' => null,
            'company_logo_url' => null,
        ];
    }
}
