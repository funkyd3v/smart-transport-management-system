<?php

declare(strict_types=1);

namespace App\Modules\Trip\DTOs;

use Carbon\CarbonImmutable;

final readonly class UpsertVehicleLocationDTO
{
    public function __construct(
        public int $tripId,
        public string $tripUlid,
        public int $driverId,
        public int $truckId,
        public float $latitude,
        public float $longitude,
        public ?float $accuracyMeters,
        public ?float $speedKph,
        public ?int $headingDegrees,
        public CarbonImmutable $capturedAt,
        public CarbonImmutable $receivedAt,
        public string $source,
    ) {}
}
