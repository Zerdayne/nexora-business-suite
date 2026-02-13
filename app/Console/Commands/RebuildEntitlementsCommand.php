<?php

namespace App\Console\Commands;

use App\Entitlements\EntitlementsRebuilder;
use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class RebuildEntitlementsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'entitlements:rebuild {tenant? : Tenant ID or slug} {--all : Rebuild for all tenants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild tenant entitlements and bump entitlements_version';

    /**
     * Execute the console command.
     */
    public function handle(EntitlementsRebuilder $rebuilder): int
    {
        if ($this->option('all')) {
            $this->info('Rebuilding entitlements for ALL tenants...');
            Tenant::query()->chunkById(100, function ($tenants) use ($rebuilder) {
                foreach ($tenants as $tenant) {
                    $v = $rebuilder->rebuildForTenant($tenant);
                    $this->line(" - $tenant->slug ($tenant->id) => version $v");
                }
            });

            return self::SUCCESS;
        }

        $arg = $this->argument('tenant');
        if (! $arg) {
            $this->error('Provide {tenant} or use --all');

            return self::FAILURE;
        }

        $tenant = Tenant::query()
            ->where(function ($query) use ($arg) {
                if (Str::isUuid($arg)) {
                    $query->where('id', $arg);
                }

                $query->orWhere('slug', $arg);
            })
            ->first();

        if (! $tenant) {
            $this->error("Tenant not found: $arg");

            return self::FAILURE;
        }

        $v = $rebuilder->rebuildForTenant($tenant);
        $this->info("Rebuilt entitlements for tenant {$tenant->slug} ($tenant->id) => version $v");

        return self::SUCCESS;
    }
}
