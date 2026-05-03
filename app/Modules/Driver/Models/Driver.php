<?php

namespace App\Modules\Driver\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Shared\Traits\HasUlid;
use App\Modules\Trip\Models\Trip;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasUlid, SoftDeletes;

    protected $fillable = [
        'ulid',
        'user_id',
        'license_number',
        'nid_number',
        'driving_type',
        'joining_date',
        'image_path',
        'total_trips',
        'total_profit_generated',
        'rating',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'total_trips' => 'integer',
            'total_profit_generated' => 'decimal:2',
            'rating' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
    }
}
