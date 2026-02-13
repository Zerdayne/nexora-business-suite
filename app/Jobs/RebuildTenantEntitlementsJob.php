<?php

namespace App\Jobs;

use App\Entitlements\EntitlementsRebuilder;
use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RebuildTenantEntitlementsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly string $tenantId) {}

    /**
     * Execute the job.
     */
    public function handle(EntitlementsRebuilder $rebuilder): void
    {
        $tenant = Tenant::query()->findOrFail($this->tenantId);
        $rebuilder->rebuildForTenant($tenant);
    }
}
