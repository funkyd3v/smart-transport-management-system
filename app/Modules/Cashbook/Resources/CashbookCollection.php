<?php

namespace App\Modules\Cashbook\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CashbookCollection extends ResourceCollection
{
    public function toArray(Request $request): array
    {
        return [
            'data' => CashbookResource::collection($this->collection),
        ];
    }
}
