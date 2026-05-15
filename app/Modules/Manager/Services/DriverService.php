<?php

declare(strict_types=1);

namespace App\Modules\Manager\Services;

use App\Modules\Auth\Models\User;
use App\Modules\Driver\Models\Driver;
use App\Modules\Manager\Repositories\Driver\DriverRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DriverService
{
    public function __construct(private readonly DriverRepositoryInterface $driverRepository) {}

    public function create(Request $request): Driver
    {
        return DB::transaction(function () use ($request): Driver {
            $validated = method_exists($request, 'validated') ? $request->validated() : [];

            if ((string) ($request->user()?->role ?? '') !== 'admin') {
                unset($validated['is_approved']);
            }

            $status = (string) ($validated['status'] ?? 'active');
            $isApproved = (bool) ($validated['is_approved'] ?? false);
            $plainPassword = (string) ($validated['password'] ?? '');
            $approvedBy = $request->user()?->getAuthIdentifier();

            $user = User::query()->create([
                'name' => (string) ($validated['name'] ?? 'Driver'),
                'email' => (string) ($validated['email'] ?? ''),
                'phone' => (string) ($validated['mobile_number'] ?? ''),
                'password_hash' => Hash::make($plainPassword),
                'role' => 'driver',
                'is_active' => $status === 'active',
                'approved_by' => $isApproved ? $approvedBy : null,
                'approved_at' => $isApproved ? now() : null,
            ]);

            $driver = new Driver;
            $driver->forceFill([
                'created_by' => (int) $request->user()->id,
                'user_id' => $user->id,
                'license_number' => $validated['license_number'] ?? null,
                'nid_number' => $validated['nid_number'] ?? null,
                'driving_type' => $validated['driving_type'] ?? null,
                'joining_date' => $validated['joining_date'] ?? null,
                'is_available' => $status === 'active',
            ]);
            $driver->save();

            if ($request->hasFile('image')) {
                $driver->addMediaFromRequest('image')->toMediaCollection('avatar');
            }

            return $driver->refresh();
        });
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
