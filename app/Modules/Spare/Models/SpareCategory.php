<?php

namespace App\Modules\Spare\Models;

use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpareCategory extends Model
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

    public function spareParts(): HasMany
    {
        return $this->hasMany(SparePart::class, 'category_id');
    }
}
