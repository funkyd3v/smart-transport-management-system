<?php

declare(strict_types=1);

namespace App\Modules\Manager\Policies;

use App\Models\User;
use App\Modules\Driver\Models\Driver;

class DriverPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function view(User $user, Driver $driver): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function create(User $user): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function update(User $user, Driver $driver): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function delete(User $user, Driver $driver): bool
    {
        return $this->hasRole($user, ['admin']);
    }

    public function toggleStatus(User $user, Driver $driver): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function toggleApproval(User $user, Driver $driver): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    /**
     * @param  list<string>  $roles
     */
    private function hasRole(User $user, array $roles): bool
    {
        $roleName = is_object($user->role) ? (string) ($user->role->name ?? '') : (string) $user->role;

        return in_array($roleName, $roles, true);
    }
}
