<?php

namespace App\Providers;

use App\Entitlements\EntitlementsRebuilder;
use App\Features\FeatureGate;
use Illuminate\Support\ServiceProvider;

class EntitlementsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(EntitlementsRebuilder::class);
        $this->app->singleton(FeatureGate::class);
    }
}
