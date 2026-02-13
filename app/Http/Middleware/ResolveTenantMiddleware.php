<?php

namespace App\Http\Middleware;

use App\Tenancy\TenantContext;
use App\Tenancy\TenantContextManager;
use App\Tenancy\TenantResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class ResolveTenantMiddleware
{
    public function __construct(
        private TenantResolver       $resolver,
        private TenantContextManager $manager
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->resolver->resolve($request);

        if (! $tenant) {
            return response()->json([
                'message' => 'Tenant could not be resolved.',
            ], 400);
        }

        if ($tenant->status !== 'active') {
            return response()->json([
                'message' => 'Tenant is not active.',
            ], 403);
        }

        $this->manager->set(new TenantContext(
            tenantId: $tenant->id,
            slug: $tenant->slug,
            planKey: $tenant->plan_key,
            billingStatus: $tenant->billing_status,
            entitlementsVersion: $tenant->entitlements_version
        ));

        return $next($request);
    }
}
