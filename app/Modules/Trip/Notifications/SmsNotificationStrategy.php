<?php

declare(strict_types=1);

namespace App\Modules\Trip\Notifications;

use App\Modules\Trip\Models\TripNotification;
use App\Modules\Trip\Notifications\Contracts\NotificationStrategyInterface;

class SmsNotificationStrategy implements NotificationStrategyInterface
{
    public function send(TripNotification $notification): bool
    {
        $smsEnabled = filter_var(setting('sms_enabled', false), FILTER_VALIDATE_BOOLEAN);
        $provider = (string) setting('sms_provider', '');
        $apiKey = (string) setting('sms_api_key', '');
        $senderName = (string) setting('sms_sender_name', '');

        if (! $smsEnabled || $provider === '' || $apiKey === '' || $senderName === '') {
            return false;
        }

        return true;
    }
}
