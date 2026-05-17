<?php

declare(strict_types=1);

namespace App\Modules\Driver\Policies;

use App\Models\User;
use App\Modules\Driver\Models\Driver;
use App\Modules\Trip\Enums\TripStatus as TripStatusEnum;
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

        return $this->ownsTrip($user, $trip);
    }

    public function create(User $user): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function update(User $user, Trip $trip): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        if ($this->hasRole($user, ['manager'])) {
            return (int) $trip->created_by === (int) $user->id;
        }

        return $this->ownsTrip($user, $trip);
    }

    public function delete(User $user, Trip $trip): bool
    {
        return $this->hasRole($user, ['admin']);
    }

    public function updateStatus(User $user, Trip $trip): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        if ($this->hasRole($user, ['manager'])) {
            return (int) $trip->created_by === (int) $user->id;
        }

        return $this->ownsTrip($user, $trip)
            && ! $this->isTripClosed($trip)
            && ! $this->isCompletionRequested($trip);
    }

    public function addExpense(User $user, Trip $trip): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        if ($this->hasRole($user, ['manager'])) {
            return (int) $trip->created_by === (int) $user->id;
        }

        return $this->ownsTrip($user, $trip)
            && $this->isTripInProgress($trip)
            && ! $this->isCompletionRequested($trip);
    }

    public function recordExpense(User $user, Trip $trip): bool
    {
        return $this->addExpense($user, $trip);
    }

    public function addReload(User $user, Trip $trip): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        if ($this->hasRole($user, ['manager'])) {
            return (int) $trip->created_by === (int) $user->id;
        }

        return $this->ownsTrip($user, $trip)
            && $this->isTripInProgress($trip)
            && ! $this->isCompletionRequested($trip);
    }

    public function addReloadHistory(User $user, Trip $trip): bool
    {
        return $this->addReload($user, $trip);
    }

    public function generateInvoice(User $user, Trip $trip): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return ! $trip->isInvoiced();
        }

        return $this->hasRole($user, ['manager'])
            && (int) $trip->created_by === (int) $user->id
            && ! $trip->isInvoiced();
    }

    public function recordPayment(User $user, Trip $trip): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        return $this->hasRole($user, ['manager']) && (int) $trip->created_by === (int) $user->id;
    }

    public function submitLocation(User $user, Trip $trip): bool
    {
        return $this->hasRole($user, ['driver'])
            && $this->ownsTrip($user, $trip)
            && $this->isTripInProgress($trip)
            && ! $this->isCompletionRequested($trip);
    }

    /**
     * @param  list<string>  $roles
     */
    private function hasRole(User $user, array $roles): bool
    {
        $roleName = is_object($user->role) ? (string) ($user->role->name ?? '') : (string) $user->role;

        return in_array($roleName, $roles, true);
    }

    private function ownsTrip(User $user, Trip $trip): bool
    {
        $driverId = Driver::query()->where('user_id', $user->id)->value('id');

        return $driverId !== null && (int) $trip->driver_id === (int) $driverId;
    }

    private function isTripClosed(Trip $trip): bool
    {
        $status = strtolower(trim((string) $trip->status?->name));

        return in_array($status, [TripStatusEnum::Completed->value, TripStatusEnum::Cancelled->value], true);
    }

    private function isTripInProgress(Trip $trip): bool
    {
        $status = strtolower(trim((string) $trip->status?->name));

        return $status === TripStatusEnum::InProgress->value;
    }

    private function isCompletionRequested(Trip $trip): bool
    {
        return $trip->completion_requested_at !== null;
    }
}
