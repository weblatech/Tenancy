<?php

if (!function_exists('tenant_store_url')) {
    function tenant_store_url(?string $path = ''): string
    {
        $tenantId = tenant('id') ?? (Auth::check() ? Auth::user()->tenant_id : null);
        $centralDomains = config('tenancy.central_domains', ['localhost']);
        $host = request()->getHost();
        $isLocal = in_array($host, ['localhost', '127.0.0.1']);

        if ($isLocal) {
            $base = "http://{$tenantId}.localhost:8000";
        } else {
            $base = request()->getScheme() . "://{$host}";
        }

        return $base . '/' . ltrim($path, '/');
    }
}
