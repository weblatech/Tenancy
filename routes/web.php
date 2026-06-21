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
                
                // Get store statuses
                $storeStatuses = [];
                foreach (\Stancl\Tenancy\Database\Models\Tenant::all() as $tenant) {
                    \Stancl\Tenancy\Tenancy::initialize($tenant);
                    $settings = \App\Models\StoreSetting::firstOrCreate(['id' => 1]);
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
                    \Stancl\Tenancy\Tenancy::end();
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