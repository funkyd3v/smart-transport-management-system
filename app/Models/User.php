<?php

declare(strict_types=1);

namespace App\Models;

use App\Modules\Shared\Traits\HasUlid;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasUlid, InteractsWithMedia, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ulid',
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
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
            'password' => 'hashed',
        ];
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
        $avatarMedia = $this->getFirstMedia('avatar');

        if ($avatarMedia === null) {
            return asset('images/user/owner.png');
        }

        if ($avatarMedia->hasGeneratedConversion('thumb')) {
            return $avatarMedia->getUrl('thumb');
        }

        return $avatarMedia->getUrl();
    }
}
