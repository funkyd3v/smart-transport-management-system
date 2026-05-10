<?php

declare(strict_types=1);

namespace App\Modules\Manager\Services;

use App\Modules\Driver\Models\Driver;
use App\Modules\Manager\Repositories\Driver\DriverRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DriverService
{
    public function __construct(private readonly DriverRepositoryInterface $driverRepository) {}

    public function create(Request $request): Driver
    {
        $data = method_exists($request, 'validated') ? $request->validated() : [];

        if ((string) ($request->user()?->role ?? '') !== 'admin') {
            unset($data['is_approved']);
        }

        $driver = $this->driverRepository->create($data);

        if ($request->hasFile('image')) {
            $driver->addMediaFromRequest('image')->toMediaCollection('avatar');
        }

        return $driver->refresh();
    }

    public function update(Driver $driver, Request $request): Driver
    {
        $data = method_exists($request, 'validated') ? $request->validated() : [];

        if ((string) ($request->user()?->role ?? '') !== 'admin') {
            unset($data['is_approved']);
        }

        $updatedDriver = $this->driverRepository->update($driver, $data);

        if ($request->hasFile('image')) {
            $updatedDriver->addMediaFromRequest('image')->toMediaCollection('avatar');
        }

        return $updatedDriver->refresh();
    }

    public function delete(Driver $driver): bool
    {
        if ($this->driverRepository->hasActiveTrip($driver)) {
            throw new HttpException(409, 'Cannot delete a driver assigned to an active trip.');
        }

        return $this->driverRepository->softDelete($driver);
    }

    public function toggleStatus(Driver $driver): Driver
    {
        return $this->driverRepository->toggleStatus($driver);
    }

    public function toggleApproval(Driver $driver): Driver
    {
        return $this->driverRepository->toggleApproval($driver);
    }

    /**
     * @return array<string, int|float|string>
     */
    public function getStats(Driver $driver): array
    {
        $driverWithStats = $driver;

        if (! isset($driverWithStats->trips_count) || ! $driverWithStats->relationLoaded('trips')) {
            $driverWithStats = $this->driverRepository->findWithStats((int) $driver->id);
        }

        return [
            'total_trips' => (int) ($driverWithStats->trips_count ?? 0),
            'total_trip_value' => (float) ($driverWithStats->trips_sum_trip_rate ?? 0),
            'total_profit_contribution' => (float) ($driverWithStats->trips_sum_profit ?? 0),
            'rating' => (float) ($driverWithStats->rating ?? 0),
            'approval_status' => $driverWithStats->is_approved ? 'Approved' : 'Pending Approval',
        ];
    }

    public function getDriverWithStats(Driver $driver): Driver
    {
        return $this->driverRepository->findWithStats((int) $driver->id);
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function getFilteredPaginated(array $filters): LengthAwarePaginator
    {
        return $this->driverRepository->paginate($filters);
    }

    public function getAssignableDrivers(): Collection
    {
        return $this->driverRepository->getAssignableDrivers();
    }
}
