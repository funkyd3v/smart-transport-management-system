<?php

declare(strict_types=1);

namespace App\Modules\Admin\Models;

use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Model;

final class SystemSetting extends Model
{
    use HasUlid;

    protected $table = 'system_settings';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'string',
        ];
    }

    public function setUlidAttribute(string $value): void
    {
        $this->attributes['id'] = $value;
    }

    public function getUlidAttribute(): ?string
    {
        return $this->attributes['id'] ?? null;
    }

    public static function getValue(string $group, string $key, mixed $default = null): mixed
    {
        $value = self::query()
            ->where('group', $group)
            ->where('key', $key)
            ->value('value');

        return $value ?? $default;
    }

    public static function setValue(string $group, string $key, mixed $value): void
    {
        self::query()->updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => is_array($value) ? json_encode($value, JSON_THROW_ON_ERROR) : (string) $value],
        );
    }
}
