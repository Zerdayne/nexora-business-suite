<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1) Tenant
        $tenant = Tenant::query()->updateOrCreate(
            ['slug' => 'acme'],
            [
                'id' => Str::uuid(),
                'name' => 'ACME GmbH',
                'status' => 'active',
                'timezone' => 'Europe/Berlin',
                'plan_key' => 'base',
                'billing_status' => 'active',
                'entitlements_version' => 1,
            ]
        );

        // 2) User (Admin)
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@acme.test'],
            [
                'name' => 'ACME Admin',
                'password' => Hash::make('password'),
            ]
        );

        // 3) Tenant-User Link
        $tenant->users()->syncWithoutDetaching([
            $admin->id => [
                'status' => 'active',
                'invited_at' => now(),
                'activated_at' => now(),
            ],
        ]);

        // 4) Roles
        $roleAdmin = Role::query()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'key' => 'admin'],
            [
                'id' => Str::uuid(),
                'name' => 'Admin',
            ]
        );

        $roleManager = Role::query()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'key' => 'manager'],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Manager',
            ]
        );

        // 5) Role Permissions
        $allPermIds = Permission::query()->pluck('id')->all();
        $roleAdmin->permissions()->sync($allPermIds);

        // manager bekommt nur read perms (Beispiel)
        $managerPermIds = Permission::query()
            ->whereIn('key', ['crm.deals.read', 'documents.templates.read'])
            ->pluck('id')
            ->all();
        $roleManager->permissions()->sync($managerPermIds);

        // 6) User Roles (tenant-scoped)
        $admin->roles()->syncWithoutDetaching([
            $roleAdmin->id => ['tenant_id' => $tenant->id],
        ]);
    }
}
