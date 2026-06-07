<?php

declare(strict_types=1);

namespace App\Modules\Communication\Models;

use App\Modules\Communication\Models\Communication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationAttempt extends Model
{
    protected $fillable = [
        'communication_id',
        'attempt_no',
        'provider',
        'status',
        'provider_message_id',
        'response_payload',
        'error_message',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'response_payload' => 'array',
            'attempted_at' => 'datetime',
        ];
    }

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }
}
