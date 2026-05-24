<?php

namespace App\Modules\Spare\Models;

use App\Modules\Shared\Traits\HasUlid;
use App\Modules\Truck\Models\Truck;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SparePart extends Model
{
    use HasUlid, SoftDeletes;

    public const LOW_STOCK_THRESHOLD = 3;

    protected $fillable = [
        'ulid',
        'category_id',
        'name',
        'part_name',
        'condition',
        'source_memo_number',
        'source_truck_id',
        'memo_number',
        'sourced_from_truck_id',
        'quantity',
        'quantity_in_stock',
        'purchase_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'quantity_in_stock' => 'integer',
            'purchase_price' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SpareCategory::class, 'category_id');
    }

    public function sourceTruck(): BelongsTo
    {
        return $this->belongsTo(Truck::class, 'source_truck_id');
    }

    public function sales(): HasMany
    {
        return $this->hasMany(SpareSale::class);
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('quantity', '>', 0);
    }
}
