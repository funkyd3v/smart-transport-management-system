<?php

namespace App\Modules\Auth\Models;

use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasUlid, SoftDeletes;

    protected $fillable = [
        'ulid',
        'name',
        'email',
        'phone',
        'password_hash',
        'role',
        'is_active',
        'approved_by',
        'approved_at',
        'last_login_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
