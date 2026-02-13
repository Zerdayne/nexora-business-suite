<?php

namespace App\Http\Middleware;

use App\Features\FeatureGate;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RequireFeature
{
    public function __construct(private readonly FeatureGate $gate) {}

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        $this->gate->assertEnabled($featureKey, 402);

        return $next($request);
    }
}
