<?php

declare(strict_types=1);

namespace App\Modules\Trip\Actions;

use App\Modules\Trip\Models\ReloadHistory;
use App\Modules\Trip\Models\Trip;
use Illuminate\Support\Carbon;

class AddReloadHistoryAction
{
    public function __invoke(
        Trip $trip,
        int $truckId,
        int $driverId,
        ?string $reloadPoint = null,
        ?string $note = null,
        ?float $reloadAmount = null,
        ?string $reloadedAt = null,
    ): ReloadHistory {
        return $trip->reloadHistory()->create([
            'truck_id' => $truckId,
            'driver_id' => $driverId,
            'reload_point' => $reloadPoint,
            'reloaded_at' => filled($reloadedAt) ? Carbon::parse($reloadedAt) : now(),
            'note' => json_encode([
                'reload_amount' => $reloadAmount,
                'note' => $note,
            ], JSON_THROW_ON_ERROR),
            'created_at' => now(),
        ]);
    }
}
