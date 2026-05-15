<?php

declare(strict_types=1);

namespace App\Modules\Trip\DTOs;

use App\Modules\Trip\Models\CurrentVehicleLocation;

final readonly class TripLocationIngestResultDTO
{
    public function __construct(
        public bool $accepted,
        public bool $broadcasted,
        public bool $historyStored,
        public string $message,
        public ?CurrentVehicleLocation $location,
    ) {}
}
