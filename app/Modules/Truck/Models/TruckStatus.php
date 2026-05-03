<?php

namespace App\Modules\Truck\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TruckStatus extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    protected function casts(): array
    {
        return [];
    }

    public function trucks(): HasMany
    {
        return $this->hasMany(Truck::class, 'status_id');
    }
}
