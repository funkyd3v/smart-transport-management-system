<?php

declare(strict_types=1);

namespace App\Modules\Driver\Repositories\Trip;

use App\Modules\Trip\Models\Trip;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DriverTripRepositoryInterface
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function getByDriver(int $driverId, array $filters, int $perPage = 15): LengthAwarePaginator;

    public function findByIdForDriver(int $tripId, int $driverId): Trip;

    public function findWithFullDetail(int $tripId, int $driverId): Trip;

    public function resolveExpenseCategoryId(string $category): int;
}
