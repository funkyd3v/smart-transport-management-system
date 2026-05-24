<?php

declare(strict_types=1);

namespace App\Modules\Admin\Services\Settings;

use App\Models\User;
use App\Modules\Admin\DTOs\Settings\UserDTO;
use App\Modules\Admin\Repositories\Settings\UserManagementRepositoryInterface;
use RuntimeException;

final class UserManagementService
{
    public function __construct(private readonly UserManagementRepositoryInterface $repository) {}

    public function indexData(array $filters): array
    {
        return [
            'users' => $this->repository->paginate($filters, 15),
            'filters' => $filters,
        ];
    }

    public function create(UserDTO $dto): User
    {
        return $this->repository->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'role' => $dto->role,
            'password' => bcrypt((string) $dto->password),
            'is_active' => $dto->isActive,
        ]);
    }

    public function update(User $user, UserDTO $dto, int $actorId): User
    {
        if ($actorId === (int) $user->id && $dto->role !== $user->role) {
            throw new RuntimeException('You cannot change your own role.');
        }

        if ($actorId === (int) $user->id && $dto->isActive === false) {
            throw new RuntimeException('You cannot deactivate your own account.');
        }

        $payload = [
            'name' => $dto->name,
            'email' => $dto->email,
            'role' => $dto->role,
            'is_active' => $dto->isActive,
        ];

        if ($dto->password !== null) {
            $payload['password'] = bcrypt($dto->password);
        }

        return $this->repository->update($user, $payload);
    }

    public function toggleStatus(User $user, int $actorId): User
    {
        if ($actorId === (int) $user->id) {
            throw new RuntimeException('You cannot deactivate your own account.');
        }

        return $this->repository->toggleStatus($user);
    }
}
