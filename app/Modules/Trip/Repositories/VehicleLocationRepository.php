<?php

declare(strict_types=1);

namespace App\Modules\Trip\Repositories;

use App\Modules\Trip\DTOs\UpsertVehicleLocationDTO;
use App\Modules\Trip\Models\CurrentVehicleLocation;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\VehicleLocationHistory;
use App\Modules\Trip\Repositories\Contracts\VehicleLocationRepositoryInterface;

class VehicleLocationRepository implements VehicleLocationRepositoryInterface
{
    public function findCurrentByTripId(int $tripId): ?CurrentVehicleLocation
    {
        return CurrentVehicleLocation::query()
            ->where('trip_id', $tripId)
            ->first();
    }

    public function upsertCurrent(UpsertVehicleLocationDTO $dto): CurrentVehicleLocation
    {
        $current = CurrentVehicleLocation::query()->updateOrCreate(
            ['trip_id' => $dto->tripId],
            [
                'driver_id' => $dto->driverId,
                'truck_id' => $dto->truckId,
                'latitude' => $dto->latitude,
                'longitude' => $dto->longitude,
                'accuracy_meters' => $dto->accuracyMeters,
                'speed_kph' => $dto->speedKph,
                'heading_degrees' => $dto->headingDegrees,
                'captured_at' => $dto->capturedAt,
                'received_at' => $dto->receivedAt,
                'is_online' => true,
                'tracking_stopped_at' => null,
                'source' => $dto->source,
            ],
        );

        return $current->refresh();
    }

    public function storeHistory(UpsertVehicleLocationDTO $dto): VehicleLocationHistory
    {
        return VehicleLocationHistory::query()->create([
            'trip_id' => $dto->tripId,
            'driver_id' => $dto->driverId,
            'truck_id' => $dto->truckId,
            'latitude' => $dto->latitude,
            'longitude' => $dto->longitude,
            'accuracy_meters' => $dto->accuracyMeters,
            'speed_kph' => $dto->speedKph,
            'heading_degrees' => $dto->headingDegrees,
            'captured_at' => $dto->capturedAt,
            'received_at' => $dto->receivedAt,
            'source' => $dto->source,
        ]);
    }

    public function latestHistoryByTripId(int $tripId): ?VehicleLocationHistory
    {
        return VehicleLocationHistory::query()
            ->where('trip_id', $tripId)
            ->orderByDesc('captured_at')
            ->first();
    }

    public function markOfflineForTrip(Trip $trip): ?CurrentVehicleLocation
    {
        $current = CurrentVehicleLocation::query()->where('trip_id', $trip->id)->first();

        if ($current === null) {
            return null;
        }

        $current->forceFill([
            'is_online' => false,
            'tracking_stopped_at' => now(),
        ])->save();

        return $current->refresh();
    }
}
