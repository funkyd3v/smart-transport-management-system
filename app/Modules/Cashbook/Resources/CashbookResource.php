<?php

namespace App\Modules\Cashbook\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashbookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type?->value ?? $this->type,
            'amount' => (float) $this->amount,
            'balance' => (float) $this->balance,
            'description' => $this->description,
            'entry_date' => $this->entry_date?->toDateString(),
            'reference_type' => $this->reference_type,
            'recorded_by' => $this->recordedBy?->name,
            'note' => $this->note,
            'created_at' => $this->created_at,
        ];
    }
}
