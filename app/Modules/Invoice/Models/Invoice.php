<?php

namespace App\Modules\Invoice\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Client\Models\Client;
use App\Modules\Shared\Traits\HasUlid;
use App\Modules\Trip\Models\Trip;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasUlid;

    public const UPDATED_AT = null;

    protected $fillable = [
        'ulid',
        'invoice_number',
        'trip_id',
        'client_id',
        'issued_by',
        'subtotal',
        'advance_paid',
        'due_amount',
        'total_amount',
        'company_logo_path',
        'authority_signature_path',
        'pdf_path',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'advance_paid' => 'decimal:2',
            'due_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'issued_at' => 'datetime',
            'created_at' => 'datetime',
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

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
