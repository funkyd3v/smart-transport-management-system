<?php

namespace App\Modules\Spare\Models;

use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpareSaleType extends Model
{
    use HasUlid;

    protected $fillable = [
        'ulid',
        'name',
        'description',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function spareSales(): HasMany
    {
        return $this->hasMany(SpareSale::class, 'sale_type_id');
    }
}
