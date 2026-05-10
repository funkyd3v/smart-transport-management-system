<?php

declare(strict_types=1);

namespace App\Modules\Trip\Listeners;

use App\Modules\Trip\Enums\NotificationChannel;
use App\Modules\Trip\Enums\NotificationType;
use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Events\TripStatusChanged;
use App\Modules\Trip\Jobs\SendTripNotificationJob;
use App\Modules\Trip\Models\TripNotification;

class SendTripNotification
{
    public function handle(TripStatusChanged $event): void
    {
        if (! in_array($event->to, [TripStatus::InProgress, TripStatus::Completed], true)) {
            return;
        }

        $channel = $event->to === TripStatus::InProgress
            ? NotificationChannel::TripStart
            : NotificationChannel::TripComplete;

        $notification = TripNotification::query()->create([
            'ulid' => str()->ulid()->toBase32(),
            'user_id' => $event->trip->creator->id,
            'trip_id' => $event->trip->id,
            'type' => NotificationType::System->value,
            'channel' => $channel->value,
            'recipient_phone' => null,
            'message' => sprintf('Trip %s changed to %s.', $event->trip->trip_code, $event->to->label()),
            'status' => 'pending',
            'created_at' => now(),
        ]);

        SendTripNotificationJob::dispatch($notification->id);
    }
}
