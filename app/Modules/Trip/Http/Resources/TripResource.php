<?php

declare(strict_types=1);

namespace App\Modules\Trip\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ulid' => $this->ulid,
            'trip_code' => $this->trip_code,
            'status' => $this->status?->name,
            'client' => $this->client?->name,
            'driver' => $this->driver?->user?->name,
            'truck' => $this->truck?->truck_number,
            'trip_rate' => $this->trip_rate,
            'advance_payment' => $this->advance_payment,
            'due_amount' => $this->due_amount,
            'load_date' => $this->load_date,
        ];
    }
}
