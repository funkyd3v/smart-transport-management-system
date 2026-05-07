<?php

declare(strict_types=1);

namespace App\Modules\Trip\Repositories;

use App\Modules\Trip\DTOs\CreateTripDTO;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripStatus as TripStatusModel;
use App\Modules\Trip\Repositories\Contracts\TripRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            ->with(['client', 'truck', 'driver', 'status', 'goods', 'invoice', 'expenses', 'payments', 'dueRecord', 'reloadHistory'])
            ->where('ulid', $ulid)
            ->firstOrFail();
    }

    public function findByTripCode(string $code): Trip
    {
        return Trip::query()->where('trip_code', $code)->firstOrFail();
    }

    public function paginate(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return Trip::query()
            ->with(['client', 'driver', 'truck', 'status'])
            ->when(isset($filters['status_id']), fn ($q) => $q->where('status_id', $filters['status_id']))
            ->when(isset($filters['client_id']), fn ($q) => $q->where('client_id', $filters['client_id']))
            ->when(isset($filters['driver_id']), fn ($q) => $q->where('driver_id', $filters['driver_id']))
            ->when(isset($filters['date_from']), fn ($q) => $q->whereDate('load_date', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn ($q) => $q->whereDate('load_date', '<=', $filters['date_to']))
            ->latest('id')
            ->paginate($perPage);
    }

    public function updateStatus(Trip $trip, TripStatus $status): bool
    {
        $statusModel = TripStatusModel::query()->where('name', $status->value)->first();

        if ($statusModel === null) {
            throw new ModelNotFoundException('Trip status not found: '.$status->value);
        }

        return $trip->update(['status_id' => $statusModel->id]);
    }
}
