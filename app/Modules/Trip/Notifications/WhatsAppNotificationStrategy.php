<?php

declare(strict_types=1);

namespace App\Modules\Trip\Notifications;

use App\Modules\Trip\Models\TripNotification;
use App\Modules\Trip\Notifications\Contracts\NotificationStrategyInterface;

class WhatsAppNotificationStrategy implements NotificationStrategyInterface
{
    public function send(TripNotification $notification): bool
    {
        return true;
    }
}
