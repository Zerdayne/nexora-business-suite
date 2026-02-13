<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class RequireRole
{
    public function __construct(
        private readonly TenantContextManager $manager
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roleKey): Response
    {
        $tenantId = $this->manager->require()->tenantId;
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $hasRole = $user->roles()
            ->where('roles.key', $roleKey)
            ->wherePivot('tenant_id', $tenantId)
            ->exists();

        if (! $hasRole) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}
