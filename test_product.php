<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $tenant = App\Models\Tenant::find('muna');
    if (!$tenant) {
        echo "Tenant 'muna' not found\n";
        exit(1);
    }
    tenancy()->initialize($tenant);
    $p = App\Models\Product::create([
        'name' => 'Test Product Fix',
        'price' => 1500,
        'compare_price' => 2000,
        'stock' => 10,
        'variant_combinations' => null,
    ]);
    echo 'SUCCESS: Product created with ID ' . $p->id . PHP_EOL;
    // Clean up
    $p->delete();
    echo 'Cleaned up test product.' . PHP_EOL;
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
}
