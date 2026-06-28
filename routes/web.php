<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;

$currentHost = request()->getHost();
$centralDomains = config('tenancy.central_domains', []);
$domainToRegister = in_array($currentHost, $centralDomains) ? $currentHost : ($centralDomains[0] ?? 'localhost');

Route::domain($domainToRegister)->group(function () {
        
        // ہوم پیج
        Route::get('/', function () {
            return view('welcome');
        });

        // یوزر ڈیش بورڈ
        Route::get('/dashboard', function () {
            $user = Auth::user();
            
            if ($user && $user->is_super_admin) {
                return redirect('/admin');
            }

            $productCount = 0;
            $orderCount = 0;
            $pendingOrdersCount = 0;

            if ($user && $user->tenant_id) {
                $tenant = \App\Models\Tenant::find($user->tenant_id);
                if ($tenant) {
                    try {
                        tenancy()->initialize($tenant);
                        $productCount = \App\Models\Product::count();
                        $orderCount = \App\Models\Order::count();
                        $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
                        tenancy()->end();
                    } catch (\Exception $e) {
                        // Safe fallback
                    }
                }
            }

            return view('dashboard', [
                'productCount' => $productCount,
                'orderCount' => $orderCount,
                'pendingOrdersCount' => $pendingOrdersCount,
            ]);
        })->middleware(['auth', 'verified'])->name('dashboard');

        // پروفائل کی سیٹنگز
        Route::middleware('auth')->group(function () {
            Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
            Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
            Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        });

        // بریز کے لاگ ان اور رجسٹریشن والے راؤٹس
        require __DIR__.'/auth.php';

        // ہوسٹنگ پر بغیر SSH مائگریشن رن کرنے کے لیے عارضی راؤٹ
        Route::get('/run-migrations', function () {
            try {
                \Artisan::call('migrate', ['--force' => true]);
                return 'Central migrations completed successfully!<br><pre>' . \Artisan::output() . '</pre>';
            } catch (\Exception $e) {
                return 'Error running migrations: ' . $e->getMessage();
            }
        });

        Route::get('/seed-admin', function () {
            try {
                $user = \App\Models\User::where('email', 'admin@saascommerce.com')->first();
                if (!$user) {
                    $user = \App\Models\User::create([
                        'name' => 'Super Admin',
                        'email' => 'admin@saascommerce.com',
                        'password' => Hash::make('admin123'),
                        'is_super_admin' => true,
                        'email_verified_at' => now(),
                    ]);
                } else {
                    $user->update([
                        'password' => Hash::make('admin123'),
                        'is_super_admin' => true,
                    ]);
                }
                return 'Super admin ready!<br>Email: admin@saascommerce.com<br>Password: admin123<br><br><a href="/login">Go to Login →</a>';
            } catch (\Exception $e) {
                return 'Error: ' . $e->getMessage();
            }
        });

        Route::get('/seed-phone-mappings', function () {
            try {
                $centralConn = config('tenancy.database.central_connection');
                $mappings = 0;

                // Insert mapping for purelife (Phone Number ID: 1172546945946113)
                $exists = \DB::connection($centralConn)
                    ->table('whatsapp_phone_mappings')
                    ->where('phone_number_id', '1172546945946113')
                    ->exists();

                if (!$exists) {
                    \DB::connection($centralConn)
                        ->table('whatsapp_phone_mappings')->insert([
                            'phone_number_id' => '1172546945946113',
                            'tenant_id' => 'purelife',
                            'verify_token' => 'my_platform_verify_2026',
                            'is_active' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    $mappings++;
                }

                return "Phone mappings seeded: {$mappings} new mapping(s) created.<br><br><a href='/admin/whatsapp-provider'>Go to WhatsApp Settings →</a>";
            } catch (\Exception $e) {
                return 'Error: ' . $e->getMessage();
            }
        });

        // سپر ایڈمن ڈیش بورڈ کے راؤٹس
        Route::middleware(['auth', \App\Http\Middleware\SuperAdminMiddleware::class])->prefix('admin')->group(function () {
            Route::get('/', [\App\Http\Controllers\SuperAdminController::class, 'dashboard']);
            Route::get('/tenants', [\App\Http\Controllers\SuperAdminController::class, 'tenants']);
            Route::get('/tenants/create', [\App\Http\Controllers\SuperAdminController::class, 'createTenantForm']);
            Route::post('/tenants/create', [\App\Http\Controllers\SuperAdminController::class, 'createTenant']);
            Route::get('/tenants/{id}', [\App\Http\Controllers\SuperAdminController::class, 'showTenant']);
            Route::post('/tenants/{id}/update-subscription', [\App\Http\Controllers\SuperAdminController::class, 'updateSubscription']);
            Route::post('/tenants/{id}/toggle-status', [\App\Http\Controllers\SuperAdminController::class, 'toggleStatus']);
            Route::post('/tenants/{id}/delete', [\App\Http\Controllers\SuperAdminController::class, 'deleteTenant']);

            // WhatsApp Provider Settings
            Route::get('/whatsapp-provider', function () {
                $provider = \DB::table('whatsapp_providers')->first();
                
                // Get store statuses - query shared DB with tenant_id
                $storeStatuses = [];
                try {
                $tenants = \App\Models\Tenant::all();
                    foreach ($tenants as $tenant) {
                        $settings = \DB::table('store_settings')->where('id', 1)->first();
                        $messagesSent = \DB::table('whatsapp_messages')->where('tenant_id', $tenant->id)->count();
                        $conversations = \DB::table('whatsapp_conversations')->where('tenant_id', $tenant->id)->count();
                        $storeStatuses[] = [
                            'tenant_id' => $tenant->id,
                            'name' => $tenant->name,
                            'phone' => $settings->whatsapp_phone_number_id ?? '',
                            'crm_active' => $settings->whatsapp_crm_active ?? false,
                            'messages_sent' => $messagesSent,
                            'conversations' => $conversations,
                        ];
                    }
                } catch (\Exception $e) {
                    // Tables may not exist yet
                }

                return view('super-admin.whatsapp-provider.index', [
                    'provider' => $provider,
                    'storeStatuses' => $storeStatuses,
                ]);
            });

            Route::post('/whatsapp-provider', function (\Illuminate\Http\Request $request) {
                $provider = \DB::table('whatsapp_providers')->first();
                
                $data = [
                    'provider_name' => $request->provider_name,
                    'api_key' => $request->api_key,
                    'phone_number_id' => $request->phone_number_id,
                    'business_account_id' => $request->business_account_id,
                    'verify_token' => $request->verify_token,
                    'is_active' => $request->has('is_active'),
                    'updated_at' => now(),
                ];

                if ($provider) {
                    \DB::table('whatsapp_providers')->where('id', $provider->id)->update($data);
                } else {
                    $data['created_at'] = now();
                    \DB::table('whatsapp_providers')->insert($data);
                }

                return redirect('/admin/whatsapp-provider')->with('success', 'WhatsApp provider settings saved successfully!');
            });
        });

    });

// Public legal pages (outside domain group for Render proxy compatibility)
Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});

