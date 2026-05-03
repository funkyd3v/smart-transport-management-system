<?php

namespace App\Modules\Spare\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpareCategory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function spareParts(): HasMany
    {
        return $this->hasMany(SparePart::class, 'category_id');
    }
}
