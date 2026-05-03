<?php

namespace App\Modules\Spare\Models;

use App\Modules\Shared\Traits\HasUlid;
use App\Modules\Truck\Models\Truck;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePart extends Model
{
    use HasUlid, SoftDeletes;

    protected $fillable = [
        'ulid',
        'category_id',
        'part_name',
        'condition',
        'sourced_from_truck_id',
        'memo_number',
        'quantity_in_stock',
        'purchase_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity_in_stock' => 'integer',
            'purchase_price' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SpareCategory::class, 'category_id');
    }

    public function sourcedFromTruck(): BelongsTo
    {
        return $this->belongsTo(Truck::class, 'sourced_from_truck_id');
    }

    public function spareSales(): HasMany
    {
        return $this->hasMany(SpareSale::class);
    }
}
