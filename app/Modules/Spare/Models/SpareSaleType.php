<?php

namespace App\Modules\Spare\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpareSaleType extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
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
