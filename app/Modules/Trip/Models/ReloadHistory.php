<?php

namespace App\Modules\Trip\Models;

use App\Modules\Driver\Models\Driver;
use App\Modules\Truck\Models\Truck;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReloadHistory extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'trip_id',
        'truck_id',
        'driver_id',
        'reload_point',
        'reloaded_at',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'reloaded_at' => 'datetime',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function truck(): BelongsTo
    {
        return $this->belongsTo(Truck::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
