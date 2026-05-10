<?php

declare(strict_types=1);

namespace App\Modules\Manager\Repositories\Truck;

use App\Modules\Truck\Models\Truck;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TruckRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): Truck;

    public function findWithStats(int $id): Truck;

    public function create(array $data): Truck;

    public function update(Truck $truck, array $data): Truck;

    public function softDelete(Truck $truck): bool;

    public function isOnTrip(Truck $truck): bool;

    public function updateStatus(Truck $truck, string $status): Truck;

    public function getAssignableTrucks(): Collection;
}
