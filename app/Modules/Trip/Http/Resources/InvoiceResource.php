<?php

declare(strict_types=1);

namespace App\Modules\Trip\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'ulid' => $this->ulid,
            'invoice_number' => $this->invoice_number,
            'trip_ulid' => $this->trip?->ulid,
            'subtotal' => $this->subtotal,
            'advance_paid' => $this->advance_paid,
            'due_amount' => $this->due_amount,
            'total_amount' => $this->total_amount,
            'issued_at' => $this->issued_at,
        ];
    }
}
