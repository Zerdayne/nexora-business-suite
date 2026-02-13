<?php

use App\Http\Middleware\RequireFeature;
use App\Http\Middleware\RequireModuleActive;
use App\Http\Middleware\RequirePermission;
use App\Http\Middleware\RequireRole;
use App\Http\Middleware\ResolveTenantMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant' => ResolveTenantMiddleware::class,
            'perm' => RequirePermission::class,
            'role' => RequireRole::class,
            'module' => RequireModuleActive::class,
            'feature' => RequireFeature::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
