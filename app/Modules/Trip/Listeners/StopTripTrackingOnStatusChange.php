<?php

declare(strict_types=1);

namespace App\Modules\Trip\Listeners;

use App\Modules\Trip\Enums\TripStatus;
use App\Modules\Trip\Events\TripStatusChanged;
use App\Modules\Trip\Services\VehicleTrackingService;

class StopTripTrackingOnStatusChange
{
    public function __construct(private readonly VehicleTrackingService $vehicleTrackingService) {}

    public function handle(TripStatusChanged $event): void
    {
        if (! in_array($event->to, [TripStatus::Completed, TripStatus::Cancelled], true)) {
            return;
        }

        $this->vehicleTrackingService->stopTracking(
            $event->trip,
            sprintf('Live tracking stopped because trip moved to %s.', $event->to->label()),
        );
    }
}
