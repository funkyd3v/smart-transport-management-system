<?php

declare(strict_types=1);

namespace App\Models;

use App\Modules\Admin\Models\CompanySetting;
use App\Modules\Admin\Models\LoginHistory;
use App\Modules\Admin\Models\NotificationPreference;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasApiTokens, HasFactory, HasUlid, InteractsWithMedia, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
        'password',
        'role',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
        ];
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

    protected function password(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->attributes['password_hash'] ?? null,
            set: fn (?string $value): array => ['password_hash' => $value],
        );
    }

    public function getAuthPasswordName(): string
    {
        return 'password_hash';
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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        if (! $this->canGenerateImageConversions()) {
            return;
        }

        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 150, 150)
            ->sharpen(10)
            ->nonQueued();
    }

    private function canGenerateImageConversions(): bool
    {
        return function_exists('imagecreatefromstring') || extension_loaded('imagick');
    }

    public function getAvatarUrlAttribute(): string
    {
        if (filled($this->avatar)) {
            return Storage::url((string) $this->avatar);
        }

        $avatarMedia = $this->getFirstMedia('avatar');

        if ($avatarMedia !== null) {
            if ($avatarMedia->hasGeneratedConversion('thumb')) {
                return $avatarMedia->getUrl('thumb');
            }

            return $avatarMedia->getUrl();
        }

        return sprintf(
            'https://ui-avatars.com/api/?name=%s&background=0D6EFD&color=fff&size=160',
            urlencode((string) $this->name)
        );
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
