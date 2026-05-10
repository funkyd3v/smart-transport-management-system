<?php

declare(strict_types=1);

namespace App\Modules\Manager\Repositories\Client;

use App\Modules\Client\Models\Client;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ClientRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function findById(int $id): Client;

    public function findWithStats(int $id): Client;

    public function create(array $data): Client;

    public function update(Client $client, array $data): Client;

    public function softDelete(Client $client): bool;

    public function toggleStatus(Client $client): Client;

    public function hasTrips(Client $client): bool;
}
