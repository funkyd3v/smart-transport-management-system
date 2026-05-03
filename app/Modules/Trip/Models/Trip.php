<?php

namespace App\Modules\Trip\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Driver\Models\Driver;
use App\Modules\Due\Models\DueRecord;
use App\Modules\Expense\Models\TripExpense;
use App\Modules\Invoice\Models\Invoice;
use App\Modules\Notification\Models\Notification;
use App\Modules\Payment\Models\Payment;
use App\Modules\Shared\Traits\HasUlid;
use App\Modules\Truck\Models\Truck;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasUlid, SoftDeletes;

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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TripStatus::class, 'status_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function tripExpenses(): HasMany
    {
        return $this->hasMany(TripExpense::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function dueRecord(): HasOne
    {
        return $this->hasOne(DueRecord::class);
    }

    public function reloadHistory(): HasMany
    {
        return $this->hasMany(ReloadHistory::class);
    }

    public function tripGoods(): HasMany
    {
        return $this->hasMany(TripGoods::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }
}
