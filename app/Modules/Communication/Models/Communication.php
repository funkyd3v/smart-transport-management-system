<?php

declare(strict_types=1);

namespace App\Modules\Communication\Models;

use App\Modules\Communication\Enums\CommunicationChannel;
use App\Modules\Auth\Models\User;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Communication extends Model
{
    use HasUlid;

    protected $fillable = [
        'ulid',
        'reference_no',
        'reference_type',
        'reference_id',
        'channel',
        'provider',
        'recipient',
        'subject',
        'body',
        'status',
        'provider_message_id',
        'template_key',
        'template_data',
        'metadata',
        'scheduled_at',
        'requested_by',
        'sent_at',
        'delivered_at',
        'failed_at',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'template_data' => 'array',
            'metadata' => 'array',
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function channelEnum(): CommunicationChannel
    {
        return CommunicationChannel::from((string) $this->channel);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(CommunicationAttempt::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(CommunicationLog::class);
    }
}
