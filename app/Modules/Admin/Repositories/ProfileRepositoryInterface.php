<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repositories;

use App\Models\User;
use App\Modules\Admin\Models\CompanySetting;
use App\Modules\Admin\Models\NotificationPreference;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProfileRepositoryInterface
{
    public function updatePersonalInfo(User $user, array $data): User;

    public function updateAvatar(User $user, string $path): User;

    public function updateCompany(User $user, array $data): CompanySetting;

    public function updateCompanyLogo(User $user, string $path): CompanySetting;

    public function updateCompanySignature(User $user, string $path): CompanySetting;

    public function getNotificationPreferences(string $userId): Collection;

    public function updateNotificationPreference(string $userId, string $event, string $channel, bool $enabled): NotificationPreference;

    public function getLoginHistory(string $userId, int $limit = 10): Collection;

    public function getActiveSessions(string $userId): Collection;

    public function getProfileStats(string $userId): array;

    public function getActivityLog(string $userId, array $filters): LengthAwarePaginator;

    public function getActivityLogCollection(string $userId, array $filters): Collection;
}
