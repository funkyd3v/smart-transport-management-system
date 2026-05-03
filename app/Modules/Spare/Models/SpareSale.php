<?php

namespace App\Modules\Spare\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpareSale extends Model
{
    use HasUlid;

    protected $fillable = [
        'ulid',
        'spare_part_id',
        'sale_type_id',
        'sold_by',
        'buyer_name',
        'buyer_contact',
        'quantity_sold',
        'purchase_price_snapshot',
        'sale_price',
        'profit',
        'sale_date',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'quantity_sold' => 'integer',
            'purchase_price_snapshot' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'profit' => 'decimal:2',
            'sale_date' => 'date',
        ];
    }

    public function sparePart(): BelongsTo
    {
        return $this->belongsTo(SparePart::class);
    }

    public function saleType(): BelongsTo
    {
        return $this->belongsTo(SpareSaleType::class, 'sale_type_id');
    }

    public function soldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }
}
