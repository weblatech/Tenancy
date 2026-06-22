<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // یہاں ہم نے دکان (Tenant) کے راستوں کو لاراویل 12 کے مطابق رجسٹر کر دیا ہے
        then: function () {
            // Domain-based tenancy (custom domains)
            require dirname(__DIR__) . '/routes/tenant.php';

            // Path-based tenancy for Render (/{tenant}/shop, /{tenant}/, etc.)
            Route::prefix('{tenant}')->middleware([
                'web',
                \App\Http\Middleware\InitializeTenantFlexible::class,
            ])->group(dirname(__DIR__) . '/routes/tenant.php');
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'admin/*',
            'webhook/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();