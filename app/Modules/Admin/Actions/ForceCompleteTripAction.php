<?php

declare(strict_types=1);

namespace App\Modules\Admin\Actions;

use App\Modules\Trip\Models\Trip;

final class ForceCompleteTripAction
{
    public function execute(Trip $trip, int $updatedBy, ?string $reason = null): Trip
    {
        $trip->forceFill([
            'completed_at' => now(),
            'notes' => trim((string) ($trip->notes.'\n[Admin Force Complete] '.($reason ?? 'No reason provided.'))),
        ])->save();

        return $trip->refresh();
    }
}
