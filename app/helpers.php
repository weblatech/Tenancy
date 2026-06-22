<?php

if (!function_exists('tenant_store_url')) {
    function tenant_store_url(?string $path = ''): string
    {
        $tenantId = tenant('id') ?? (Auth::check() ? Auth::user()->tenant_id : null);
        $host = request()->getHost();
        $isLocal = in_array($host, ['localhost', '127.0.0.1']);

        if ($isLocal) {
            $base = "http://{$tenantId}.localhost:8000";
            return $base . '/' . ltrim($path, '/');
        }

        $base = request()->getScheme() . "://{$host}";

        if ($tenantId && $path !== null) {
            return $base . '/' . $tenantId . '/' . ltrim($path, '/');
        }

        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('tenant_plan')) {
    function tenant_plan(): string
    {
        return tenant('subscription_plan') ?? 'starter';
    }
}

if (!function_exists('plan_feature')) {
    function plan_feature(string $feature): bool
    {
        $plan = tenant_plan();
        
        $features = [
            'starter' => [
                'products' => true,
                'orders' => true,
                'customers' => true,
                'customizer' => true,
                'pages' => true,
                'messages' => true,
                'subscribers' => true,
                'payments' => true,
                'custom_domain' => false,
                'social_tracking' => false,
                'whatsapp_chat' => false,
                'unlimited_products' => false,
                'premium_customizer' => false,
            ],
            'growth' => [
                'products' => true,
                'orders' => true,
                'customers' => true,
                'customizer' => true,
                'pages' => true,
                'messages' => true,
                'subscribers' => true,
                'payments' => true,
                'custom_domain' => true,
                'social_tracking' => true,
                'whatsapp_chat' => true,
                'unlimited_products' => true,
                'premium_customizer' => true,
            ],
            'enterprise' => [
                'products' => true,
                'orders' => true,
                'customers' => true,
                'customizer' => true,
                'pages' => true,
                'messages' => true,
                'subscribers' => true,
                'payments' => true,
                'custom_domain' => true,
                'social_tracking' => true,
                'whatsapp_chat' => true,
                'unlimited_products' => true,
                'premium_customizer' => true,
                'dedicated_designer' => true,
                'database_tuning' => true,
                'priority_support' => true,
            ],
        ];
        
        return $features[$plan][$feature] ?? false;
    }
}

if (!function_exists('plan_max_products')) {
    function plan_max_products(): ?int
    {
        $plan = tenant_plan();
        $limits = [
            'starter' => 50,
            'growth' => null,
            'enterprise' => null,
        ];
        return $limits[$plan] ?? 50;
    }
}

if (!function_exists('plan_max_staff')) {
    function plan_max_staff(): ?int
    {
        $plan = tenant_plan();
        $limits = [
            'starter' => 1,
            'growth' => 5,
            'enterprise' => null,
        ];
        return $limits[$plan] ?? 1;
    }
}
