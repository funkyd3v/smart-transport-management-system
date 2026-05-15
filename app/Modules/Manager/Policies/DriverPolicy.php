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
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        return $this->hasRole($user, ['manager']) && $this->ownsResource($user, $driver);
    }

    public function delete(User $user, Driver $driver): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        return $this->hasRole($user, ['manager']) && $this->ownsResource($user, $driver);
    }

    public function toggleStatus(User $user, Driver $driver): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        return $this->hasRole($user, ['manager']) && $this->ownsResource($user, $driver);
    }

    public function toggleApproval(User $user, Driver $driver): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        return $this->hasRole($user, ['manager']) && $this->ownsResource($user, $driver);
    }

    /**
     * @param  list<string>  $roles
     */
    private function hasRole(User $user, array $roles): bool
    {
        $roleName = is_object($user->role) ? (string) ($user->role->name ?? '') : (string) $user->role;

        return in_array($roleName, $roles, true);
    }

    private function ownsResource(User $user, Driver $driver): bool
    {
        $ownerId = (int) ($driver->created_by ?? 0);

        if ($ownerId > 0) {
            return $ownerId === (int) $user->id;
        }

        if ($driver->relationLoaded('user') && $driver->user !== null) {
            return (int) ($driver->user->approved_by ?? 0) === (int) $user->id;
        }

        $driver->loadMissing('user:id,approved_by');

        return (int) ($driver->user?->approved_by ?? 0) === (int) $user->id;
    }
}