Route::get('/terms-of-service', function () {
    return view('terms-of-service');
});

// Universal WhatsApp webhook — outside domain group so it works on root domain
Route::get('/webhook/whatsapp/universal', [\App\Http\Controllers\WhatsAppWebhookController::class, 'verifyUniversal']);
Route::post('/webhook/whatsapp/universal', [\App\Http\Controllers\WhatsAppWebhookController::class, 'handleUniversal']);

// COMPREHENSIVE DEBUG — shows exactly why 404 is happening
Route::get('/debug/routing/{slug?}', function ($slug = null) {
    $debug = [
        'url' => request()->fullUrl(),
        'host' => request()->getHost(),
        'path' => request()->path(),
        'central_domains' => config('tenancy.central_domains'),
        'db_connection' => config('database.default'),
    ];

    // Check if tenant exists
    try {
        $tenant = \App\Models\Tenant::find('purelife');
        $debug['tenant_found'] = $tenant ? true : false;
        $debug['tenant_id'] = $tenant?->id;
        $debug['tenant_name'] = $tenant?->name;
    } catch (\Exception $e) {
        $debug['tenant_error'] = $e->getMessage();
    }

    // Check pages table and data
    try {
        \Illuminate\Support\Facades\Config::set('database.default', 'pgsql');
        $conn = \Illuminate\Support\Facades\DB::connection('pgsql');
        $debug['central_db'] = $conn->getDatabaseName();

        // Try to find tenant database
        $tenantDb = 'tenant' . 'purelife';
        $debug['tenant_db_name'] = $tenantDb;
        $debug['tenant_db_exists'] = $conn->select("SELECT 1 FROM pg_database WHERE datname = ?", [$tenantDb])->isNotEmpty();
    } catch (\Exception $e) {
        $debug['db_error'] = $e->getMessage();
    }

    // Check tenant-specific page
    if ($slug) {
        try {
            $tenant = \App\Models\Tenant::find('purelife');
            if ($tenant) {
                tenancy()->initialize($tenant);
                $debug['tenancy_initialized'] = true;
                $debug['current_tenant_id'] = tenant('id');
                $debug['current_db'] = DB::connection()->getDatabaseName();
                $debug['pages_table_exists'] = Schema::hasTable('pages');
                if ($debug['pages_table_exists']) {
                    $pages = \App\Models\Page::all();
                    $debug['pages_count'] = $pages->count();
                    $debug['pages'] = $pages->map(fn($p) => ['slug' => $p->slug, 'title' => $p->title, 'is_active' => $p->is_active])->toArray();
                    $debug['page_found'] = \App\Models\Page::where('slug', $slug)->first() ? true : false;
                }
                tenancy()->end();
            }
        } catch (\Exception $e) {
            $debug['page_check_error'] = $e->getMessage();
        }
    }

    // List all registered routes matching 'page'
    try {
        $routes = app('router')->getRoutes();
        $matchingRoutes = [];
        foreach ($routes as $route) {
            $uri = $route->getUri();
            if (str_contains($uri, 'page') || str_contains($uri, 'tenant')) {
                $matchingRoutes[] = [
                    'uri' => $uri,
                    'methods' => $route->getMethods(),
                    'action' => $route->getAction()['uses'] ?? 'Closure',
                ];
            }
        }
        $debug['routes_with_page_or_tenant'] = array_slice($matchingRoutes, 0, 30);
        $debug['total_routes'] = $routes->count();
    } catch (\Exception $e) {
        $debug['route_list_error'] = $e->getMessage();
    }

    return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
});