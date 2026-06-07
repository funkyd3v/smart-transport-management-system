<?php

declare(strict_types=1);

namespace App\Modules\Communication\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpCode extends Model
{
    use HasUlid;

    protected $fillable = [
        'ulid',
        'reference_no',
        'purpose',
        'recipient',
        'channel',
        'provider',
        'code_hash',
        'attempts',
        'max_attempts',
        'expires_at',
        'verified_at',
        'requested_by',
        'reference_type',
        'reference_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
