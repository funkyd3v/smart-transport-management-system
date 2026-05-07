<?php

declare(strict_types=1);

namespace App\Modules\Trip\Actions;

use App\Modules\Trip\DTOs\UpdateTripStatusDTO;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Services\TripService;

class UpdateTripStatusAction
{
    public function __construct(private readonly TripService $tripService) {}

    public function __invoke(UpdateTripStatusDTO $dto): Trip
    {
        return $this->tripService->updateStatus($dto);
    }
}
