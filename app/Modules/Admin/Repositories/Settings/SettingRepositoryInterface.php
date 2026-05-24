<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repositories\Settings;

interface SettingRepositoryInterface
{
    public function getGroupValues(string $group): array;

    public function upsertGroup(string $group, array $values): void;
}
