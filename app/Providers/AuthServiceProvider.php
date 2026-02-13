<?php

namespace App\Providers;

use App\Models\User;
use App\Tenancy\TenantContextManager;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            // TenantContext muss gesetzt sein (Tenant Middleware)
            /** @var TenantContextManager $manager */
            $manager = app(TenantContextManager::class);
            $ctx = $manager->get();

            if (! $ctx) {
                return null;
            } // Kein Context => Keine Entscheidung

            // @TODO: Optional -> Super-Admin Rolle Check

            return $user->hasPermission($ability, $ctx->tenantId);
        });
    }
}
