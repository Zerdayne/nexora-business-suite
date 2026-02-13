<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Module;
use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Plans
        $base = Plan::query()->updateOrCreate(
            ['key' => 'base'],
            [
                'id' => Str::uuid(),
                'name' => 'Base',
                'included_seats' => 5,
                'included_api_units' => 100_000,
                'api_overage_allowed' => true,
                'is_active' => true,
            ]
        );

        $growth = Plan::query()->updateOrCreate(
            ['key' => 'growth'],
            [
                'id' => Str::uuid(),
                'name' => 'Growth',
                'included_seats' => 15,
                'included_api_units' => 500_000,
                'api_overage_allowed' => true,
                'is_active' => true,
            ]
        );

        // Modules
        $crm = Module::query()->updateOrCreate(
            ['key' => 'crm'],
            [
                'id' => Str::uuid(),
                'name' => 'CRM',
                'description' => 'Deals, Accounts, Pipelines',
                'is_active' => true,
                'sort_order' => 10,
            ]
        );

        $documents = Module::query()->updateOrCreate(
            ['key' => 'documents'],
            [
                'id' => Str::uuid(),
                'name' => 'Documents',
                'description' => 'Templates, Versions, Publishing',
                'is_active' => true,
                'sort_order' => 20,
            ]
        );

        // Features (minimal)
        Feature::query()->updateOrCreate(
            ['module_id' => $crm->id, 'key' => 'export.csv'],
            [
                'id' => Str::uuid(),
                'name' => 'Export to CSV',
                'type' => 'boolean',
                'default_value' => ['enabled' => false],
            ]
        );

        Feature::query()->updateOrCreate(
            ['module_id' => $documents->id, 'key' => 'export.pdf'],
            [
                'id' => Str::uuid(),
                'name' => 'Export to PDF',
                'type' => 'boolean',
                'default_value' => ['enabled' => false],
            ]
        );

        // Plan Modules (Base enthält z.B. CRM, Documents optional)
        $base->modules()->syncWithoutDetaching([
            $crm->id => ['is_included' => true],
            $documents->id => ['is_included' => false],
        ]);

        $growth->modules()->syncWithoutDetaching([
            $crm->id => ['is_included' => true],
            $documents->id => ['is_included' => true],
        ]);
    }
}
