<?php

declare(strict_types=1);

namespace App\Modules\Communication\Models;

use App\Modules\Communication\Models\Communication;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationLog extends Model
{
    protected $fillable = [
        'communication_id',
        'event',
        'message',
        'context',
        'logged_at',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'logged_at' => 'datetime',
        ];
    }

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }
}
