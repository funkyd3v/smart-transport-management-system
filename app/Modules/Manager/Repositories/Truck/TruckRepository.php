<?php

declare(strict_types=1);

namespace App\Modules\Manager\Repositories\Truck;

use App\Modules\Truck\Models\Truck;
use App\Modules\Truck\Models\TruckStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruckRepository implements TruckRepositoryInterface
{
    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Truck::query();
        $table = $query->getModel()->getTable();

        $hasTruckType = Schema::hasColumn($table, 'truck_type');
        $hasModel = Schema::hasColumn($table, 'model');
        $hasCapacity = Schema::hasColumn($table, 'capacity');
        $hasCapacityTons = Schema::hasColumn($table, 'capacity_tons');
        $hasStatus = Schema::hasColumn($table, 'status');
        $hasStatusId = Schema::hasColumn($table, 'status_id');

        $shouldJoinStatuses = ! $hasStatus && $hasStatusId;

        if ($shouldJoinStatuses) {
            $query->leftJoin('truck_statuses', 'truck_statuses.id', '=', "{$table}.status_id");
        }

        $query->select(["{$table}.id", "{$table}.truck_number", "{$table}.created_at"]);

        if ($hasTruckType) {
            $query->addSelect("{$table}.truck_type");
        } elseif ($hasModel) {
            $query->addSelect(DB::raw("{$table}.model as truck_type"));
        } else {
            $query->selectRaw("'' as truck_type");
        }

        if ($hasCapacity) {
            $query->addSelect("{$table}.capacity");
        } elseif ($hasCapacityTons) {
            $query->addSelect(DB::raw("{$table}.capacity_tons as capacity"));
        } else {
            $query->selectRaw('0 as capacity');
        }

        if ($hasStatus) {
            $query->addSelect("{$table}.status");
        } elseif ($shouldJoinStatuses) {
            $query->selectRaw("CASE
                WHEN LOWER(truck_statuses.name) IN ('on_trip', 'on trip', 'in transit', 'ontrip') THEN 'on_trip'
                WHEN LOWER(truck_statuses.name) IN ('under_workshop', 'under workshop', 'under maintenance', 'under_maintenance', 'workshop') THEN 'under_workshop'
                ELSE 'idle'
            END as status");
        } else {
            $query->selectRaw("'idle' as status");
        }

        if (filled($filters['status'] ?? null)) {
            $status = (string) $filters['status'];

            if ($hasStatus) {
                $query->where("{$table}.status", $status);
            } elseif ($shouldJoinStatuses) {
                $query->where(function (Builder $statusQuery) use ($status): void {
                    if ($status === 'on_trip') {
                        $statusQuery->whereIn(DB::raw('LOWER(truck_statuses.name)'), ['on_trip', 'on trip', 'in transit', 'ontrip']);

                        return;
                    }

                    if ($status === 'under_workshop') {
                        $statusQuery->whereIn(DB::raw('LOWER(truck_statuses.name)'), ['under_workshop', 'under workshop', 'under maintenance', 'under_maintenance', 'workshop']);

                        return;
                    }

                    $statusQuery->whereIn(DB::raw('LOWER(truck_statuses.name)'), ['idle', 'active', 'available']);
                });
            }
        }

        if (filled($filters['truck_type'] ?? null)) {
            $truckType = trim((string) $filters['truck_type']);

            if ($hasTruckType) {
                $query->where("{$table}.truck_type", 'like', "%{$truckType}%");
            } elseif ($hasModel) {
                $query->where("{$table}.model", 'like', "%{$truckType}%");
            }
        }

        if (filled($filters['search'] ?? null)) {
            $query->where("{$table}.truck_number", 'like', '%'.(string) $filters['search'].'%');
        }

        return $query
            ->orderBy("{$table}.created_at", 'desc')
            ->paginate($perPage);
    }

    public function findById(int $id): Truck
    {
        return Truck::query()->findOrFail($id);
    }

    public function findWithStats(int $id): Truck
    {
        return Truck::query()
            ->select('trucks.*')
            ->selectSub(function ($query): void {
                $query->from('trip_expenses')
                    ->join('trips', 'trips.id', '=', 'trip_expenses.trip_id')
                    ->whereColumn('trips.truck_id', 'trucks.id')
                    ->selectRaw('COALESCE(SUM(trip_expenses.amount), 0)');
            }, 'total_expense_amount')
            ->with([
                'status:id,name',
                'trips' => function ($query): void {
                    $query->latest()->limit(10)->select([
                        'id',
                        'truck_id',
                        'client_id',
                        'status_id',
                        'load_date',
                        'pickup_point',
                        'delivery_point',
                        'route_description',
                        'trip_rate',
                    ])->with(['client:id,company_name', 'status:id,name']);
                },
            ])
            ->withCount('trips')
            ->withSum('trips', 'trip_rate')
            ->findOrFail($id);
    }

    public function create(array $data): Truck
    {
        $truck = new Truck;
        $truck->forceFill($this->mapAttributes($data, null));
        $truck->save();

        return $truck->refresh();
    }

    public function update(Truck $truck, array $data): Truck
    {
        $truck->forceFill($this->mapAttributes($data, $truck));
        $truck->save();

        return $truck->refresh();
    }

    public function softDelete(Truck $truck): bool
    {
        return (bool) $truck->delete();
    }

    public function isOnTrip(Truck $truck): bool
    {
        $table = $truck->getTable();

        if (Schema::hasColumn($table, 'status')) {
            return Truck::query()
                ->whereKey($truck->id)
                ->where('status', 'on_trip')
                ->exists();
        }

        if (Schema::hasColumn($table, 'status_id')) {
            return Truck::query()
                ->whereKey($truck->id)
                ->whereHas('status', function (Builder $query): void {
                    $query->whereIn(DB::raw('LOWER(name)'), ['on_trip', 'on trip', 'in transit', 'ontrip']);
                })
                ->exists();
        }

        return false;
    }

    public function updateStatus(Truck $truck, string $status): Truck
    {
        $table = $truck->getTable();

        if (Schema::hasColumn($table, 'status')) {
            $truck->update(['status' => $status]);

            return $truck->refresh();
        }

        if (Schema::hasColumn($table, 'status_id')) {
            $statusId = $this->resolveStatusId($status);

            if ($statusId !== null) {
                $truck->update(['status_id' => $statusId]);
            }
        }

        return $truck->refresh();
    }

    public function getAssignableTrucks(): Collection
    {
        $query = Truck::query();
        $table = $query->getModel()->getTable();

        if (Schema::hasColumn($table, 'status')) {
            $query->whereNotIn('status', ['on_trip', 'under_workshop']);
        } elseif (Schema::hasColumn($table, 'status_id')) {
            $query->whereHas('status', function (Builder $statusQuery): void {
                $statusQuery->whereNotIn(DB::raw('LOWER(name)'), [
                    'on_trip',
                    'on trip',
                    'in transit',
                    'ontrip',
                    'under_workshop',
                    'under workshop',
                    'under maintenance',
                    'under_maintenance',
                    'workshop',
                ]);
            });
        }

        return $query->orderBy('truck_number')->get();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function mapAttributes(array $data, ?Truck $truck): array
    {
        $table = $truck?->getTable() ?? (new Truck)->getTable();
        $mapped = [];

        if (Schema::hasColumn($table, 'truck_number')) {
            $mapped['truck_number'] = $data['truck_number'] ?? $truck?->truck_number;
        }

        if (Schema::hasColumn($table, 'truck_type')) {
            $mapped['truck_type'] = $data['truck_type'] ?? $truck?->truck_type;
        } elseif (Schema::hasColumn($table, 'model')) {
            $mapped['model'] = $data['truck_type'] ?? $truck?->model;
        }

        if (Schema::hasColumn($table, 'capacity')) {
            $mapped['capacity'] = $data['capacity'] ?? $truck?->capacity;
        } elseif (Schema::hasColumn($table, 'capacity_tons')) {
            $mapped['capacity_tons'] = $data['capacity'] ?? $truck?->capacity_tons;
        }

        if (array_key_exists('status', $data)) {
            if (Schema::hasColumn($table, 'status')) {
                $mapped['status'] = $data['status'];
            } elseif (Schema::hasColumn($table, 'status_id')) {
                $statusId = $this->resolveStatusId((string) $data['status']);

                if ($statusId !== null) {
                    $mapped['status_id'] = $statusId;
                }
            }
        }

        return $mapped;
    }

    private function resolveStatusId(string $status): ?int
    {
        $normalized = strtolower(trim($status));

        $existingStatus = TruckStatus::query()
            ->get(['id', 'name'])
            ->first(fn (TruckStatus $truckStatus): bool => $this->normalizeStatus((string) $truckStatus->name) === $normalized);

        if ($existingStatus !== null) {
            return (int) $existingStatus->id;
        }

        $canonicalName = match ($normalized) {
            'on_trip' => 'On Trip',
            'under_workshop' => 'Under Workshop',
            default => 'Idle',
        };

        return (int) TruckStatus::query()->firstOrCreate([
            'name' => $canonicalName,
        ])->id;
    }

    private function normalizeStatus(string $statusName): string
    {
        $normalized = strtolower(trim($statusName));

        if (in_array($normalized, ['on_trip', 'on trip', 'in transit', 'ontrip'], true)) {
            return 'on_trip';
        }

        if (in_array($normalized, ['under_workshop', 'under workshop', 'under maintenance', 'under_maintenance', 'workshop'], true)) {
            return 'under_workshop';
        }

        return 'idle';
    }
}
