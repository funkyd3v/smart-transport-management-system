<?php

declare(strict_types=1);

namespace App\Modules\Manager\Policies;

use App\Models\User;
use App\Modules\Client\Models\Client;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function view(User $user, Client $client): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function create(User $user): bool
    {
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function update(User $user, Client $client): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        return $this->hasRole($user, ['manager']) && $this->ownsResource($user, $client);
    }

    public function delete(User $user, Client $client): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        if (! $this->hasRole($user, ['manager'])) {
            return false;
        }

        if (! $this->ownsResource($user, $client)) {
            return false;
        }

        $clientUserRole = $client->user !== null
            ? (is_object($client->user->role) ? (string) ($client->user->role->name ?? '') : (string) $client->user->role)
            : '';

        return $clientUserRole === 'client';
    }

    public function toggleStatus(User $user, Client $client): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        return $this->hasRole($user, ['manager']) && $this->ownsResource($user, $client);
    }

    /**
     * @param  list<string>  $roles
     */
    private function hasRole(User $user, array $roles): bool
    {
        $roleName = is_object($user->role) ? (string) ($user->role->name ?? '') : (string) $user->role;

        return in_array($roleName, $roles, true);
    }

    private function ownsResource(User $user, Client $client): bool
    {
        $ownerId = (int) ($client->created_by ?? 0);

        if ($ownerId > 0) {
            return $ownerId === (int) $user->id;
        }

        if ($client->relationLoaded('user') && $client->user !== null) {
            return (int) ($client->user->approved_by ?? 0) === (int) $user->id;
        }

        $client->loadMissing('user:id,approved_by');

        return (int) ($client->user?->approved_by ?? 0) === (int) $user->id;
    }
}
