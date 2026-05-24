<?php

namespace App\Modules\Spare\Models;

use App\Models\User;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpareSale extends Model
{
    use HasUlid, SoftDeletes;

    protected $fillable = [
        'ulid',
        'sale_type_id',
        'spare_part_id',
        'buyer_name',
        'quantity',
        'sale_price',
        'purchase_price_snapshot',
        'profit',
        'note',
        'sold_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'sale_price' => 'decimal:2',
            'purchase_price_snapshot' => 'decimal:2',
            'profit' => 'decimal:2',
            'sold_at' => 'date',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
