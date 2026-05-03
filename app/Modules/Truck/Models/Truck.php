<?php

namespace App\Modules\Truck\Models;

use App\Modules\Driver\Models\Driver;
use App\Modules\Shared\Traits\HasUlid;
use App\Modules\Trip\Models\Trip;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Truck extends Model
{
    use HasUlid, SoftDeletes;

    protected $fillable = [
        'ulid',
        'truck_number',
        'model',
        'brand',
        'year',
        'capacity_tons',
        'status_id',
        'current_driver_id',
        'total_trips',
        'last_service_date',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'capacity_tons' => 'decimal:2',
            'total_trips' => 'integer',
            'last_service_date' => 'date',
        ];
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TruckStatus::class, 'status_id');
    }

    public function currentDriver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'current_driver_id');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}
