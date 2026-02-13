<?php

namespace App\Http\Middleware;

use App\Models\TenantEntitlement;
use App\Tenancy\TenantContextManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireModuleActive
{
    public function __construct(private readonly TenantContextManager $manager) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $moduleKey): Response
    {
        $tenantId = $this->manager->require()->tenantId;
        $featureKey = "module.$moduleKey.enabled";

        $ent = TenantEntitlement::query()
            ->where('tenant_id', $tenantId)
            ->where('feature_key', $featureKey)
            ->first();

        $enabled = $ent?->value['enabled'] ?? false;

        if (! $enabled) {
            return response()->json(['message' => 'Module not active.'], 402);
        }

        return $next($request);
    }
}
