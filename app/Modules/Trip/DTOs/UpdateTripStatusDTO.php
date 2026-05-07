<?php

declare(strict_types=1);

namespace App\Modules\Trip\DTOs;

use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Http\Requests\UpdateTripStatusRequest;

readonly class UpdateTripStatusDTO
{
    public function __construct(
        public string $tripUlid,
        public TripStatus $status,
        public int $updatedBy,
        public ?string $note,
    ) {}

    public static function fromRequest(UpdateTripStatusRequest $request): self
    {
        $data = $request->validated();

        return new self(
            tripUlid: (string) $data['trip_ulid'],
            status: TripStatus::from((string) $data['status']),
            updatedBy: (int) $request->user()->id,
            note: $data['note'] ?? null,
        );
    }
}
