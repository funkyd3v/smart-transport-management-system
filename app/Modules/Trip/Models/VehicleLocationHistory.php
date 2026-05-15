<?php

declare(strict_types=1);

namespace App\Modules\Trip\Models;

use App\Modules\Driver\Models\Driver;
use App\Modules\Truck\Models\Truck;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleLocationHistory extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'trip_id',
        'driver_id',
        'truck_id',
        'latitude',
        'longitude',
        'accuracy_meters',
        'speed_kph',
        'heading_degrees',
        'captured_at',
        'received_at',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'accuracy_meters' => 'decimal:2',
            'speed_kph' => 'decimal:2',
            'heading_degrees' => 'integer',
            'captured_at' => 'datetime',
            'received_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }
}
