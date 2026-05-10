<?php

declare(strict_types=1);

namespace App\Modules\Trip\Actions;

use App\Modules\Trip\DTOs\CreateTripDTO;
use App\Modules\Trip\DTOs\GenerateInvoiceDTO;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Services\TripService;
use App\Modules\Truck\Models\Truck;
use App\Modules\Truck\Models\TruckStatus;

class CreateTripAction
{
    public function __construct(
        private readonly TripService $tripService,
        private readonly GenerateInvoiceAction $generateInvoice,
    ) {}

    public function __invoke(CreateTripDTO $dto): Trip
    {
        $trip = $this->tripService->createTrip($dto);

        $this->markTruckOnTrip($trip);

        ($this->generateInvoice)(new GenerateInvoiceDTO(
            tripUlid: $trip->ulid,
            issuedBy: $trip->created_by,
            companyLogoPath: null,
            authoritySignaturePath: null,
        ));

        return $trip->fresh(['invoice', 'truck.status']);
    }

    private function markTruckOnTrip(Trip $trip): void
    {
        $truck = Truck::query()->find($trip->truck_id);

        if ($truck === null) {
            return;
        }

        $statusName = TruckStatus::query()->firstOrCreate([
            'name' => 'On Trip',
        ]);

        $truck->forceFill(['status_id' => $statusName->id])->save();
    }
}
