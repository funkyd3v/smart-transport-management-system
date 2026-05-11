<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Modules\Trip\Models\Trip;

class TripPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasRole($user, ['admin', 'manager', 'driver']);
    }

    public function view(User $user, Trip $trip): bool
    {
        if ($this->hasRole($user, ['admin', 'manager'])) {
            return true;
        }

        return $this->hasRole($user, ['driver']) && (int) $trip->driver_id === (int) ($user->driver?->id ?? 0);
    }

    public function create(User $user): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function update(User $user, Trip $trip): bool
    {
        if ($this->hasRole($user, ['admin', 'manager'])) {
            return true;
        }

        return $this->hasRole($user, ['driver']) && (int) $trip->driver_id === (int) ($user->driver?->id ?? 0);
    }

    public function delete(User $user, Trip $trip): bool
    {
        return $this->hasRole($user, ['admin']);
    }

    public function updateStatus(User $user, Trip $trip): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function recordExpense(User $user, Trip $trip): bool
    {
        if ($this->hasRole($user, ['admin', 'manager'])) {
            return true;
        }

        return $this->hasRole($user, ['driver']) && (int) $trip->driver_id === (int) ($user->driver?->id ?? 0);
    }

    public function addReloadHistory(User $user, Trip $trip): bool
    {
        if ($this->hasRole($user, ['admin', 'manager'])) {
            return true;
        }

        return $this->hasRole($user, ['driver']) && (int) $trip->driver_id === (int) ($user->driver?->id ?? 0);
    }

    public function generateInvoice(User $user, Trip $trip): bool
    {
        return $this->hasRole($user, ['admin', 'manager']) && ! $trip->isInvoiced();
    }

    public function recordPayment(User $user, Trip $trip): bool
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
