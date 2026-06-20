<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::whereNotNull('tenant_id')->first();
if (!$user) {
    echo "No tenant user found.\n";
    exit;
}

$tenant = \App\Models\Tenant::find($user->tenant_id);
if (!$tenant) {
    echo "No tenant found.\n";
    exit;
}

tenancy()->initialize($tenant);

$settings = \App\Models\StoreSetting::first();
if ($settings) {
    echo "Announcement settings:\n";
    echo "announcement_font_size: " . var_export($settings->announcement_font_size, true) . "\n";
    echo "announcement_active: " . var_export($settings->announcement_active, true) . "\n";
    echo "announcement_marquee: " . var_export($settings->announcement_marquee, true) . "\n";
    echo "announcement_text: " . var_export($settings->announcement_text, true) . "\n";
    echo "enable_rtl: " . var_export($settings->enable_rtl, true) . "\n";
} else {
    echo "No settings record found.\n";
}
