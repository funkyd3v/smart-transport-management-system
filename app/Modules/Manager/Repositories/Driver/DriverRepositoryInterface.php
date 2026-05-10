<?php

declare(strict_types=1);

namespace App\Modules\Manager\Repositories\Driver;

use App\Modules\Driver\Models\Driver;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface DriverRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): Driver;

    public function findWithStats(int $id): Driver;

    public function create(array $data): Driver;

    public function update(Driver $driver, array $data): Driver;

    public function softDelete(Driver $driver): bool;

    public function toggleStatus(Driver $driver): Driver;

    public function toggleApproval(Driver $driver): Driver;

    public function hasActiveTrip(Driver $driver): bool;

    public function getAssignableDrivers(): Collection;
}
