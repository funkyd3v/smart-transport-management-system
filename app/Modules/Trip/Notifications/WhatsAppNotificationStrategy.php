<?php

declare(strict_types=1);

namespace App\Modules\Trip\Notifications;

use App\Modules\Trip\Models\TripNotification;
use App\Modules\Trip\Notifications\Contracts\NotificationStrategyInterface;

class WhatsAppNotificationStrategy implements NotificationStrategyInterface
{
    public function send(TripNotification $notification): bool
    {
        $whatsappEnabled = filter_var(setting('whatsapp_enabled', false), FILTER_VALIDATE_BOOLEAN);
        $apiKey = (string) setting('whatsapp_api_key', '');
        $senderNumber = (string) setting('whatsapp_sender_number', '');

        if (! $whatsappEnabled || $apiKey === '' || $senderNumber === '') {
            return false;
        }

        return true;
    }
}
