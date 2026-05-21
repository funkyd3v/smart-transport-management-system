<?php

declare(strict_types=1);

namespace App\Modules\Admin\Models;

use App\Models\User;
use App\Modules\Shared\Traits\HasUlid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class NotificationPreference extends Model
{
    use HasUlid;

    public const EVENTS = [
        'trip_created',
        'trip_completed',
        'invoice_generated',
        'payment_received',
        'due_raised',
        'due_collected',
        'driver_status_changed',
        'low_spare_inventory',
        'client_added',
        'daily_summary',
    ];

    public const CHANNELS = ['in_app', 'email', 'sms'];

    protected $fillable = [
        'ulid',
        'user_id',
        'event',
        'channel',
        'enabled',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaultPreferences(): array
    {
        $defaults = [];

        foreach (self::EVENTS as $event) {
            foreach (self::CHANNELS as $channel) {
                $defaults[] = [
                    'event' => $event,
                    'channel' => $channel,
                    'enabled' => true,
                ];
            }
        }

        return $defaults;
    }

    public static function getForUser(string $userId): Collection
    {
        $existing = self::query()
            ->where('user_id', $userId)
            ->get()
            ->keyBy(fn (self $item): string => $item->event.'_'.$item->channel);

        $merged = collect(self::getDefaultPreferences())->map(function (array $default) use ($existing, $userId): self {
            $key = $default['event'].'_'.$default['channel'];

            if ($existing->has($key)) {
                return $existing->get($key);
            }

            $model = new self;
            $model->forceFill([
                'user_id' => $userId,
                'event' => $default['event'],
                'channel' => $default['channel'],
                'enabled' => $default['enabled'],
            ]);

            return $model;
        });

        return new Collection($merged->all());
    }
}
