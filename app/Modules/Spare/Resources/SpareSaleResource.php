<?php

declare(strict_types=1);

namespace App\Modules\Spare\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpareSaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ulid' => $this->ulid,
            'buyer_name' => $this->buyer_name,
            'quantity' => $this->quantity,
            'sale_price' => (float) $this->sale_price,
            'purchase_price_snapshot' => (float) $this->purchase_price_snapshot,
            'profit' => (float) $this->profit,
            'note' => $this->note,
            'sold_at' => $this->sold_at?->toDateString(),
            'sale_type' => [
                'id' => $this->saleType?->id,
                'name' => $this->saleType?->name,
            ],
            'spare_part' => [
                'id' => $this->sparePart?->id,
                'name' => $this->sparePart?->name,
            ],
            'created_by' => [
                'id' => $this->creator?->id,
                'name' => $this->creator?->name,
            ],
        ];
    }
}
