<?php

namespace App\Modules\Trip\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TripStatus extends Model
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

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'status_id');
    }
}
