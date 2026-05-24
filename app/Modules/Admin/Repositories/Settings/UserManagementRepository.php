<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repositories\Settings;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

final class UserManagementRepository implements UserManagementRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->select(['id', 'name', 'email', 'role', 'is_active', 'created_at'])
            ->when(filled($filters['search'] ?? null), function (Builder $query) use ($filters): void {
                $search = (string) $filters['search'];

                $query->where(function (Builder $subQuery) use ($search): void {
                    $subQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(filled($filters['role'] ?? null), fn (Builder $query): Builder => $query->where('role', (string) $filters['role']))
            ->when(array_key_exists('status', $filters) && $filters['status'] !== '' && $filters['status'] !== null, fn (Builder $query): Builder => $query->where('is_active', (bool) $filters['status']))
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function create(array $data): User
    {
        return User::query()->create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->fill($data);
        $user->save();

        return $user->refresh();
    }

    public function toggleStatus(User $user): User
    {
        $user->is_active = ! $user->is_active;
        $user->save();

        return $user->refresh();
    }
}
