<?php

declare(strict_types=1);

namespace App\Modules\Trip\Actions;

use App\Modules\Trip\DTOs\CreateTripDTO;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Services\TripService;

class CreateTripAction
{
    public function __construct(private readonly TripService $tripService) {}

    public function __invoke(CreateTripDTO $dto): Trip
    {
        return $this->tripService->createTrip($dto);
    }
}
