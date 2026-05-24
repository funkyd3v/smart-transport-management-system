<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Spare\Models\SpareCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class SpareCategorySeeder extends Seeder
{
    public function run(): void
    {
        Cache::forget('spare_categories');

        $categories = [
            'Engine & Drivetrain' => 'Engine components, crankshafts, camshafts, and drivetrain parts.',
            'Brakes & Suspension' => 'Brake pads, discs, calipers, shock absorbers, and suspension links.',
            'Tyres & Wheels' => 'Tyres, tubes, rims, and wheel-related accessories.',
            'Electrical & Battery' => 'Batteries, alternators, starters, wiring harnesses, and electrical components.',
            'Fuel System' => 'Fuel pumps, injectors, filters, and fuel tank components.',
            'Transmission & Clutch' => 'Gearboxes, clutch plates, pressure plates, and transmission components.',
            'Body & Cabin' => 'Truck body panels, cabin fittings, mirrors, and interior parts.',
            'Exhaust System' => 'Exhaust pipes, mufflers, catalytic converters, and turbocharger components.',
            'Security & Accessories' => 'Locks, alarms, tracking accessories, and vehicle security parts.',
            'Maintenance & Consumables' => 'Engine oil, filters, grease, belts, coolant, and routine consumables.',
        ];

        foreach ($categories as $name => $description) {
            SpareCategory::query()->firstOrCreate(
                ['name' => $name],
                ['description' => $description],
            );
        }

        Cache::forget('spare_categories');
    }
}
