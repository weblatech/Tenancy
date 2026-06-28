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
        then: function () {
            // DIRECT TEST: Route registered FIRST, no middleware, no groups
            Route::get('/purelife/page/about-us', function () {
                return response("DIRECT TEST WORKS - slug=" . request()->route('slug', 'N/A'), 200)
                    ->header('Content-Type', 'text/plain');
            });

            // Load tenant routes with {tenant} prefix
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