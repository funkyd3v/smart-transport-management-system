<?php

declare(strict_types=1);

namespace App\Modules\Trip\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Driver\Models\Driver;
use App\Modules\Shared\Traits\HasUlid;
use App\Modules\Trip\Enums\TripStatus as TripStatusEnum;
use App\Modules\Truck\Models\Truck;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasUlid;
    use SoftDeletes;

    protected $table = 'trips';

    protected $fillable = [
        'ulid',
        'trip_code',
        'client_id',
        'truck_id',
        'driver_id',
        'created_by',
        'status_id',
        'pickup_point',
        'delivery_point',
        'route_description',
        'goods_description',
        'load_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'trip_rate',
        'advance_payment',
        'total_income',
        'total_expense',
        'due_amount',
        'profit',
        'notes',
        'sms_note',
        'invoice_generated_at',
        'completed_at',
        'completion_requested_at',
        'completion_requested_by',
        'completion_requested_note',
    ];

    protected function casts(): array
    {
        return [
            'load_date' => 'datetime',
            'expected_delivery_date' => 'datetime',
            'actual_delivery_date' => 'datetime',
            'trip_rate' => 'decimal:2',
            'advance_payment' => 'decimal:2',
            'total_income' => 'decimal:2',
            'total_expense' => 'decimal:2',
            'due_amount' => 'decimal:2',
            'profit' => 'decimal:2',
            'invoice_generated_at' => 'datetime',
            'completed_at' => 'datetime',
            'completion_requested_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function completionRequestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completion_requested_by');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TripStatus::class, 'status_id');
    }

    public function goods(): HasMany
    {
        return $this->hasMany(TripGoods::class, 'trip_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'trip_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(TripExpense::class, 'trip_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'trip_id');
    }

    public function dueRecord(): HasOne
    {
        return $this->hasOne(DueRecord::class, 'trip_id');
    }

    public function reloadHistory(): HasMany
    {
        return $this->hasMany(ReloadHistory::class, 'trip_id');
    }

    public function reloadHistories(): HasMany
    {
        return $this->reloadHistory();
    }

    public function currentVehicleLocation(): HasOne
    {
        return $this->hasOne(CurrentVehicleLocation::class, 'trip_id');
    }

    public function vehicleLocationHistories(): HasMany
    {
        return $this->hasMany(VehicleLocationHistory::class, 'trip_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(TripNotification::class, 'trip_id');
    }

    public function scopeStatus(Builder $query, ?int $statusId): Builder
    {
        if ($statusId === null) {
            return $query;
        }

        return $query->where('status_id', $statusId);
    }

    public function scopeClient(Builder $query, ?int $clientId): Builder
    {
        if ($clientId === null) {
            return $query;
        }

        return $query->where('client_id', $clientId);
    }

    public function scopeDriver(Builder $query, ?int $driverId): Builder
    {
        if ($driverId === null) {
            return $query;
        }

        return $query->where('driver_id', $driverId);
    }

    public function isInvoiced(): bool
    {
        return $this->invoice_generated_at !== null;
    }

    public function canAddExpense(): bool
    {
        $statusName = strtolower(trim((string) $this->status?->name));

        return in_array($statusName, [TripStatusEnum::InProgress->value, 'active', 'in_transit', TripStatusEnum::Completed->value], true)
            && ! $this->hasPendingCompletionRequest();
    }

    public function hasPendingCompletionRequest(): bool
    {
        return $this->completion_requested_at !== null;
    }

    public function canTransitionTo(TripStatusEnum $toStatus): bool
    {
        $current = $this->normalizedStatus();

        if ($current === null) {
            return false;
        }

        return in_array($toStatus, $current->allowedNextStatuses(), true);
    }

    private function normalizedStatus(): ?TripStatusEnum
    {
        $statusName = strtolower(trim((string) $this->status?->name));

        return match ($statusName) {
            'created', 'pending' => TripStatusEnum::Created,
            'in_progress', 'active', 'in_transit' => TripStatusEnum::InProgress,
            'completed' => TripStatusEnum::Completed,
            'cancelled', 'canceled' => TripStatusEnum::Cancelled,
            default => null,
        };
    }

    public function getRouteKeyName(): string
    {
        return 'ulid';
    }
}
