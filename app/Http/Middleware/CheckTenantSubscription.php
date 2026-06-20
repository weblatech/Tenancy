<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();
        if ($tenant) {
            $status = $tenant->subscription_status ?? 'active';
            $endsAt = $tenant->subscription_ends_at;

            // Enforce suspension status
            if ($status === 'suspended') {
                return response()->view('errors.suspended', ['tenant' => $tenant], 403);
            }

            // Enforce subscription expiration
            if ($status === 'expired' || ($endsAt && \Carbon\Carbon::parse($endsAt)->isPast())) {
                // Allow logout or session clearance
                if ($request->is('shop/logout') || $request->is('logout')) {
                    return $next($request);
                }
                return response()->view('errors.expired', ['tenant' => $tenant], 403);
            }
        }

        return $next($request);
    }
}
