<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InitializeTenantFlexible
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Try domain-based tenancy first (works with custom domains)
        $host = $request->getHost();
        $centralDomains = config('tenancy.central_domains', []);

        if (!in_array($host, $centralDomains)) {
            // Not a central domain — try to find tenant by domain
            try {
                $tenant = \Stancl\Tenancy\Database\Models\Domain::where('domain', $host)->first()?->tenant;
                if ($tenant) {
                    tenancy()->initialize($tenant);
                    return $next($request);
                }
            } catch (\Exception $e) {
                // Domain not found, continue to fallback
            }
        }

        // 2. Fallback: initialize from authenticated user's tenant_id (for Render central domain)
        if (Auth::check() && Auth::user()->tenant_id) {
            $tenant = \App\Models\Tenant::find(Auth::user()->tenant_id);
            if ($tenant) {
                try {
                    tenancy()->initialize($tenant);
                } catch (\Exception $e) {
                    // Already initialized or not found
                }
            }
        }

        return $next($request);
    }
}
