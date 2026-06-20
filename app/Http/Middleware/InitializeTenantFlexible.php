<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InitializeTenantFlexible
{
    public function handle(Request $request, Closure $next)
    {
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
            } catch (\Exception $e) {
                // Domain not found
            }
        }

        // 2. Try path-based: first segment might be tenant ID (for Render)
        $segments = $request->segments();
        if (count($segments) > 0) {
            $firstSegment = $segments[0];
            $tenant = \App\Models\Tenant::find($firstSegment);
            if ($tenant) {
                // Strip tenant ID from path
                $request->offsetUnset('segments');
                array_shift($segments);
                $request->setPathInfo('/' . implode('/', $segments));

                try {
                    tenancy()->initialize($tenant);
                    return $next($request);
                } catch (\Exception $e) {
                    // Already initialized
                }
            }
        }

        // 3. Fallback: user-based tenancy (for /shop routes on central domain)
        if (Auth::check() && Auth::user()->tenant_id) {
            $tenant = \App\Models\Tenant::find(Auth::user()->tenant_id);
            if ($tenant) {
                try {
                    tenancy()->initialize($tenant);
                } catch (\Exception $e) {
                    // Already initialized
                }
            }
        }

        return $next($request);
    }
}
