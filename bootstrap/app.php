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
            // Direct page route — registered OUTSIDE tenant middleware group
            Route::get('/{tenant}/page/{slug}', function ($tenant, $slug) {
                // Initialize tenancy
                $t = \App\Models\Tenant::find($tenant);
                if (!$t) { abort(404, 'Store not found'); }
                tenancy()->initialize($t);

                $page = \App\Models\Page::where('slug', $slug)->first();
                if (!$page) {
                    $allSlugs = \App\Models\Page::pluck('slug')->implode(', ');
                    abort(404, "Page '{$slug}' not found. Available: [{$allSlugs}]");
                }
                if (!$page->is_active) {
                    $page->update(['is_active' => true]);
                }
                $settings = \App\Models\StoreSetting::firstOrCreate(['id' => 1]);
                return view('tenant.page', [
                    'tenantId' => tenant('id'),
                    'settings' => $settings,
                    'page' => $page
                ]);
            });

            // Load remaining tenant routes with {tenant} prefix
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