<?php

declare(strict_types=1);

namespace App\Modules\Trip\Notifications\Contracts;

use App\Modules\Trip\Models\TripNotification;

interface NotificationStrategyInterface
{
    public function send(TripNotification $notification): bool;
}
