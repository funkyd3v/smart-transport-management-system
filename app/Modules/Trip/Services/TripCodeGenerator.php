<?php

declare(strict_types=1);

namespace App\Modules\Trip\Services;

use App\Modules\Trip\Models\Trip;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class TripCodeGenerator
{
    public function generate(): string
    {
        return DB::transaction(function (): string {
            $today = CarbonImmutable::now();
            $prefix = 'TRP-'.$today->format('Ymd').'-';

            $lastCode = Trip::query()
                ->where('trip_code', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value('trip_code');

            $nextNumber = 1;

            if (is_string($lastCode)) {
                $lastPart = (int) substr($lastCode, -4);
                $nextNumber = $lastPart + 1;
            }

            return $prefix.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }
}
