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
        return $this->hasRole($user, ['admin', 'manager']);
    }

    public function delete(User $user, Client $client): bool
    {
        if ($this->hasRole($user, ['admin'])) {
            return true;
        }

        if (! $this->hasRole($user, ['manager'])) {
            return false;
        }

        $clientUserRole = $client->user !== null
            ? (is_object($client->user->role) ? (string) ($client->user->role->name ?? '') : (string) $client->user->role)
            : '';

        return $clientUserRole === 'client';
    }

    public function toggleStatus(User $user, Client $client): bool
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
