<?php

declare(strict_types=1);

namespace App\Modules\Driver\Repositories\Trip;

use App\Modules\Expense\Models\ExpenseCategory;
use App\Modules\Trip\Models\Trip;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class DriverTripRepository implements DriverTripRepositoryInterface
{
    public function getByDriver(int $driverId, array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Trip::query()
            ->select([
                'id',
                'ulid',
                'trip_code',
                'pickup_point',
                'delivery_point',
                'load_date',
                'status_id',
                'truck_id',
            ])
            ->with([
                'status:id,name',
                'truck:id,truck_number,model',
            ])
            ->where('driver_id', $driverId)
            ->when(filled($filters['status'] ?? null) && (string) $filters['status'] !== 'all', function ($query) use ($filters): void {
                $query->whereHas('status', function ($statusQuery) use ($filters): void {
                    $statusQuery->where('name', (string) $filters['status']);
                });
            })
            ->orderByDesc('load_date')
            ->paginate($perPage);
    }

    public function findByIdForDriver(int $tripId, int $driverId): Trip
    {
        return Trip::query()
            ->where('id', $tripId)
            ->where('driver_id', $driverId)
            ->firstOrFail();
    }

    public function findWithFullDetail(int $tripId, int $driverId): Trip
    {
        return Trip::query()
            ->withSum('expenses', 'amount')
            ->with([
                'status:id,name',
                'truck:id,truck_number,model',
                'client:id,company_name,user_id',
                'client.user:id,name',
                'goods',
                'expenses.category:id,name',
                'reloadHistory' => function ($query): void {
                    $query->orderByDesc('reloaded_at')->orderByDesc('id');
                },
                'invoice',
                'dueRecord',
            ])
            ->where('id', $tripId)
            ->where('driver_id', $driverId)
            ->firstOrFail();
    }

    public function resolveExpenseCategoryId(string $category): int
    {
        $name = match ($category) {
            'fuel' => 'Fuel',
            'toll' => 'Toll',
            'driver_expense' => 'Driver Expense',
            'other' => 'Other',
            default => Str::of($category)->replace('_', ' ')->headline()->toString(),
        };

        return (int) ExpenseCategory::query()->firstOrCreate(['name' => $name])->id;
    }
}
