<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Spare\Models\SpareSaleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class SpareSaleTypeSeeder extends Seeder
{
    public function run(): void
    {
        Cache::forget('spare_sale_types');

        $saleTypes = [
            'spare_part' => 'Physical spare part sale from inventory stock.',
            'security_solution' => 'Security product or solution sale.',
            'monthly_maintenance' => 'Recurring monthly maintenance contract sale.',
        ];

        foreach ($saleTypes as $name => $description) {
            SpareSaleType::query()->firstOrCreate(
                ['name' => $name],
                ['description' => $description],
            );
        }

        Cache::forget('spare_sale_types');
    }
}
