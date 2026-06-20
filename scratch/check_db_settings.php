<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the first tenant settings
$tenant = \App\Models\Tenant::first();
if ($tenant) {
    tenancy()->initialize($tenant);
    $settings = \App\Models\StoreSetting::first();
    echo "Tenant: " . $tenant->id . "\n";
    echo "announcement_font_size: " . ($settings->announcement_font_size ?? 'NULL') . "\n";
    echo "header_logo_height: " . ($settings->header_logo_height ?? 'NULL') . "\n";
    echo "header_menu_bg: " . ($settings->header_menu_bg ?? 'NULL') . "\n";
    echo "header_menu_text: " . ($settings->header_menu_text ?? 'NULL') . "\n";
    echo "btn_primary_bg: " . ($settings->btn_primary_bg ?? 'NULL') . "\n";
} else {
    echo "No tenants found.\n";
}
