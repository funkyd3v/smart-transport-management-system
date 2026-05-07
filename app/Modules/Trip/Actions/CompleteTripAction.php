<?php

declare(strict_types=1);

namespace App\Modules\Trip\Actions;

use App\Modules\Trip\DTOs\UpdateTripStatusDTO;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Services\TripService;

class CompleteTripAction
{
    public function __construct(private readonly TripService $tripService) {}

    public function __invoke(UpdateTripStatusDTO $dto): Trip
    {
        $completedDto = new UpdateTripStatusDTO(
            tripUlid: $dto->tripUlid,
            status: TripStatus::Completed,
            updatedBy: $dto->updatedBy,
            note: $dto->note,
        );

        return $this->tripService->updateStatus($completedDto);
    }
}
