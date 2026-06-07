<?php

declare(strict_types=1);

namespace App\Modules\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentWebhook extends Model
{
    protected $fillable = [
        'payment_id',
        'gateway',
        'event_type',
        'payload',
        'signature',
        'validated_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'validated_at' => 'datetime',
        ];
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }
}
