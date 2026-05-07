<?php

declare(strict_types=1);

namespace App\Modules\Trip\Actions;

use App\Modules\Trip\Models\ReloadHistory;
use App\Modules\Trip\Models\Trip;

class AddReloadHistoryAction
{
    public function __invoke(Trip $trip, int $truckId, int $driverId, ?string $reloadPoint = null, ?string $note = null): ReloadHistory
    {
        return $trip->reloadHistory()->create([
            'truck_id' => $truckId,
            'driver_id' => $driverId,
            'reload_point' => $reloadPoint,
            'reloaded_at' => now(),
            'note' => $note,
            'created_at' => now(),
        ]);
    }
}
