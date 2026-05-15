<?php

declare(strict_types=1);

namespace App\Modules\Trip\Repositories\Contracts;

use App\Modules\Trip\DTOs\UpsertVehicleLocationDTO;
use App\Modules\Trip\Models\CurrentVehicleLocation;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\VehicleLocationHistory;

interface VehicleLocationRepositoryInterface
{
    public function findCurrentByTripId(int $tripId): ?CurrentVehicleLocation;

    public function upsertCurrent(UpsertVehicleLocationDTO $dto): CurrentVehicleLocation;

    public function storeHistory(UpsertVehicleLocationDTO $dto): VehicleLocationHistory;

    public function latestHistoryByTripId(int $tripId): ?VehicleLocationHistory;

    public function markOfflineForTrip(Trip $trip): ?CurrentVehicleLocation;
}
