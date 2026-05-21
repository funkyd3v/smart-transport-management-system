<?php

declare(strict_types=1);

namespace App\Modules\Admin\Models;

use App\Models\User;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class LoginHistory extends Model
{
    use HasUlid;

    public $timestamps = false;

    protected $fillable = [
        'ulid',
        'user_id',
        'ip_address',
        'user_agent',
        'status',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getParsedUserAgentAttribute(): array
    {
        $agent = strtolower((string) ($this->user_agent ?? ''));

        $browser = 'Unknown';
        if (str_contains($agent, 'edg/')) {
            $browser = 'Edge';
        } elseif (str_contains($agent, 'chrome/')) {
            $browser = 'Chrome';
        } elseif (str_contains($agent, 'safari/') && ! str_contains($agent, 'chrome/')) {
            $browser = 'Safari';
        } elseif (str_contains($agent, 'firefox/')) {
            $browser = 'Firefox';
        } elseif (str_contains($agent, 'opr/') || str_contains($agent, 'opera')) {
            $browser = 'Opera';
        }

        $os = 'Unknown';
        if (str_contains($agent, 'windows')) {
            $os = 'Windows';
        } elseif (str_contains($agent, 'android')) {
            $os = 'Android';
        } elseif (str_contains($agent, 'iphone') || str_contains($agent, 'ipad') || str_contains($agent, 'ios')) {
            $os = 'iOS';
        } elseif (str_contains($agent, 'mac os') || str_contains($agent, 'macintosh')) {
            $os = 'macOS';
        } elseif (str_contains($agent, 'linux')) {
            $os = 'Linux';
        }

        $device = 'desktop';
        if (str_contains($agent, 'ipad') || str_contains($agent, 'tablet')) {
            $device = 'tablet';
        } elseif (str_contains($agent, 'mobile') || str_contains($agent, 'android') || str_contains($agent, 'iphone')) {
            $device = 'mobile';
        }

        return [
            'browser' => $browser,
            'os' => $os,
            'device' => $device,
        ];
    }
}
