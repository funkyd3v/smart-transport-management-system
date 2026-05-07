<?php

declare(strict_types=1);

namespace App\Modules\Trip\Enums;

enum NotificationChannel: string
{
    case TripStart = 'trip_start';
    case TripComplete = 'trip_complete';
    case Invoice = 'invoice';
    case DueReminder = 'due_reminder';
    case ThankYou = 'thank_you';
}
