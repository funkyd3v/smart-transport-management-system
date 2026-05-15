<?php

declare(strict_types=1);

namespace App\Modules\Manager\Policies;

use App\Models\User;
use App\Modules\Truck\Models\Truck;

class TruckPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function view(User $user, Truck $truck): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function create(User $user): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function update(User $user, Truck $truck): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        return $this->hasRole($user, ['manager']) && $this->ownsResource($user, $truck);
    }

    public function delete(User $user, Truck $truck): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        return $this->hasRole($user, ['manager']) && $this->ownsResource($user, $truck);
    }

    public function updateStatus(User $user, Truck $truck): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        return $this->hasRole($user, ['manager']) && $this->ownsResource($user, $truck);
    }

    /**
     * @param  list<string>  $roles
     */
    private function hasRole(User $user, array $roles): bool
    {
        $roleName = is_object($user->role) ? (string) ($user->role->name ?? '') : (string) $user->role;

        return in_array($roleName, $roles, true);
    }

    private function ownsResource(User $user, Truck $truck): bool
    {
        return (int) ($truck->created_by ?? 0) === (int) $user->id;
    }
}
