<?php

declare(strict_types=1);

namespace App\Modules\Manager\Services;

use App\Modules\Client\Models\Client;
use App\Modules\Manager\Repositories\Client\ClientRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ClientService
{
    public function __construct(private readonly ClientRepositoryInterface $clientRepository) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Client
    {
        return $this->clientRepository->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Client $client, array $data): Client
    {
        return $this->clientRepository->update($client, $data);
    }

    public function delete(Client $client): bool
    {
        if ($this->clientRepository->hasTrips($client)) {
            throw new HttpException(409, 'Cannot delete a client with existing trips.');
        }

        return $this->clientRepository->softDelete($client);
    }

    public function toggleStatus(Client $client): Client
    {
        return $this->clientRepository->toggleStatus($client);
    }

    /**
     * @return array<string, int|float>
     */
    public function getStats(Client $client): array
    {
        $clientWithStats = $client;

        if (! isset($clientWithStats->trips_count) || ! $clientWithStats->relationLoaded('trips')) {
            $clientWithStats = $this->clientRepository->findWithStats((int) $client->id);
        }

        return [
            'total_trips' => (int) ($clientWithStats->trips_count ?? 0),
            'total_business_amount' => (float) ($clientWithStats->trips_sum_trip_rate ?? 0),
            'total_due' => (float) ($clientWithStats->trips_sum_due_amount ?? 0),
            'payment_count' => (int) ($clientWithStats->payments_count ?? 0),
        ];
    }

    public function getClientWithStats(Client $client): Client
    {
        return $this->clientRepository->findWithStats((int) $client->id);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getFilteredPaginated(array $filters): LengthAwarePaginator
    {
        return $this->clientRepository->paginate($filters);
    }
}
