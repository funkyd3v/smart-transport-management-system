<?php

namespace App\Modules\Driver\Models;

use App\Modules\Auth\Models\User;
use App\Modules\Shared\Traits\HasUlid;
use App\Modules\Trip\Models\Trip;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Driver extends Model implements HasMedia
{
    use HasUlid, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'ulid',
        'user_id',
        'license_number',
        'nid_number',
        'driving_type',
        'joining_date',
        'total_trips',
        'total_profit_generated',
        'rating',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'joining_date' => 'date',
            'total_trips' => 'integer',
            'total_profit_generated' => 'decimal:2',
            'rating' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trips(): HasMany
    {
        return $this->hasMany(Trip::class);
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
            return asset('images/user/user-01.jpg');
        }

        if ($avatarMedia->hasGeneratedConversion('thumb')) {
            return $avatarMedia->getUrl('thumb');
        }

        return $avatarMedia->getUrl();
    }

    public function getNameAttribute(?string $value): ?string
    {
        return $value ?? $this->user?->name;
    }

    public function getMobileNumberAttribute(?string $value): ?string
    {
        return $value ?? $this->user?->phone;
    }

    public function getStatusAttribute(?string $value): string
    {
        if (filled($value)) {
            return (string) $value;
        }

        return $this->user?->is_active ? 'active' : 'inactive';
    }

    public function getIsApprovedAttribute(?bool $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return $this->user?->approved_at !== null;
    }
}
