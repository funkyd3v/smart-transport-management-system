<?php

declare(strict_types=1);

namespace App\Modules\Trip\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TripStatus extends Model
{
    public $timestamps = false;

    protected $table = 'trip_statuses';

    protected $fillable = [
        'name',
        'description',
    ];

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class, 'status_id');
    }
}
