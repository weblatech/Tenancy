<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InitializeTenantFlexible
{
    public function handle(Request $request, Closure $next)
    {
        \Illuminate\Support\Facades\Log::info("TENANT MIDDLEWARE", [
            'url' => $request->fullUrl(),
            'host' => $request->getHost(),
            'route_tenant' => $request->route('tenant'),
            'tenancy_initialized' => tenancy()->initialized,
        ]);

        // Already initialized by route parameter?
        if (tenancy()->initialized) {
            return $next($request);
        }

        $host = $request->getHost();
        $centralDomains = config('tenancy.central_domains', ['localhost']);

        // 1. Try domain-based tenancy (custom domains)
        if (!in_array($host, $centralDomains)) {
            try {
                $domain = \Stancl\Tenancy\Database\Models\Domain::where('domain', $host)->first();
                if ($domain && $domain->tenant) {
                    tenancy()->initialize($domain->tenant);
                    return $next($request);
                }
            } catch (\Exception $e) {}
        }

        // 2. Try route parameter {tenant}
        $tenantParam = $request->route('tenant');
        if ($tenantParam) {
            $tenant = \App\Models\Tenant::find($tenantParam);
            \Illuminate\Support\Facades\Log::info("TENANT MIDDLEWARE route param", [
                'tenantParam' => $tenantParam,
                'found' => $tenant ? true : false,
                'tenant_id' => $tenant?->id ?? 'null',
            ]);
            if ($tenant) {
                tenancy()->initialize($tenant);
                return $next($request);
            }
            \Illuminate\Support\Facades\Log::warning("TENANT MIDDLEWARE aborting 404", ['tenantParam' => $tenantParam]);
            abort(404, 'Store not found');
        }

        // 3. Fallback: user-based tenancy (for /shop on central domain)
        if (Auth::check() && Auth::user()->tenant_id) {
            $tenant = \App\Models\Tenant::find(Auth::user()->tenant_id);
            if ($tenant) {
                try {
                    tenancy()->initialize($tenant);
                } catch (\Exception $e) {}
            }
        }

        return $next($request);
    }
}
