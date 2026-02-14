<?php

namespace App\Http\Middleware;

use App\Features\FeatureGate;
use App\Tenancy\TenantContextManager;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $shared = parent::share($request);
        $user = $request->user();

        /** @var TenantContextManager $manager */
        $manager = app(TenantContextManager::class);
        $ctx = $manager->get();

        if (! $ctx) {
            return array_merge($shared, [
                'auth' => [
                    'user' => $user,
                ],
                'nexora' => null,
            ]);
        }

        /** @var FeatureGate $gate */
        $gate = app(FeatureGate::class);

        // Permissions (tenant-scoped, cached über Redis)
        $permissions = [];
        if ($user) {
            $permissions = $user->permissionsForTenant($ctx->tenantId);
        }

        // Snapshot (feature_key => { ... })
        $entitlements = $gate->snapshot();

        // Active modules aus module.*.enabled ableiten
        $activeModules = [];
        foreach ($entitlements as $featureKey => $value) {
            if (str_starts_with($featureKey, 'module.') && str_ends_with($featureKey, '.enabled')) {
                if (($value['enabled'] ?? false) === true) {
                    // module.crm.enabled -> crm
                    $activeModules[] = explode('.', $featureKey)[1] ?? null;
                }
            }
        }

        $activeModules = array_values(array_filter($activeModules));

        return array_merge($shared, [
            'auth' => [
                'user' => $user,
            ],
            'nexora' => [
                'tenant' => [
                    'id' => $ctx->tenantId,
                    'slug' => $ctx->slug,
                    'planKey' => $ctx->planKey,
                    'billingStatus' => $ctx->billingStatus,
                    'entitlementsVersion' => $ctx->entitlementsVersion,
                ],
                'permissions' => $permissions,
                'entitlements' => $entitlements,
                'activeModules' => $activeModules,

                // praktische "Systemwerte" direkt mitgeben
                'includedSeats' => (int) ($entitlements['system.seats.included']['limit'] ?? 0),
                'includedApiUnits' => (int) ($entitlements['system.api.units.included']['limit'] ?? 0),
                'apiOverageAllowed' => (bool) ($entitlements['system.api.overage.allowed']['enabled'] ?? false),
            ],
        ]);
    }
}
