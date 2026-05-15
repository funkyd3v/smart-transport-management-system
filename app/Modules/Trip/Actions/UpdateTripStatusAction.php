<?php

declare(strict_types=1);

namespace App\Modules\Trip\Actions;

use App\Modules\Trip\DTOs\UpdateTripStatusDTO;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Services\TripService;
use App\Modules\Truck\Models\Truck;
use App\Modules\Truck\Models\TruckStatus;

class UpdateTripStatusAction
{
    public function __construct(
        private readonly TripService $tripService,
        private readonly CompleteTripAction $completeTrip,
    ) {}

    public function __invoke(UpdateTripStatusDTO $dto): Trip
    {
        if ($dto->status === TripStatus::Completed && $this->isDriverRequest()) {
            return $this->tripService->requestCompletion($dto);
        }

        if ($dto->status === TripStatus::Completed) {
            return ($this->completeTrip)($dto);
        }

        $trip = $this->tripService->updateStatus($dto);

        if ($dto->status === TripStatus::Cancelled) {
            $this->markTruckIdle($trip);
        }

        return $trip;
    }

    private function isDriverRequest(): bool
    {
        $user = request()->user();
        $roleName = is_object($user?->role) ? (string) ($user?->role?->name ?? '') : (string) ($user?->role ?? '');

        return $roleName === 'driver';
    }

    private function markTruckIdle(Trip $trip): void
    {
        $truck = Truck::query()->find($trip->truck_id);

        if ($truck === null) {
            return;
        }

        $status = TruckStatus::query()->firstOrCreate([
            'name' => 'Idle',
        ]);

        $truck->forceFill(['status_id' => $status->id])->save();
    }
}
