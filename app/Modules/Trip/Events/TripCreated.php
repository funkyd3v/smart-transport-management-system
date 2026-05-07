<?php

declare(strict_types=1);

namespace App\Modules\Trip\Events;

use App\Modules\Trip\Models\Trip;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Trip $trip) {}
}
