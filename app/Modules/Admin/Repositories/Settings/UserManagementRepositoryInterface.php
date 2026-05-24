<?php

declare(strict_types=1);

namespace App\Modules\Admin\Repositories\Settings;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserManagementRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): User;

    public function update(User $user, array $data): User;

    public function toggleStatus(User $user): User;
}
