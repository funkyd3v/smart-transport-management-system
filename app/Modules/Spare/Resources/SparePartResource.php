<?php

declare(strict_types=1);

namespace App\Modules\Spare\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SparePartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ulid' => $this->ulid,
            'name' => $this->name,
            'condition' => $this->condition,
            'quantity' => (int) $this->quantity,
            'purchase_price' => (float) $this->purchase_price,
            'source_memo_number' => $this->source_memo_number,
            'source_truck_id' => $this->source_truck_id,
            'source_truck_number' => $this->sourceTruck?->truck_number,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
            ],
        ];
    }
}
