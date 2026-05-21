<?php

declare(strict_types=1);

namespace App\Modules\Admin\Models;

use App\Models\User;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

final class CompanySetting extends Model
{
    use HasUlid;

    protected $fillable = [
        'ulid',
        'user_id',
        'company_name',
        'trade_license',
        'phone',
        'email',
        'address',
        'website',
        'logo_path',
        'signature_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getLogoUrlAttribute(): ?string
    {
        if (blank($this->logo_path)) {
            return null;
        }

        return Storage::url((string) $this->logo_path);
    }

    public function getSignatureUrlAttribute(): ?string
    {
        if (blank($this->signature_path)) {
            return null;
        }

        return Storage::url((string) $this->signature_path);
    }
}
