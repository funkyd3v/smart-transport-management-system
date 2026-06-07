<?php

declare(strict_types=1);

namespace App\Modules\Payment\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Shared\Traits\HasUlid;
use App\Modules\Trip\Models\Trip;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasUlid;

    protected $fillable = [
        'ulid',
        'trip_id',
        'payable_type',
        'payable_id',
        'client_id',
        'collected_by',
        'payment_method_id',
        'gateway',
        'status',
        'amount',
        'transaction_reference',
        'provider_reference',
        'payment_date',
        'is_advance',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
            'is_advance' => 'boolean',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function collectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function collector(): BelongsTo
    {
        return $this->collectedBy();
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(PaymentAttempt::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(PaymentWebhook::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(PaymentAudit::class);
    }
}
