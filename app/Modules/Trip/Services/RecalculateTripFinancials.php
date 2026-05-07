<?php

declare(strict_types=1);

namespace App\Modules\Trip\Services;

use App\Modules\Trip\Models\Trip;

class RecalculateTripFinancials
{
    public function execute(Trip $trip): void
    {
        $trip->total_income = (float) $trip->trip_rate;
        $trip->due_amount = max(0, (float) $trip->trip_rate - (float) $trip->advance_payment);
        $trip->profit = (float) $trip->total_income - (float) $trip->total_expense;
        $trip->save();
    }
}
