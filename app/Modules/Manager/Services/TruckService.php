<?php

declare(strict_types=1);

namespace App\Modules\Manager\Services;

use App\Modules\Manager\Repositories\Truck\TruckRepositoryInterface;
use App\Modules\Truck\Models\Truck;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TruckService
{
    public function __construct(private readonly TruckRepositoryInterface $truckRepository) {}

    public function create(array $data): Truck
    {
        if (($data['status'] ?? 'idle') === 'on_trip') {
            throw new HttpException(422, 'Truck status cannot be set to on_trip manually.');
        }

        return $this->truckRepository->create($data);
    }

    public function update(Truck $truck, array $data): Truck
    {
        if ($this->truckRepository->isOnTrip($truck) && isset($data['status']) && in_array((string) $data['status'], ['idle', 'under_workshop'], true)) {
            throw new HttpException(409, 'Truck status cannot be changed while it is on a trip.');
        }

        if (isset($data['status']) && (string) $data['status'] === 'on_trip') {
            throw new HttpException(422, 'Truck status cannot be set to on_trip manually.');
        }

        return $this->truckRepository->update($truck, $data);
    }

    public function delete(Truck $truck): bool
    {
        if ($this->truckRepository->isOnTrip($truck)) {
            throw new HttpException(409, 'Cannot delete a truck that is currently on a trip.');
        }

        return $this->truckRepository->softDelete($truck);
    }

    public function updateStatus(Truck $truck, string $status): Truck
    {
        if (! in_array($status, ['idle', 'under_workshop'], true)) {
            throw new HttpException(422, 'Only idle or under_workshop statuses can be set manually.');
        }

        if ($this->truckRepository->isOnTrip($truck)) {
            throw new HttpException(409, 'Truck is currently on a trip and status cannot be changed manually.');
        }

        return $this->truckRepository->updateStatus($truck, $status);
    }

    /**
     * @return array<string, int|float>
     */
    public function getStats(Truck $truck): array
    {
        $truckWithStats = $truck;

        if (! isset($truckWithStats->trips_count) || ! $truckWithStats->relationLoaded('trips')) {
            $truckWithStats = $this->truckRepository->findWithStats((int) $truck->id);
        }

        $totalIncome = (float) ($truckWithStats->trips_sum_trip_rate ?? 0);
        $totalExpense = (float) ($truckWithStats->total_expense_amount ?? 0);

        return [
            'total_trips' => (int) ($truckWithStats->trips_count ?? 0),
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'total_profit' => $totalIncome - $totalExpense,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getFilteredPaginated(array $filters): LengthAwarePaginator
    {
        return $this->truckRepository->paginate($filters);
    }

    public function getTruckWithStats(Truck $truck): Truck
    {
        return $this->truckRepository->findWithStats((int) $truck->id);
    }
}
