<?php

declare(strict_types=1);

namespace App\Modules\Trip\Repositories\Contracts;

use App\Modules\Trip\DTOs\CreateTripDTO;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Models\Trip;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TripRepositoryInterface
{
    public function create(CreateTripDTO $dto): Trip;

    public function findByUlid(string $ulid): Trip;

    public function findByTripCode(string $code): Trip;

    /**
     * @param  array<string,mixed>  $filters
     */
    public function paginate(array $filters, int $perPage = 20): LengthAwarePaginator;

    public function updateStatus(Trip $trip, TripStatus $status): bool;
}
