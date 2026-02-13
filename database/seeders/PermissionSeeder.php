<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $perms = [
            // CRM
            ['key' => 'crm.deals.read',  'module_key' => 'crm', 'description' => 'Read deals'],
            ['key' => 'crm.deals.write', 'module_key' => 'crm', 'description' => 'Write deals'],
            ['key' => 'crm.export.csv',  'module_key' => 'crm', 'description' => 'Export deals as CSV'],

            // Documents
            ['key' => 'documents.templates.read',  'module_key' => 'documents', 'description' => 'Read templates'],
            ['key' => 'documents.templates.write', 'module_key' => 'documents', 'description' => 'Manage templates'],
            ['key' => 'documents.export.pdf',      'module_key' => 'documents', 'description' => 'Export as PDF'],

            // Tenant admin
            ['key' => 'tenant.users.invite', 'module_key' => null, 'description' => 'Invite users'],
            ['key' => 'tenant.billing.manage', 'module_key' => null, 'description' => 'Manage billing/subscription'],
        ];

        foreach ($perms as $perm) {
            Permission::query()->updateOrCreate(
                ['key' => $perm['key']],
                [
                    'id' => Str::uuid(),
                    'module_key' => $perm['module_key'],
                    'description' => $perm['description'],
                ]
            );
        }
    }
}
