<?php

namespace Database\Seeders;

use App\Entitlements\EntitlementsRebuilder;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CatalogSeeder::class,
            PermissionSeeder::class,
            TenantSeeder::class,
        ]);

        $rebuilder = app(EntitlementsRebuilder::class);
        Tenant::query()->get()->each(fn (Tenant $tenant) => $rebuilder->rebuildForTenant($tenant));
    }
}
