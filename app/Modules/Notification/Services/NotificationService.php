<?php

namespace App\Modules\Notification\Services;

use App\Modules\Notification\Repositories\NotificationRepositoryInterface;

class NotificationService
{
    public function __construct(protected NotificationRepositoryInterface $repository) {}
}
