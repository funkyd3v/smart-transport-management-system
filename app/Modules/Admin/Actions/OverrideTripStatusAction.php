<?php

declare(strict_types=1);

namespace App\Modules\Admin\Actions;

use App\Modules\Trip\Models\Trip;
use App\Modules\Trip\Models\TripStatus;

final class OverrideTripStatusAction
{
    public function execute(Trip $trip, string $statusName): Trip
    {
        $statusId = TripStatus::query()->where('name', $statusName)->value('id');

        if ($statusId !== null) {
            $trip->forceFill(['status_id' => (int) $statusId])->save();
        }

        return $trip->refresh();
    }
}
