<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Client\Models\ClientCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $definitions = [
                'Port' => 'Port-based transport clients.',
                'Contractual' => 'Contract-based transport clients.',
                'Mega Project' => 'Large project transport clients.',
            ];

            $canonicalIds = [];

            foreach ($definitions as $name => $description) {
                $category = ClientCategory::query()->firstOrCreate(
                    ['name' => $name],
                    ['description' => $description]
                );

                $canonicalIds[$name] = (int) $category->id;
            }

            $nameToCanonical = [
                'port' => 'Port',
                'port client' => 'Port',
                'contractual' => 'Contractual',
                'contractual client' => 'Contractual',
                'mega project' => 'Mega Project',
                'mega project client' => 'Mega Project',
                'mega_project' => 'Mega Project',
                'mega_project_client' => 'Mega Project',
            ];

            $allCategories = ClientCategory::query()->get(['id', 'name']);

            foreach ($allCategories as $category) {
                $normalized = strtolower(str_replace('-', ' ', trim((string) $category->name)));
                $canonicalName = $nameToCanonical[$normalized] ?? null;

                if ($canonicalName === null) {
                    continue;
                }

                $targetId = $canonicalIds[$canonicalName];

                if ((int) $category->id === $targetId) {
                    continue;
                }

                DB::table('clients')
                    ->where('category_id', (int) $category->id)
                    ->update(['category_id' => $targetId]);
            }

            DB::table('clients')
                ->whereNotIn('category_id', array_values($canonicalIds))
                ->update(['category_id' => $canonicalIds['Port']]);

            ClientCategory::query()
                ->whereNotIn('id', array_values($canonicalIds))
                ->delete();
        });
    }
}
