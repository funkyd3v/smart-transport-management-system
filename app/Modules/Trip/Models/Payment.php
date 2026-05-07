<?php

declare(strict_types=1);

namespace App\Modules\Trip\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Payment\Models\PaymentMethod;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasUlid;

    protected $table = 'payments';

    protected $fillable = [
        'ulid',
        'trip_id',
        'client_id',
        'collected_by',
        'payment_method_id',
        'amount',
        'transaction_reference',
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

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
