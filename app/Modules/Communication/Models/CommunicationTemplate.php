<?php

declare(strict_types=1);

namespace App\Modules\Communication\Models;

use Illuminate\Database\Eloquent\Model;

class CommunicationTemplate extends Model
{
    protected $fillable = [
        'key',
        'channel',
        'subject_template',
        'body_template',
        'is_active',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'metadata' => 'array',
        ];
    }
}
