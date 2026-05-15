<?php

declare(strict_types=1);

namespace App\Modules\Trip\Repositories;

use App\Modules\Trip\DTOs\CreateTripDTO;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripStatus as TripStatusModel;
use App\Modules\Trip\Repositories\Contracts\TripRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TripRepository implements TripRepositoryInterface
{
    public function create(CreateTripDTO $dto): Trip
    {
        return Trip::query()->create([
            'ulid' => str()->ulid()->toBase32(),
            'trip_code' => '',
            'client_id' => $dto->clientId,
            'truck_id' => $dto->truckId,
            'driver_id' => $dto->driverId,
            'created_by' => $dto->createdBy,
            'status_id' => $dto->statusId,
            'pickup_point' => $dto->pickupPoint,
            'delivery_point' => $dto->deliveryPoint,
            'route_description' => $dto->routeDescription,
            'goods_description' => $dto->goodsDescription,
            'load_date' => $dto->loadDate,
            'expected_delivery_date' => $dto->expectedDeliveryDate,
            'trip_rate' => $dto->tripRate,
            'advance_payment' => $dto->advancePayment,
            'total_income' => $dto->tripRate,
            'total_expense' => 0,
            'due_amount' => max(0, $dto->tripRate - $dto->advancePayment),
            'profit' => $dto->tripRate,
            'notes' => $dto->notes,
            'sms_note' => $dto->smsNote,
        ]);
    }

    public function findByUlid(string $ulid): Trip
    {
        return Trip::query()
            ->with([
                'client:id,company_name,user_id',
                'client.user:id,name',
                'truck:id,truck_number',
                'driver:id,user_id',
                'driver.user:id,name',
                'status:id,name',
                'goods',
                'invoice',
                'expenses:id,trip_id,category_id,amount,description,expense_date,recorded_by',
                'expenses.category:id,name',
                'payments:id,trip_id,payment_method_id,amount,payment_date,collected_by,transaction_reference,note',
                'payments.paymentMethod:id,name',
                'payments.collector:id,name',
                'dueRecord',
                'reloadHistory',
                'currentVehicleLocation',
            ])
            ->where('ulid', $ulid)
            ->firstOrFail();
    }

    public function findByTripCode(string $code): Trip
    {
        return Trip::query()->where('trip_code', $code)->firstOrFail();
    }

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Trip::query()
            ->select([
                'id',
                'ulid',
                'trip_code',
                'client_id',
                'truck_id',
                'driver_id',
                'status_id',
                'pickup_point',
                'delivery_point',
                'load_date',
                'trip_rate',
                'due_amount',
                'created_at',
            ])
            ->with([
                'client:id,company_name,user_id',
                'client.user:id,name',
                'truck:id,truck_number',
                'driver:id,user_id',
                'driver.user:id,name',
                'status:id,name',
            ])
            ->when(filled($filters['status_id'] ?? null), fn ($q) => $q->where('status_id', (int) $filters['status_id']))
            ->when(filled($filters['client_id'] ?? null), fn ($q) => $q->where('client_id', (int) $filters['client_id']))
            ->when(filled($filters['truck_id'] ?? null), fn ($q) => $q->where('truck_id', (int) $filters['truck_id']))
            ->when(filled($filters['date_from'] ?? null), fn ($q) => $q->whereDate('load_date', '>=', (string) $filters['date_from']))
            ->when(filled($filters['date_to'] ?? null), fn ($q) => $q->whereDate('load_date', '<=', (string) $filters['date_to']))
            ->when(filled($filters['search'] ?? null), function ($q) use ($filters): void {
                $search = (string) $filters['search'];

                $q->where(function ($searchQuery) use ($search): void {
                    $searchQuery->where('trip_code', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($clientQuery) use ($search): void {
                            $clientQuery->where('company_name', 'like', "%{$search}%")
                                ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
                        });
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function updateStatus(Trip $trip, TripStatus $status): bool
    {
        $statusModel = TripStatusModel::query()->where('name', $status->value)->first();

        if ($statusModel === null) {
            $statusModel = TripStatusModel::query()->firstOrCreate([
                'name' => $status->value,
            ]);
        }

        return $trip->update(['status_id' => $statusModel->id]);
    }
}
