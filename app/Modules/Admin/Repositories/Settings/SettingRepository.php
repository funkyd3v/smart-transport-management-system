<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repositories\Settings;

use App\Modules\Admin\Models\SystemSetting;

final class SettingRepository implements SettingRepositoryInterface
{
    public function getGroupValues(string $group): array
    {
        return SystemSetting::query()
            ->where('group', $group)
            ->pluck('value', 'key')
            ->toArray();
    }

    public function upsertGroup(string $group, array $values): void
    {
        foreach ($values as $key => $value) {
            SystemSetting::query()->updateOrCreate(
                ['group' => $group, 'key' => (string) $key],
                ['value' => $value],
            );
        }
    }
}
