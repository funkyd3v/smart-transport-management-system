<?php

declare(strict_types=1);

namespace App\Modules\Admin\Actions\Settings;

use App\Models\User;
use App\Modules\Admin\Services\Settings\UserManagementService;

final class ToggleUserStatusAction
{
    public function __construct(private readonly UserManagementService $service) {}

    public function __invoke(User $user, int $actorId): User
    {
        return $this->service->toggleStatus($user, $actorId);
    }
}
