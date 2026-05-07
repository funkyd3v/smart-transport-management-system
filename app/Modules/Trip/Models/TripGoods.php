<?php

declare(strict_types=1);

namespace App\Modules\Trip\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripGoods extends Model
{
    public $timestamps = false;

    protected $table = 'trip_goods';

    protected $fillable = [
        'trip_id',
        'item_name',
        'unit',
        'quantity',
        'unit_price',
        'total_price',
        'measurement_details',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
