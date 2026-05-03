<?php

namespace App\Modules\Notification\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notification\Requests\StoreNotificationRequest;
use App\Modules\Notification\Requests\UpdateNotificationRequest;
use App\Modules\Notification\Services\NotificationService;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $service) {}

    public function index() {}

    public function store(StoreNotificationRequest $request) {}

    public function show(string $ulid) {}

    public function update(UpdateNotificationRequest $request, string $ulid) {}

    public function destroy(string $ulid) {}
}
