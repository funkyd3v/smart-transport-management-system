<?php

declare(strict_types=1);

namespace App\Modules\Trip\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripNotification extends Model
{
    use HasUlid;

    public $timestamps = false;

    protected $table = 'notifications';

    protected $fillable = [
        'ulid',
        'user_id',
        'trip_id',
        'type',
        'channel',
        'recipient_phone',
        'message',
        'status',
        'sent_at',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
