<?php

namespace App\Modules\Due\Models;

use App\Modules\Client\Models\Client;
use App\Modules\Shared\Traits\HasUlid;
use App\Modules\Trip\Models\Trip;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DueRecord extends Model
{
    use HasUlid;

    protected $fillable = [
        'ulid',
        'trip_id',
        'client_id',
        'original_due',
        'collected_amount',
        'remaining_due',
        'due_date',
        'is_settled',
        'settled_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'original_due' => 'decimal:2',
            'collected_amount' => 'decimal:2',
            'remaining_due' => 'decimal:2',
            'due_date' => 'date',
            'is_settled' => 'boolean',
            'settled_at' => 'datetime',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
