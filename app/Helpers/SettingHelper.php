<?php

declare(strict_types=1);

use App\Modules\Admin\Services\Settings\FinancialSettingService;
use App\Modules\Admin\Services\Settings\GeneralSettingService;
use App\Modules\Admin\Services\Settings\NotificationSettingService;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        $general = app(GeneralSettingService::class)->settings();
        if (array_key_exists($key, $general)) {
            return $general[$key] ?? $default;
        }

        $financial = app(FinancialSettingService::class)->settings();
        if (array_key_exists($key, $financial)) {
            return $financial[$key] ?? $default;
        }

        $notification = app(NotificationSettingService::class)->rawSettings();
        if (array_key_exists($key, $notification)) {
            $value = $notification[$key];
            if (is_string($value) && str_ends_with($key, '_api_key') && $value !== '') {
                try {
                    return decrypt($value);
                } catch (Throwable) {
                    return $default;
                }
            }

            return $value ?? $default;
        }

        return $default;
    }
}
