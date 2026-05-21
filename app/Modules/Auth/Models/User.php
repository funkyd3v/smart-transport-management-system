<?php

declare(strict_types=1);

namespace App\Modules\Auth\Models;

use App\Modules\Admin\Models\CompanySetting;
use App\Modules\Admin\Models\LoginHistory;
use App\Modules\Admin\Models\NotificationPreference;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class User extends Model
{
    use HasUlid, SoftDeletes;

    protected $fillable = [
        'ulid',
        'name',
        'email',
        'phone',
        'nid',
        'date_of_birth',
        'gender',
        'address',
        'avatar',
        'password_hash',
        'role',
        'is_active',
        'approved_by',
        'approved_at',
        'last_login_at',
        'last_login_ip',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'approved_at' => 'datetime',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
        ];
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function companySetting(): HasOne
    {
        return $this->hasOne(CompanySetting::class);
    }

    public function notificationPreferences(): HasMany
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function getAvatarUrlAttribute(): string
    {
        if (filled($this->avatar)) {
            return Storage::url((string) $this->avatar);
        }

        return sprintf(
            'https://ui-avatars.com/api/?name=%s&background=0D6EFD&color=fff&size=160',
            urlencode((string) $this->name)
        );
    }

    /**
     * @param  string|list<string>  $roles
     */
    public function hasRole(string|array $roles): bool
    {
        $allowedRoles = is_array($roles) ? $roles : [$roles];
        $roleName = is_object($this->role) ? (string) ($this->role->name ?? '') : (string) $this->role;

        return in_array($roleName, $allowedRoles, true);
    }

    public function getFormattedLastLoginAttribute(): ?string
    {
        if ($this->last_login_at === null) {
            return null;
        }

        $lastLogin = $this->last_login_at;
        if ($lastLogin->isToday()) {
            return 'Today at '.$lastLogin->format('H:i');
        }

        if ($lastLogin->isYesterday()) {
            return 'Yesterday at '.$lastLogin->format('H:i');
        }

        return $lastLogin->format('d M Y \a\t H:i');
    }
}
