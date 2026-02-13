<?php

namespace App\Providers;

use App\Tenancy\TenantContextManager;
use App\Tenancy\TenantResolver;
use Illuminate\Support\ServiceProvider;

class TenancyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(TenantContextManager::class);
        $this->app->singleton(TenantResolver::class);
    }
}
