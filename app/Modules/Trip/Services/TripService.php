<?php

declare(strict_types=1);

namespace App\Modules\Trip\Services;

use App\Modules\Trip\DTOs\CreateTripDTO;
use App\Modules\Trip\DTOs\UpdateTripStatusDTO;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Events\TripCreated;
use App\Modules\Trip\Events\TripStatusChanged;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Repositories\Contracts\TripRepositoryInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TripService
{
    public function __construct(
        private readonly TripRepositoryInterface $tripRepository,
        private readonly TripCodeGenerator $tripCodeGenerator,
        private readonly RecalculateTripFinancials $recalculateTripFinancials,
    ) {}

    public function createTrip(CreateTripDTO $dto): Trip
    {
        $trip = DB::transaction(function () use ($dto): Trip {
            $trip = $this->tripRepository->create($dto);

            $trip->trip_code = $this->tripCodeGenerator->generate();
            $trip->save();

            foreach ($dto->goods as $item) {
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $trip->goods()->create([
                    'item_name' => $item['item_name'],
                    'unit' => $item['unit'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $quantity * $unitPrice,
                    'measurement_details' => null,
                ]);
            }

            $this->recalculateTripFinancials->execute($trip);

            return $trip->fresh(['goods', 'status']);
        });

        event(new TripCreated($trip));

        return $trip;
    }

    public function updateStatus(UpdateTripStatusDTO $dto): Trip
    {
        $trip = $this->tripRepository->findByUlid($dto->tripUlid);
        $fromStatus = $this->mapStatus((string) ($trip->status?->name ?? 'created'));

        if (! $trip->canTransitionTo($dto->status)) {
            throw new RuntimeException('Invalid status transition from '.$fromStatus->value.' to '.$dto->status->value);
        }

        if ($fromStatus === $dto->status) {
            return $trip->fresh(['status']);
        }

        DB::transaction(function () use ($trip, $dto): void {
            $this->tripRepository->updateStatus($trip, $dto->status);

            if ($dto->status === TripStatus::Completed) {
                $trip->forceFill(['completed_at' => now()])->save();
            }
        });

        $updated = $this->tripRepository->findByUlid($trip->ulid);

        event(new TripStatusChanged($updated, $fromStatus, $dto->status));

        return $updated;
    }

    private function mapStatus(string $statusName): TripStatus
    {
        $normalized = strtolower(trim($statusName));

        return match ($normalized) {
            'created', 'pending' => TripStatus::Created,
            'in_progress', 'active', 'in_transit' => TripStatus::InProgress,
            'completed' => TripStatus::Completed,
            'cancelled', 'canceled' => TripStatus::Cancelled,
            default => TripStatus::Created,
        };
    }
}
