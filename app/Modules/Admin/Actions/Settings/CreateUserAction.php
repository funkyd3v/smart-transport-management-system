<?php

declare(strict_types=1);

namespace App\Modules\Admin\Actions\Settings;

use App\Models\User;
use App\Modules\Admin\DTOs\Settings\UserDTO;
use App\Modules\Admin\Services\Settings\UserManagementService;

final class CreateUserAction
{
    public function __construct(private readonly UserManagementService $service) {}

    public function __invoke(UserDTO $dto): User
    {
        return $this->service->create($dto);
    }
}
