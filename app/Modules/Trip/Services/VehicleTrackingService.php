<?php

declare(strict_types=1);

namespace App\Modules\Trip\Services;

use App\Modules\Trip\DTOs\TripLocationIngestResultDTO;
use App\Modules\Trip\DTOs\UpsertVehicleLocationDTO;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Events\TripLocationUpdated;
use App\Modules\Trip\Events\TripTrackingStopped;
use App\Modules\Trip\Models\CurrentVehicleLocation;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\VehicleLocationHistory;
use App\Modules\Trip\Repositories\Contracts\VehicleLocationRepositoryInterface;
use Illuminate\Support\Facades\DB;

class VehicleTrackingService
{
    public function __construct(private readonly VehicleLocationRepositoryInterface $vehicleLocationRepository) {}

    public function ingest(UpsertVehicleLocationDTO $dto): TripLocationIngestResultDTO
    {
        $trip = Trip::query()->with('status:id,name')->where('id', $dto->tripId)->firstOrFail();

        if (! $this->isTrackableStatus($trip)) {
            return new TripLocationIngestResultDTO(
                accepted: false,
                broadcasted: false,
                historyStored: false,
                message: 'Trip is not active for live tracking.',
                location: $this->vehicleLocationRepository->findCurrentByTripId($dto->tripId),
            );
        }

        $current = $this->vehicleLocationRepository->findCurrentByTripId($dto->tripId);

        if ($this->isStaleOrDuplicate($current, $dto)) {
            return new TripLocationIngestResultDTO(
                accepted: false,
                broadcasted: false,
                historyStored: false,
                message: 'Duplicate or stale location update ignored.',
                location: $current,
            );
        }

        $historyStored = false;
        $broadcasted = false;

        $newCurrent = DB::transaction(function () use ($dto, $current, &$historyStored, &$broadcasted): CurrentVehicleLocation {
            $newCurrent = $this->vehicleLocationRepository->upsertCurrent($dto);

            if ($this->shouldStoreHistory($newCurrent, $dto)) {
                $this->vehicleLocationRepository->storeHistory($dto);
                $newCurrent->forceFill(['last_history_at' => $dto->capturedAt])->save();
                $historyStored = true;
            }

            if ($this->shouldBroadcast($newCurrent, $current, $dto)) {
                $newCurrent->forceFill(['last_broadcast_at' => $dto->receivedAt])->save();
                $broadcasted = true;
            }

            return $newCurrent->refresh();
        });

        if ($broadcasted) {
            event(new TripLocationUpdated($dto->tripUlid, $this->payloadFromCurrent($newCurrent)));
        }

        return new TripLocationIngestResultDTO(
            accepted: true,
            broadcasted: $broadcasted,
            historyStored: $historyStored,
            message: 'Location update accepted.',
            location: $newCurrent,
        );
    }

    public function stopTracking(Trip $trip, string $reason = 'Trip is no longer active.'): void
    {
        $location = $this->vehicleLocationRepository->markOfflineForTrip($trip);

        if ($location === null) {
            return;
        }

        event(new TripTrackingStopped($trip->ulid, $reason));
    }

    private function isTrackableStatus(Trip $trip): bool
    {
        $normalized = strtolower(trim((string) $trip->status?->name));

        return in_array($normalized, [TripStatus::InProgress->value, 'active', 'in_transit'], true);
    }

    private function isStaleOrDuplicate(?CurrentVehicleLocation $current, UpsertVehicleLocationDTO $dto): bool
    {
        if ($current === null) {
            return false;
        }

        if ($current->captured_at !== null && $dto->capturedAt->lessThanOrEqualTo($current->captured_at->toImmutable())) {
            return true;
        }

        $sameCoordinates = $this->distanceMeters(
            (float) $current->latitude,
            (float) $current->longitude,
            $dto->latitude,
            $dto->longitude,
        ) < 3.0;

        if (! $sameCoordinates) {
            return false;
        }

        if ($current->captured_at === null) {
            return false;
        }

        $secondsDiff = $dto->capturedAt->diffInSeconds($current->captured_at->toImmutable());

        return $secondsDiff < 5;
    }

    private function shouldStoreHistory(CurrentVehicleLocation $newCurrent, UpsertVehicleLocationDTO $dto): bool
    {
        if ($newCurrent->last_history_at === null) {
            return true;
        }

        $secondsSinceLastHistory = $dto->capturedAt->diffInSeconds($newCurrent->last_history_at->toImmutable());

        if ($secondsSinceLastHistory >= 45) {
            return true;
        }

        $latestHistory = $this->vehicleLocationRepository->latestHistoryByTripId($dto->tripId);

        if (! $latestHistory instanceof VehicleLocationHistory) {
            return true;
        }

        $distanceMeters = $this->distanceMeters(
            (float) $latestHistory->latitude,
            (float) $latestHistory->longitude,
            $dto->latitude,
            $dto->longitude,
        );

        return $distanceMeters >= 120;
    }

    private function shouldBroadcast(CurrentVehicleLocation $newCurrent, ?CurrentVehicleLocation $previousCurrent, UpsertVehicleLocationDTO $dto): bool
    {
        if ($newCurrent->last_broadcast_at === null || $previousCurrent === null) {
            return true;
        }

        $secondsSinceLastBroadcast = $dto->receivedAt->diffInSeconds($newCurrent->last_broadcast_at->toImmutable());

        if ($secondsSinceLastBroadcast >= 10) {
            return true;
        }

        if ($secondsSinceLastBroadcast < 5) {
            return false;
        }

        $distanceMeters = $this->distanceMeters(
            (float) $previousCurrent->latitude,
            (float) $previousCurrent->longitude,
            $dto->latitude,
            $dto->longitude,
        );

        return $distanceMeters >= 8;
    }

    /**
     * @return array<string, float|int|string|null>
     */
    private function payloadFromCurrent(CurrentVehicleLocation $location): array
    {
        return [
            'latitude' => (float) $location->latitude,
            'longitude' => (float) $location->longitude,
            'accuracy_meters' => $location->accuracy_meters !== null ? (float) $location->accuracy_meters : null,
            'speed_kph' => $location->speed_kph !== null ? (float) $location->speed_kph : null,
            'heading_degrees' => $location->heading_degrees,
            'captured_at' => optional($location->captured_at)->toIso8601String(),
            'received_at' => optional($location->received_at)->toIso8601String(),
            'is_online' => $location->is_online,
        ];
    }

    private function distanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
