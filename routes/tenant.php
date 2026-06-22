<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\StoreSection;
use App\Models\Review;

if (!function_exists('getRealIpAddress')) {
    function getRealIpAddress(Request $request) {
        $headerNames = [
            'CF-Connecting-IP',
            'X-Forwarded-For',
            'X-Real-IP',
            'Client-Ip'
        ];
        foreach ($headerNames as $headerName) {
            $value = $request->header($headerName);
            if (!empty($value)) {
                $ips = explode(',', $value);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        $serverKeys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR'
        ];
        foreach ($serverKeys as $key) {
            $value = $request->server($key);
            if (!empty($value)) {
                $ips = explode(',', $value);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return $request->ip();
    }
}

Route::middleware([
    'web',
    \App\Http\Middleware\InitializeTenantFlexible::class,
    \App\Http\Middleware\CheckTenantSubscription::class,
])->group(function () {

    // 🛒 1. پبلک ہوم پیج (سٹور فرنٹ)
    Route::get('/', function () {
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        $products = Product::latest()->get();
        $sections = StoreSection::where('is_active', true)->orderBy('sort_order')->get();
        $homepageReviews = Review::with('product')->where('show_on_homepage', true)->latest()->take(6)->get();

        return view('tenant.storefront', [
            'tenantId' => tenant('id'), 'settings' => $settings, 'products' => $products,
            'sections' => $sections, 'homepageReviews' => $homepageReviews
        ]);
    });

    // 📦 2. پروڈکٹ کا انفرادی پیج
    Route::get('/product/{id}', function ($id) {
        return view('tenant.product-detail', [
            'tenantId' => tenant('id'), 'settings' => StoreSetting::firstOrCreate(['id' => 1]), 'product' => Product::with('reviews')->findOrFail($id)
        ]);
    });

    Route::post('/product/{id}/review', function (Request $request, $id) {
        $request->validate(['customer_name' => 'required|string|max:255', 'rating' => 'required|integer|min:1|max:5', 'comment' => 'required|string']);
        Review::create(['product_id' => $id, 'customer_name' => $request->customer_name, 'rating' => $request->rating, 'comment' => $request->comment, 'show_on_homepage' => true]);
        return redirect('/product/' . $id)->with('success', 'آپ کا ریویو کامیابی سے شامل ہو گیا ہے! ⭐');
    });

    Route::get('/shop', function () {
        $pendingOrdersCount = App\Models\Order::where('status', 'pending')->count();
        $orders = App\Models\Order::latest()->get();
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        $sections = StoreSection::orderBy('sort_order')->get();
        $pages = App\Models\Page::all();

        // Calculate unified customers count (registered + unique guest phone numbers)
        $registeredCustomers = App\Models\Customer::all();
        $guestPhones = App\Models\Order::whereNull('customer_id')
            ->whereNotNull('customer_phone')
            ->where('customer_phone', '!=', '')
            ->distinct()
            ->pluck('customer_phone')
            ->toArray();
        $registeredPhones = $registeredCustomers->pluck('phone')->filter()->toArray();
        $uniqueGuestPhones = array_diff($guestPhones, $registeredPhones);
        $customerCount = $registeredCustomers->count() + count($uniqueGuestPhones);

        return view('tenant.dashboard', [
            'tenantId' => tenant('id'), 
            'productCount' => Product::count(), 
            'products' => Product::latest()->get(),
            'pendingOrdersCount' => $pendingOrdersCount,
            'orders' => $orders,
            'settings' => $settings,
            'sections' => $sections,
            'pages' => $pages,
            'customerCount' => $customerCount
        ]);
    });

    Route::get('/shop/products', function () {
        return view('tenant.products.index', [
            'tenantId' => tenant('id'),
            'products' => Product::latest()->get()
        ]);
    });

    Route::get('/shop/add-product', function () { 
        return view('tenant.add-product', ['tenantId' => tenant('id')]); 
    });

    Route::post('/shop/add-product', function (Request $request) {
        $imagePath = null;
        $imagesPaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('products', 'public');
                $imagesPaths[] = $path;
                if ($index === 0) {
                    $imagePath = $path;
                }
            }
        }

        $variants = null;
        if ($request->filled('variants_text')) {
            $variants = [];
            $lines = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $request->variants_text))));
            
            // Check if at least one line contains a colon
            $hasColon = false;
            foreach ($lines as $line) {
                if (str_contains($line, ':')) {
                    $hasColon = true;
                    break;
                }
            }

            if ($hasColon) {
                foreach ($lines as $line) {
                    $parts = explode(':', $line, 2);
                    if (count($parts) == 2) {
                        $optName = trim($parts[0]);
                        $optVals = array_filter(array_map('trim', explode(',', $parts[1])));
                        if (!empty($optName) && !empty($optVals)) {
                            $variants[$optName] = array_values($optVals);
                        }
                    }
                }
            } else {
                $variants['Option'] = array_values($lines);
            }
        }

        $variantCombinations = null;
        if ($request->filled('variant_combinations_json')) {
            $variantCombinations = json_decode($request->variant_combinations_json, true);
        }

        $bundleOptions = [];
        if ($request->has('bundle_options') && is_array($request->bundle_options)) {
            foreach ($request->bundle_options as $idx => $opt) {
                $option = [
                    'title' => $opt['title'] ?? '',
                    'price' => (float)($opt['price'] ?? 0),
                    'compare_price' => isset($opt['compare_price']) && $opt['compare_price'] !== '' ? (float)$opt['compare_price'] : null,
                    'badge' => $opt['badge'] ?? '',
                    'label' => $opt['label'] ?? '',
                    'image' => $opt['existing_image'] ?? null,
                ];

                if ($request->hasFile("bundle_options.{$idx}.image")) {
                    $file = $request->file("bundle_options.{$idx}.image");
                    $path = $file->store('products', 'public');
                    $option['image'] = $path;
                }

                $bundleOptions[] = $option;
            }
        }

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'description' => $request->description,
            'stock' => $request->stock ?? 10,
            'image' => $imagePath,
            'images' => $imagesPaths,
            'variants' => $variants,
            'variant_combinations' => $variantCombinations,
            'is_bundle' => $request->has('is_bundle'),
            'bundle_title' => $request->bundle_title,
            'bundle_price' => $request->bundle_price,
            'bundle_details' => $request->bundle_details,
            'bundle_header_title' => $request->bundle_header_title,
            'bundle_header_badge' => $request->bundle_header_badge,
            'bundle_options' => $bundleOptions,
            'bundle_color_primary' => $request->bundle_color_primary,
            'bundle_color_text' => $request->bundle_color_text,
            'is_discount' => $request->has('is_discount'),
            'discount_badge' => $request->discount_badge,
            'discount_terms' => $request->discount_terms
        ]);
        return redirect('/shop/products')->with('success', 'Product added successfully! 🚀');
    });

    Route::post('/shop/products/{id}/edit', function (Request $request, $id) {
        $product = Product::findOrFail($id);
        
        $imagePath = $product->image;
        $imagesPaths = $product->images ?? [];
        
        if ($request->hasFile('images')) {
            $imagesPaths = [];
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('products', 'public');
                $imagesPaths[] = $path;
                if ($index === 0) {
                    $imagePath = $path;
                }
            }
        }

        $variants = null;
        if ($request->filled('variants_text')) {
            $variants = [];
            $lines = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $request->variants_text))));
            
            // Check if at least one line contains a colon
            $hasColon = false;
            foreach ($lines as $line) {
                if (str_contains($line, ':')) {
                    $hasColon = true;
                    break;
                }
            }

            if ($hasColon) {
                foreach ($lines as $line) {
                    $parts = explode(':', $line, 2);
                    if (count($parts) == 2) {
                        $optName = trim($parts[0]);
                        $optVals = array_filter(array_map('trim', explode(',', $parts[1])));
                        if (!empty($optName) && !empty($optVals)) {
                            $variants[$optName] = array_values($optVals);
                        }
                    }
                }
            } else {
                $variants['Option'] = array_values($lines);
            }
        }

        $variantCombinations = null;
        if ($request->filled('variant_combinations_json')) {
            $variantCombinations = json_decode($request->variant_combinations_json, true);
        }
        
        $bundleOptions = [];
        if ($request->has('bundle_options') && is_array($request->bundle_options)) {
            foreach ($request->bundle_options as $idx => $opt) {
                $option = [
                    'title' => $opt['title'] ?? '',
                    'price' => (float)($opt['price'] ?? 0),
                    'compare_price' => isset($opt['compare_price']) && $opt['compare_price'] !== '' ? (float)$opt['compare_price'] : null,
                    'badge' => $opt['badge'] ?? '',
                    'label' => $opt['label'] ?? '',
                    'image' => $opt['existing_image'] ?? null,
                ];

                if ($request->hasFile("bundle_options.{$idx}.image")) {
                    $file = $request->file("bundle_options.{$idx}.image");
                    $path = $file->store('products', 'public');
                    $option['image'] = $path;
                }

                $bundleOptions[] = $option;
            }
        }
        
        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'compare_price' => $request->compare_price,
            'description' => $request->description,
            'stock' => $request->stock ?? 10,
            'image' => $imagePath,
            'images' => $imagesPaths,
            'variants' => $variants,
            'variant_combinations' => $variantCombinations,
            'is_bundle' => $request->has('is_bundle'),
            'bundle_title' => $request->bundle_title,
            'bundle_price' => $request->bundle_price,
            'bundle_details' => $request->bundle_details,
            'bundle_header_title' => $request->bundle_header_title,
            'bundle_header_badge' => $request->bundle_header_badge,
            'bundle_options' => $bundleOptions,
            'bundle_color_primary' => $request->bundle_color_primary,
            'bundle_color_text' => $request->bundle_color_text,
            'is_discount' => $request->has('is_discount'),
            'discount_badge' => $request->discount_badge,
            'discount_terms' => $request->discount_terms
        ]);
        return redirect('/shop/products')->with('success', 'Product updated successfully! 🚀');
    });

    Route::post('/shop/products/{id}/delete', function ($id) {
        Product::findOrFail($id)->delete();
        return redirect('/shop/products')->with('success', 'Product deleted successfully! 🗑️');
    });

    // ⚙️ تھیم کسٹمائزر
    Route::get('/shop/settings', function () {
        return view('tenant.settings', [
            'tenantId' => tenant('id'), 
            'settings' => StoreSetting::firstOrCreate(['id' => 1]), 
            'sections' => StoreSection::orderBy('sort_order')->get(),
            'pages' => App\Models\Page::all()
        ]);
    });

    // 💳 پیمنٹ اور ڈلیوری سیٹنگز پیج
    Route::get('/shop/payments', function () {
        return view('tenant.payment.index', [
            'tenantId' => tenant('id'), 
            'settings' => App\Models\StoreSetting::firstOrCreate(['id' => 1])
        ]);
    });

    Route::post('/shop/payments', function (Request $request) {
        $settings = App\Models\StoreSetting::firstOrCreate(['id' => 1]);
        $settings->update([
            'shipping_mode' => $request->shipping_mode,
            'shipping_flat_fee' => $request->shipping_flat_fee ?? 250,
            'shipping_threshold' => $request->shipping_threshold ?? 2000,
            
            'payment_cod_active' => $request->has('payment_cod_active'),
            'footer_whatsapp' => $request->footer_whatsapp,
            
            'cod_require_advance' => $request->has('cod_require_advance'),
            'cod_advance_type' => $request->cod_advance_type ?? 'flat',
            'cod_advance_value' => $request->cod_advance_value ?? 0.00,
            'cod_advance_instructions' => $request->cod_advance_instructions,
            
            'cod_advance_method' => $request->cod_advance_method ?? 'easypaisa',
            'cod_advance_bank_name' => $request->cod_advance_bank_name,
            'cod_advance_account_title' => $request->cod_advance_account_title,
            'cod_advance_account_number' => $request->cod_advance_account_number,
            'cod_advance_easypaisa_title' => $request->cod_advance_easypaisa_title,
            'cod_advance_easypaisa_number' => $request->cod_advance_easypaisa_number,
            'cod_advance_jazzcash_title' => $request->cod_advance_jazzcash_title,
            'cod_advance_jazzcash_number' => $request->cod_advance_jazzcash_number,
            
            'payment_bank_active' => $request->has('payment_bank_active'),
            'payment_bank_name' => $request->payment_bank_name,
            'payment_bank_title' => $request->payment_bank_title,
            'payment_bank_number' => $request->payment_bank_number,
            
            'payment_easypaisa_active' => $request->has('payment_easypaisa_active'),
            'payment_easypaisa_title' => $request->payment_easypaisa_title,
            'payment_easypaisa_number' => $request->payment_easypaisa_number,
            
            'payment_jazzcash_active' => $request->has('payment_jazzcash_active'),
            'payment_jazzcash_title' => $request->payment_jazzcash_title,
            'payment_jazzcash_number' => $request->payment_jazzcash_number,
        ]);
        return redirect('/shop/payments')->with('success', 'Payment & Delivery Settings updated successfully!');
    });

    // 📱 Social Media & Tracking Integration
    Route::get('/shop/social', function () {
        return view('tenant.social.index', [
            'tenantId' => tenant('id'),
            'settings' => App\Models\StoreSetting::firstOrCreate(['id' => 1])
        ]);
    });

    Route::post('/shop/social', function (Request $request) {
        $settings = App\Models\StoreSetting::firstOrCreate(['id' => 1]);
        $settings->update([
            'meta_pixel_active' => $request->has('meta_pixel_active'),
            'meta_pixel_id' => $request->meta_pixel_id,
            'meta_pixel_events' => $request->meta_pixel_events,
            'google_ads_active' => $request->has('google_ads_active'),
            'google_ads_conversion_id' => $request->google_ads_conversion_id,
            'google_ads_conversion_label' => $request->google_ads_conversion_label,
            'google_analytics_id' => $request->google_analytics_id,
            'google_tag_manager_id' => $request->google_tag_manager_id,
            'tiktok_pixel_active' => $request->has('tiktok_pixel_active'),
            'tiktok_pixel_id' => $request->tiktok_pixel_id,
            'snapchat_pixel_active' => $request->has('snapchat_pixel_active'),
            'snapchat_pixel_id' => $request->snapchat_pixel_id,
            'pinterest_tag_active' => $request->has('pinterest_tag_active'),
            'pinterest_tag_id' => $request->pinterest_tag_id,
            'twitter_pixel_active' => $request->has('twitter_pixel_active'),
            'twitter_pixel_id' => $request->twitter_pixel_id,
            'custom_tracking_active' => $request->has('custom_tracking_active'),
            'custom_tracking_head' => $request->custom_tracking_head,
            'custom_tracking_body' => $request->custom_tracking_body,
            'facebook_page_url' => $request->facebook_page_url,
            'instagram_url' => $request->instagram_url,
            'tiktok_url' => $request->tiktok_url,
            'youtube_url' => $request->youtube_url,
            'twitter_url' => $request->twitter_url,
            'snapchat_url' => $request->snapchat_url,
            'pinterest_url' => $request->pinterest_url,
            'telegram_url' => $request->telegram_url,
            'whatsapp_business_url' => $request->whatsapp_business_url,
        ]);
        return redirect('/shop/social')->with('success', 'Social Media & Tracking settings updated successfully!');
    });

    // WhatsApp Registration Flow
    Route::get('/shop/whatsapp-register', function () {
        $settings = App\Models\StoreSetting::firstOrCreate(['id' => 1]);
        $registration = new \App\Services\WhatsAppRegistration();
        return view('tenant.whatsapp-crm.register', [
            'tenantId' => tenant('id'),
            'storeName' => tenant('name') ?? '',
            'isReady' => $registration->isReady(),
            'phoneNumberId' => $settings->whatsapp_phone_number_id ?? '',
        ]);
    });

    // AJAX: List existing phone numbers from WABA
    Route::get('/shop/whatsapp-register/list-numbers', function () {
        $registration = new \App\Services\WhatsAppRegistration();
        $result = $registration->listPhoneNumbers();
        return response()->json($result);
    });

    // AJAX: Select an existing phone number (no OTP needed)
    Route::post('/shop/whatsapp-register/select-number', function (Request $request) {
        $request->validate([
            'phone_number_id' => 'required|string',
        ]);

        $registration = new \App\Services\WhatsAppRegistration();
        $details = $registration->getPhoneNumberDetails($request->phone_number_id);

        $settings = App\Models\StoreSetting::firstOrCreate(['id' => 1]);
        $settings->update([
            'whatsapp_phone_number_id' => $request->phone_number_id,
            'whatsapp_crm_active' => true,
            'footer_whatsapp' => $details['phone_number'] ?? $settings->footer_whatsapp ?? '',
        ]);

        return response()->json([
            'success' => true,
            'phone_number' => $details['phone_number'] ?? '',
            'verified_name' => $details['verified_name'] ?? '',
            'phone_number_id' => $request->phone_number_id,
        ]);
    });

    Route::post('/shop/whatsapp-register/send-otp', function (Request $request) {
        $request->validate([
            'phone_number' => 'required|string',
            'store_name' => 'required|string|max:255',
        ]);

        $registration = new \App\Services\WhatsAppRegistration();
        if (!$registration->isReady()) {
            return response()->json(['success' => false, 'error' => 'WhatsApp Business Account not configured by admin']);
        }

        $phone = $registration->formatPhone($request->phone_number);
        $result = $registration->registerPhoneNumber($phone, $request->store_name);

        if ($result['success']) {
            // Save phone number ID temporarily
            $settings = App\Models\StoreSetting::firstOrCreate(['id' => 1]);
            $settings->update(['whatsapp_phone_number_id' => $result['phone_number_id']]);

            return response()->json([
                'success' => true,
                'phone_number_id' => $result['phone_number_id'],
            ]);
        }

        return response()->json($result);
    });

    Route::post('/shop/whatsapp-register/verify-otp', function (Request $request) {
        $request->validate([
            'phone_number_id' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        $registration = new \App\Services\WhatsAppRegistration();
        $result = $registration->verifyPhoneNumber($request->phone_number_id, $request->code);

        if ($result['success']) {
            // Phone verified - get details and save
            $details = $registration->getPhoneNumberDetails($request->phone_number_id);

            $settings = App\Models\StoreSetting::firstOrCreate(['id' => 1]);
            $settings->update([
                'whatsapp_phone_number_id' => $request->phone_number_id,
                'whatsapp_crm_active' => true,
                'footer_whatsapp' => $settings->footer_whatsapp ?? $details['phone_number'] ?? '',
            ]);

            return response()->json([
                'success' => true,
                'phone_number' => $details['phone_number'] ?? '',
                'verified_name' => $details['verified_name'] ?? '',
            ]);
        }

        return response()->json($result);
    });

    // WhatsApp CRM Settings
    Route::get('/shop/whatsapp-crm', function () {
        $settings = App\Models\StoreSetting::firstOrCreate(['id' => 1]);
        $whatsapp = new \App\Services\WhatsAppCRM();
        $provider = \App\Services\WhatsAppCRM::getProvider();
        return view('tenant.whatsapp-crm.index', [
            'tenantId' => tenant('id'),
            'settings' => $settings,
            'isConfigured' => $whatsapp->isConfigured(),
            'hasProvider' => (bool) $provider,
        ]);
    });

    Route::post('/shop/whatsapp-crm', function (Request $request) {
        $settings = App\Models\StoreSetting::firstOrCreate(['id' => 1]);
        $settings->update([
            'whatsapp_crm_active' => $request->has('whatsapp_crm_active'),
            'whatsapp_phone_number_id' => $request->whatsapp_phone_number_id,
            'whatsapp_verify_token' => $request->whatsapp_verify_token,
            'footer_whatsapp' => $request->footer_whatsapp,
            'whatsapp_msg_order_pending' => $request->whatsapp_msg_order_pending,
            'whatsapp_msg_order_confirmed' => $request->whatsapp_msg_order_confirmed,
            'whatsapp_msg_order_processing' => $request->whatsapp_msg_order_processing,
            'whatsapp_msg_order_completed' => $request->whatsapp_msg_order_completed,
            'whatsapp_msg_order_cancelled' => $request->whatsapp_msg_order_cancelled,
        ]);
        return redirect('/shop/whatsapp-crm')->with('success', 'WhatsApp CRM settings updated successfully!');
    });

    // WhatsApp CRM Logs
    Route::get('/shop/whatsapp-logs', function () {
        $logs = \DB::table('whatsapp_messages')
            ->where('tenant_id', tenant('id'))
            ->orderByDesc('created_at')
            ->paginate(20);
        
        $stats = [
            'total' => \DB::table('whatsapp_messages')->where('tenant_id', tenant('id'))->count(),
            'sent' => \DB::table('whatsapp_messages')->where('tenant_id', tenant('id'))->where('direction', 'outbound')->where('status', 'sent')->count(),
            'inbound' => \DB::table('whatsapp_messages')->where('tenant_id', tenant('id'))->where('direction', 'inbound')->count(),
            'failed' => \DB::table('whatsapp_messages')->where('tenant_id', tenant('id'))->where('status', 'failed')->count(),
        ];

        return view('tenant.whatsapp-crm.logs', [
            'tenantId' => tenant('id'),
            'logs' => $logs,
            'stats' => $stats,
        ]);
    });

    // WhatsApp Chat - Main Page
    Route::get('/shop/whatsapp-chat', function () {
        $settings = App\Models\StoreSetting::firstOrCreate(['id' => 1]);
        $storePhone = $settings->footer_whatsapp ?? '';
        $isConfigured = (new \App\Services\WhatsAppCRM())->isConfigured();

        return view('tenant.whatsapp-crm.chat', [
            'tenantId' => tenant('id'),
            'storePhone' => $storePhone,
            'isConfigured' => $isConfigured,
        ]);
    });

    // AJAX: Get contacts list (conversations from local DB)
    Route::get('/shop/whatsapp-chat/contacts', function () {
        $tenantId = tenant('id');

        $conversations = \DB::table('whatsapp_conversations')
            ->where('tenant_id', $tenantId)
            ->orderByDesc('last_message_at')
            ->get();

        $contacts = [];
        foreach ($conversations as $conv) {
            $lastMsg = \DB::table('whatsapp_messages')
                ->where('conversation_id', $conv->id)
                ->orderByDesc('created_at')
                ->first();

            $contacts[] = [
                'id' => $conv->id,
                'order_id' => $conv->order_id,
                'customer_name' => $conv->customer_name,
                'customer_phone' => $conv->customer_phone,
                'status' => $conv->status,
                'unread_count' => $conv->unread_count ?? 0,
                'last_message' => $lastMsg ? $lastMsg->message_body : null,
                'last_message_at' => $lastMsg ? $lastMsg->created_at : $conv->last_message_at,
            ];
        }

        return response()->json(['contacts' => $contacts]);
    });

    // AJAX: Get chat messages for a conversation
    Route::get('/shop/whatsapp-chat/{id}/messages', function ($id) {
        $conversation = \DB::table('whatsapp_conversations')
            ->where('tenant_id', tenant('id'))
            ->where('id', $id)
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        $messages = \DB::table('whatsapp_messages')
            ->where('conversation_id', $id)
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'order_id' => $conversation->order_id,
                'customer_name' => $conversation->customer_name,
                'customer_phone' => $conversation->customer_phone,
                'status' => $conversation->status,
            ],
            'messages' => $messages,
        ]);
    });

    Route::post('/shop/whatsapp-chat/{id}/send', function ($id, Request $request) {
        $request->validate(['message' => 'required|string|max:4000']);

        $conversation = \DB::table('whatsapp_conversations')
            ->where('tenant_id', tenant('id'))
            ->where('id', $id)
            ->first();

        if (!$conversation) {
            return response()->json(['success' => false, 'error' => 'Conversation not found'], 404);
        }

        // Handle label change (internal, not sent to customer)
        if (str_starts_with($request->message, '__label__')) {
            $label = substr($request->message, 9);
            if ($conversation->order_id) {
                App\Models\Order::where('id', $conversation->order_id)->update(['status' => $label === 'None' ? 'Unfulfilled' : $label]);
            }
            \DB::table('whatsapp_conversations')->where('id', $id)->update(['status' => $label === 'None' ? 'open' : strtolower(str_replace(' ', '_', $label))]);
            return response()->json(['success' => true, 'status' => 'label_changed', 'label' => $label]);
        }

        $customerPhone = preg_replace('/[^0-9]/', '', $conversation->customer_phone);
        if (strlen($customerPhone) === 11 && str_starts_with($customerPhone, '0')) {
            $customerPhone = '92' . substr($customerPhone, 1);
        } elseif (strlen($customerPhone) === 10) {
            $customerPhone = '92' . $customerPhone;
        }

        $settings = App\Models\StoreSetting::firstOrCreate(['id' => 1]);
        $storePhone = $settings->footer_whatsapp ?? '';

        // Save message locally
        $msgId = \DB::table('whatsapp_messages')->insertGetId([
            'conversation_id' => $id,
            'tenant_id' => tenant('id'),
            'direction' => 'outbound',
            'message_type' => 'manual_chat',
            'message_body' => $request->message,
            'from_phone' => $storePhone,
            'to_phone' => $customerPhone,
            'status' => 'sending',
            'is_auto' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Send via WhatsApp Cloud API
        $sent = false;
        $sendError = null;
        $sendHint = null;
        $whatsapp = new \App\Services\WhatsAppCRM();

        if ($whatsapp->isConfigured()) {
            $result = $whatsapp->sendChatMessage($customerPhone, $request->message, (int) $id);
            $sent = $result['success'] ?? false;
            if (!$sent) {
                $sendError = $result['error'] ?? 'API send failed';
                $sendHint = $result['hint'] ?? null;
            }
        } else {
            $provider = \App\Services\WhatsAppCRM::getProvider();
            if (!$provider) {
                $sendError = 'API Not Configured — Super Admin has not saved WhatsApp Provider settings yet.';
                $sendHint = 'Ask your platform admin to save API Key, Phone Number ID and WABA ID at /admin/whatsapp-provider';
            } else {
                $sendError = 'API Not Configured — Save your Phone Number ID in CRM Settings.';
                $sendHint = 'Go to /shop/whatsapp-crm and enter your Phone Number ID.';
            }
        }

        // Update message status
        $newStatus = $sent ? 'sent' : 'saved';
        \DB::table('whatsapp_messages')->where('id', $msgId)->update(['status' => $newStatus]);
        \DB::table('whatsapp_conversations')->where('id', $id)->update(['last_message_at' => now()]);

        return response()->json([
            'success' => $sent,
            'error' => $sendError,
            'hint' => $sendHint,
            'message_id' => $msgId,
            'status' => $newStatus,
            'message' => $request->message,
            'created_at' => now()->toDateTimeString(),
        ]);
    });

    Route::post('/shop/whatsapp-chat/{id}/close', function ($id) {
        \DB::table('whatsapp_conversations')
            ->where('tenant_id', tenant('id'))
            ->where('id', $id)
            ->update(['status' => 'closed']);

        return redirect('/shop/whatsapp-chat');
    });

    // AJAX: Create new conversation (start chat with new customer)
    Route::post('/shop/whatsapp-chat/new', function (Request $request) {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $request->customer_phone);
        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            $phone = '92' . substr($phone, 1);
        }

        // Check if conversation already exists for this phone
        $existing = \DB::table('whatsapp_conversations')
            ->where('tenant_id', tenant('id'))
            ->where('customer_phone', $phone)
            ->where('status', '!=', 'closed')
            ->first();

        if ($existing) {
            return response()->json(['success' => true, 'conversation_id' => $existing->id, 'existing' => true]);
        }

        $convId = \DB::table('whatsapp_conversations')->insertGetId([
            'tenant_id' => tenant('id'),
            'customer_name' => $request->customer_name,
            'customer_phone' => $phone,
            'status' => 'open',
            'last_message_at' => now(),
            'unread_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'conversation_id' => $convId, 'existing' => false]);
    });

    // Custom Domain Settings
    Route::get('/shop/domains', function () {
        $domains = tenant()->domains;
        $platformIp = config('platform.ip', request()->server('SERVER_ADDR') ?: '127.0.0.1');
        $centralDomains = config('tenancy.central_domains', ['localhost', '127.0.0.1']);
        $defaultSubdomain = tenant('id') . '.' . ($centralDomains[0] ?? 'localhost');
        
        // Determine the current store URL for the storefront link
        $currentHost = request()->getHost();
        $storeUrl = "http://{$currentHost}";
        if (request()->getPort() && request()->getPort() != 80) {
            $storeUrl .= ":" . request()->getPort();
        }
        
        return view('tenant.domains.index', [
            'tenantId' => tenant('id'),
            'domains' => $domains,
            'platformIp' => $platformIp,
            'centralDomains' => $centralDomains,
            'defaultSubdomain' => $defaultSubdomain,
            'storeUrl' => $storeUrl,
        ]);
    });

    Route::post('/shop/domains', function (Request $request) {
        $request->validate([
            'domain' => 'required|string|max:255',
        ]);

        $domain = trim($request->domain);
        $domain = preg_replace('/^https?:\/\//i', '', $domain);
        $domain = rtrim($domain, '/');
        $domain = strtolower($domain);

        if (empty($domain)) {
            return redirect()->back()->with('error', 'Invalid domain format.');
        }

        // Check uniqueness
        $exists = \Stancl\Tenancy\Database\Models\Domain::on(config('tenancy.database.central_connection'))->where('domain', $domain)->exists();
        if ($exists) {
            return redirect()->back()->with('error', 'This domain is already registered.');
        }

        // Check it's not a central/platform domain
        $centralDomains = config('tenancy.central_domains', ['localhost', '127.0.0.1']);
        if (in_array($domain, $centralDomains)) {
            return redirect()->back()->with('error', 'Cannot bind a central platform domain.');
        }

        tenant()->domains()->create(['domain' => $domain]);

        return redirect('/shop/domains')->with('success', 'Custom domain added successfully!');
    });

    Route::post('/shop/domains/{id}/delete', function ($id) {
        $domain = tenant()->domains()->findOrFail($id);

        $centralDomains = config('tenancy.central_domains', ['localhost', '127.0.0.1']);
        $defaultSubdomain = tenant('id') . '.' . ($centralDomains[0] ?? 'localhost');
        $totalDomains = tenant()->domains()->count();

        // Must have at least one domain
        if ($totalDomains <= 1) {
            return redirect()->back()->with('error', 'Cannot delete! A store must have at least one active domain.');
        }

        // Cannot delete the default system subdomain
        if ($domain->domain === $defaultSubdomain) {
            return redirect()->back()->with('error', 'Cannot delete the default system subdomain.');
        }

        $domain->delete();
        return redirect('/shop/domains')->with('success', 'Custom domain deleted successfully!');
    });

    // 💾 مین گلوبل سیٹنگز
    Route::post('/shop/settings', function (Request $request) {
        $settings = App\Models\StoreSetting::first();

        // 🛒 1. شاپ پیج سیٹنگز الگ سے اپڈیٹ کریں (تاکہ گلوبل سیٹنگز اوور رائیٹ نہ ہوں)
        if ($request->input('form_type') === 'shoppage') {
            $settings->update([
                'collection_show_banner' => $request->has('collection_show_banner'),
                'collection_title' => $request->collection_title,
                'collection_subtitle' => $request->collection_subtitle,
                'collection_banner_bg' => $request->collection_banner_bg ?? $settings->collection_banner_bg,
                'collection_banner_text_color' => $request->collection_banner_text_color ?? $settings->collection_banner_text_color,
            ]);
            return redirect('/shop/settings')->with('success', 'Shop Page Settings updated successfully! 🛒');
        }

        // 🎨 2. گلوبل سیٹنگز اپڈیٹ کریں
        $logoPath = $request->hasFile('header_logo') ? $request->file('header_logo')->store('storefront', 'public') : $settings->header_logo;

        // Process multiple hero images for slider and delete option
        $remainingOldImages = json_decode($request->input('remaining_hero_images', '[]'), true);
        if (!is_array($remainingOldImages)) {
            $remainingOldImages = [];
        }

        $uploadedImages = [];
        if ($request->hasFile('hero_images')) {
            foreach ($request->file('hero_images') as $file) {
                $uploadedImages[] = $file->store('storefront', 'public');
            }
        }

        $finalImages = array_merge($remainingOldImages, $uploadedImages);
        $heroImagesJson = empty($finalImages) ? null : $finalImages;

        // Backward compatibility: set hero_image to the first image in the slider
        $imagePath = !empty($finalImages) ? $finalImages[0] : null;

        $parseLinks = function($text) {
            $arr = [];
            if ($text) { foreach (explode("\n", str_replace("\r", "", $text)) as $line) {
                if (trim($line) != '') { $parts = explode('|', $line); if (count($parts) >= 2) $arr[] = ['label' => trim($parts[0]), 'url' => trim($parts[1])]; }
            }} return empty($arr) ? null : $arr;
        };

        $popupArray = [];
        if ($request->sales_popup_data_text) {
            foreach (explode("\n", str_replace("\r", "", $request->sales_popup_data_text)) as $line) {
                if (trim($line) != '') { $parts = explode('|', $line); if (count($parts) >= 3) $popupArray[] = ['name' => trim($parts[0]), 'city' => trim($parts[1]), 'item' => trim($parts[2]), 'time' => trim($parts[3] ?? 'ابھی ابھی')]; }
            }
        }

        $settings->update([
            'announcement_active' => $request->has('announcement_active'),
            'announcement_marquee' => $request->has('announcement_marquee'),
            'announcement_text' => $request->has('announcement_text') ? $request->announcement_text : $settings->announcement_text,
            'announcement_bg_color' => $request->announcement_bg_color ?? $settings->announcement_bg_color,
            'announcement_text_color' => $request->announcement_text_color ?? $settings->announcement_text_color,
            
            // Hero fields using form presence check to allow clearing/saving empty string
            'hero_title' => $request->has('hero_title') ? $request->hero_title : $settings->hero_title,
            'hero_subtitle' => $request->has('hero_subtitle') ? $request->hero_subtitle : $settings->hero_subtitle,
            'hero_btn_text' => $request->has('hero_btn_text') ? $request->hero_btn_text : $settings->hero_btn_text,
            'hero_btn_link' => $request->has('hero_btn_link') ? $request->hero_btn_link : $settings->hero_btn_link,
            'hero_btn2_text' => $request->has('hero_btn2_text') ? $request->hero_btn2_text : $settings->hero_btn2_text,
            'hero_btn2_link' => $request->has('hero_btn2_link') ? $request->hero_btn2_link : $settings->hero_btn2_link,
            
            'hero_bg_color' => $request->hero_bg_color ?? $settings->hero_bg_color,
            'hero_text_color' => $request->hero_text_color ?? $settings->hero_text_color,
            'hero_layout_type' => $request->hero_layout_type ?? $settings->hero_layout_type,
            'hero_image' => $imagePath,
            'hero_images' => $heroImagesJson,
            'hero_custom_code' => $request->has('hero_custom_code') ? $request->hero_custom_code : $settings->hero_custom_code,
            
            'hero_height' => $request->hero_height ?? $settings->hero_height ?? 'medium',
            'hero_align' => $request->hero_align ?? $settings->hero_align ?? 'center',
            'hero_show_container' => $request->has('hero_show_container'),
            'hero_overlay_opacity' => $request->hero_overlay_opacity ?? $settings->hero_overlay_opacity ?? 50,
            
            'header_logo' => $logoPath,
            'header_menu' => $parseLinks($request->header_menu_links_text),
            
            'facebook_pixel_id' => $request->has('facebook_pixel_id') ? $request->facebook_pixel_id : $settings->facebook_pixel_id,
            'enable_rtl' => $request->has('enable_rtl'),
            'disable_inspect' => $request->has('disable_inspect'),
            'enable_sales_popup' => $request->has('enable_sales_popup'),
            'sales_popup_data' => empty($popupArray) ? null : $popupArray,
            
            'footer_bg_color' => $request->footer_bg_color ?? $settings->footer_bg_color ?? '#4CAF50',
            'footer_text_color' => $request->footer_text_color ?? $settings->footer_text_color ?? '#ffffff',
            'footer_about' => $request->has('footer_about') ? $request->footer_about : $settings->footer_about,
            'footer_email' => $request->has('footer_email') ? $request->footer_email : $settings->footer_email,
            'footer_phone' => $request->has('footer_phone') ? $request->footer_phone : $settings->footer_phone,
            'footer_whatsapp' => $request->has('footer_whatsapp') ? $request->footer_whatsapp : $settings->footer_whatsapp,
            'footer_address' => $request->has('footer_address') ? $request->footer_address : $settings->footer_address,
            'footer_quick_links' => $parseLinks($request->footer_quick_links_text),
            'footer_policies_links' => $parseLinks($request->footer_policies_links_text),
            'footer_newsletter_text' => $request->has('footer_newsletter_text') ? $request->footer_newsletter_text : $settings->footer_newsletter_text,
            'footer_copyright' => $request->has('footer_copyright') ? $request->footer_copyright : $settings->footer_copyright,
            'footer_bottom_bg_color' => $request->footer_bottom_bg_color ?? $settings->footer_bottom_bg_color ?? '#1B5E20',
            'footer_bottom_text_color' => $request->footer_bottom_text_color ?? $settings->footer_bottom_text_color ?? '#ffffff',
            
            'btn_add_to_cart_text' => $request->btn_add_to_cart_text,
            'btn_add_to_cart_bg' => $request->btn_add_to_cart_bg,
            'btn_add_to_cart_text_color' => $request->btn_add_to_cart_text_color,
            'btn_buy_now_text' => $request->btn_buy_now_text,
            'btn_buy_now_bg' => $request->btn_buy_now_bg,
            'btn_buy_now_text_color' => $request->btn_buy_now_text_color,
            
            'announcement_font_size' => $request->announcement_font_size,
            'header_logo_height' => $request->header_logo_height,
            'header_menu_bg' => $request->header_menu_bg,
            'header_menu_text' => $request->header_menu_text,
            'header_menu_active_bg' => $request->header_menu_active_bg,
            'header_menu_active_text' => $request->header_menu_active_text,
            'btn_primary_bg' => $request->btn_primary_bg,
            'btn_primary_text' => $request->btn_primary_text,
            'btn_secondary_bg' => $request->btn_secondary_bg,
            'btn_secondary_text' => $request->btn_secondary_text,
        ]);
        return redirect('/shop/settings')->with('success', 'Store Global Settings updated successfully! 🎨');
    });

    // 🧩 نیا سیکشن ایڈ / ایڈٹ کرنے کا روٹ (مع Spacing کنٹرول)
    Route::post('/shop/settings/section', function (Request $request) {
        $type = $request->section_type ?? 'custom_code';
        $settings = [];
        
        // ⭐ Spacing Variables
        $pt = $request->pt ?? 'pt-16';
        $pb = $request->pb ?? 'pb-16';

        if ($type === 'discount_banner') {
            $settings = ['badge' => $request->banner_badge, 'heading' => $request->banner_heading, 'highlight' => $request->banner_highlight, 'description' => $request->banner_description, 'btn_text' => $request->banner_btn_text];
        } 
        elseif ($type === 'image_with_text') {
            $imagePath = $request->section_id ? StoreSection::find($request->section_id)->settings['image'] ?? null : null;
            if ($request->hasFile('iwt_image')) { $imagePath = $request->file('iwt_image')->store('sections', 'public'); }
            $settings = ['image' => $imagePath, 'layout' => $request->iwt_layout, 'heading' => $request->iwt_heading, 'text' => $request->iwt_text, 'btn_text' => $request->iwt_btn_text, 'btn_link' => $request->iwt_btn_link];
        }
        elseif ($type === 'features_bar') {
            $f1_icon = $request->section_id ? (StoreSection::find($request->section_id)->settings['f1_icon'] ?? null) : null;
            $f2_icon = $request->section_id ? (StoreSection::find($request->section_id)->settings['f2_icon'] ?? null) : null;
            $f3_icon = $request->section_id ? (StoreSection::find($request->section_id)->settings['f3_icon'] ?? null) : null;
            $f4_icon = $request->section_id ? (StoreSection::find($request->section_id)->settings['f4_icon'] ?? null) : null;

            if ($request->hasFile('f1_icon')) $f1_icon = $request->file('f1_icon')->store('sections', 'public');
            if ($request->hasFile('f2_icon')) $f2_icon = $request->file('f2_icon')->store('sections', 'public');
            if ($request->hasFile('f3_icon')) $f3_icon = $request->file('f3_icon')->store('sections', 'public');
            if ($request->hasFile('f4_icon')) $f4_icon = $request->file('f4_icon')->store('sections', 'public');

            $settings = ['f1' => $request->f1_title, 'f1_icon' => $f1_icon, 'f2' => $request->f2_title, 'f2_icon' => $f2_icon, 'f3' => $request->f3_title, 'f3_icon' => $f3_icon, 'f4' => $request->f4_title, 'f4_icon' => $f4_icon];
        }
        elseif ($type === 'testimonials') {
            $settings = ['heading' => $request->testi_heading, 'r1_name' => $request->r1_name, 'r1_text' => $request->r1_text, 'r2_name' => $request->r2_name, 'r2_text' => $request->r2_text, 'r3_name' => $request->r3_name, 'r3_text' => $request->r3_text];
        }
        elseif ($type === 'faq') {
            $faqs = [];
            if ($request->has('faq_q') && is_array($request->faq_q)) {
                foreach ($request->faq_q as $index => $q) {
                    if (!empty($q)) { $faqs[] = ['q' => $q, 'a' => $request->faq_a[$index] ?? '']; }
                }
            }
            $settings = ['heading' => $request->faq_heading, 'faqs' => $faqs];
        }
        elseif ($type === 'featured_products') {
            $settings = ['heading' => $request->fp_heading, 'product_count' => $request->fp_count ?? 4];
        }
        elseif ($type === 'video_banner') {
            $settings = ['heading' => $request->video_heading, 'subheading' => $request->video_subheading, 'video_url' => $request->video_url, 'btn_text' => $request->video_btn_text, 'btn_link' => $request->video_btn_link];
        }
        elseif ($type === 'newsletter_form') {
            $settings = ['heading' => $request->news_heading, 'subheading' => $request->news_subheading, 'placeholder' => $request->news_placeholder ?? 'Enter your email', 'btn_text' => $request->news_btn_text ?? 'Subscribe'];
        }
        elseif ($type === 'rich_text') {
            $settings = ['heading' => $request->rt_heading, 'text' => $request->rt_text, 'btn_text' => $request->rt_btn_text, 'btn_link' => $request->rt_btn_link];
        }

        // ⭐ Spacing کو سیٹنگز میں شامل کریں
        $settings['pt'] = $pt;
        $settings['pb'] = $pb;

        if ($request->filled('section_id')) {
            $section = StoreSection::findOrFail($request->section_id);
            $section->update(['title' => $request->section_title, 'content' => $request->section_content ?? '', 'settings' => $settings]);
            return redirect('/shop/settings')->with('success', 'Section Updated Successfully! ✏️');
        } else {
            StoreSection::create(['title' => $request->section_title, 'type' => $type, 'content' => $request->section_content ?? '', 'settings' => $settings, 'sort_order' => StoreSection::count() + 1, 'is_active' => true]);
            return redirect('/shop/settings')->with('success', 'Section Added Successfully! 🚀');
        }
    });

    Route::get('/shop/settings/section/delete/{id}', function ($id) { 
        StoreSection::findOrFail($id)->delete(); return redirect('/shop/settings')->with('success', 'Section deleted successfully! 🗑️'); 
    });

    // 📄 پیج بنانا اور ایڈٹ کرنا
    Route::post('/shop/settings/page', function (Request $request) {
        $slug = $request->slug ? \Illuminate\Support\Str::slug($request->slug) : \Illuminate\Support\Str::slug($request->title);
        
        $existing = App\Models\Page::where('slug', $slug);
        if ($request->page_id) {
            $existing->where('id', '!=', $request->page_id);
        }
        if ($existing->exists()) {
            $slug = $slug . '-' . time();
        }

        if ($request->page_id) {
            $page = App\Models\Page::findOrFail($request->page_id);
            $page->update([
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'is_active' => $request->has('is_active'),
                'is_policy' => $request->has('is_policy'),
            ]);
            $msg = 'Page Updated Successfully! 📄';
        } else {
            App\Models\Page::create([
                'title' => $request->title,
                'slug' => $slug,
                'content' => $request->content,
                'is_active' => $request->has('is_active'),
                'is_policy' => $request->has('is_policy'),
            ]);
            $msg = 'Page Created Successfully! 📄';
        }

        // Auto add to Header Menu if requested
        if ($request->has('add_to_header')) {
            $settings = StoreSetting::firstOrCreate(['id' => 1]);
            $headerMenu = $settings->header_menu;
            if (is_null($headerMenu)) {
                $headerMenu = [
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'Shop', 'url' => '/collection']
                ];
            }
            if (!is_array($headerMenu)) {
                $headerMenu = [];
            }
            $exists = false;
            $pageUrl = '/page/' . $slug;
            foreach ($headerMenu as $item) {
                if (($item['url'] ?? '') === $pageUrl) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $headerMenu[] = [
                    'label' => $request->title,
                    'url' => $pageUrl,
                ];
                $settings->update(['header_menu' => $headerMenu]);
            }
        }

        return redirect('/shop/settings')->with('success', $msg);
    });

    // 🗑️ پیج ڈیلیٹ کرنا
    Route::get('/shop/settings/page/delete/{id}', function ($id) {
        App\Models\Page::findOrFail($id)->delete();
        return redirect('/shop/settings')->with('success', 'Page deleted successfully! 🗑️');
    });

    // 📋 1. ایڈمن میسجز انڈیکس پیج
    Route::get('/shop/messages', function () {
        $messagesFile = storage_path('app/contact_messages_' . tenant('id') . '.json');
        $contactMessages = [];
        if (file_exists($messagesFile)) {
            $contactMessages = json_decode(file_get_contents($messagesFile), true) ?? [];
        }
        foreach ($contactMessages as $idx => &$msg) {
            $msg['original_index'] = $idx;
        }
        $contactMessages = array_reverse($contactMessages);

        return view('tenant.messages.index', [
            'tenantId' => tenant('id'),
            'contactMessages' => $contactMessages
        ]);
    });

    // 🗑️ میسج ڈیلیٹ کرنا
    Route::get('/shop/messages/delete/{index}', function ($index) {
        $messagesFile = storage_path('app/contact_messages_' . tenant('id') . '.json');
        if (file_exists($messagesFile)) {
            $messages = json_decode(file_get_contents($messagesFile), true) ?? [];
            if (isset($messages[$index])) {
                unset($messages[$index]);
                $messages = array_values($messages);
                file_put_contents($messagesFile, json_encode($messages, JSON_PRETTY_PRINT));
            }
        }
        return redirect('/shop/messages')->with('success', 'Contact message deleted successfully! 🗑️');
    });

    // 📧 میسج پر رپلائی بھیجنا
    Route::post('/shop/messages/reply', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $email = $request->email;
        $subject = $request->subject;
        $messageBody = $request->message;

        try {
            $mailable = new class($messageBody) extends \Illuminate\Mail\Mailable {
                public function __construct(public $body) {}
                public function build() {
                    return $this->html($this->body);
                }
            };
            $mailable->subject($subject);
            Mail::to($email)->send($mailable);
            return redirect('/shop/messages')->with('success', 'Reply email sent successfully! 📧');
        } catch (\Exception $e) {
            return redirect('/shop/messages')->with('error', 'Could not send automated mail. Error: ' . $e->getMessage() . '. You can click "Open Mail Client" instead.');
        }
    });

    // 📋 2. ایڈمن سبسکرائبرز انڈیکس پیج
    Route::get('/shop/subscribers', function () {
        $subscribersFile = storage_path('app/subscribers_' . tenant('id') . '.json');
        $subscribers = [];
        if (file_exists($subscribersFile)) {
            $subscribers = json_decode(file_get_contents($subscribersFile), true) ?? [];
        }
        foreach ($subscribers as $idx => &$sub) {
            $sub['original_index'] = $idx;
        }
        $subscribers = array_reverse($subscribers);

        return view('tenant.subscribers.index', [
            'tenantId' => tenant('id'),
            'subscribers' => $subscribers
        ]);
    });

    // 🗑️ سبسکرائبر ڈیلیٹ کرنا
    Route::get('/shop/subscribers/delete/{index}', function ($index) {
        $subscribersFile = storage_path('app/subscribers_' . tenant('id') . '.json');
        if (file_exists($subscribersFile)) {
            $subscribers = json_decode(file_get_contents($subscribersFile), true) ?? [];
            if (isset($subscribers[$index])) {
                unset($subscribers[$index]);
                $subscribers = array_values($subscribers);
                file_put_contents($subscribersFile, json_encode($subscribers, JSON_PRETTY_PRINT));
            }
        }
        return redirect('/shop/subscribers')->with('success', 'Subscriber deleted successfully! 🗑️');
    });

    // 📢 تمام سبسکرائبرز کو پرومشن براڈکاسٹ بھیجنا
    Route::post('/shop/subscribers/broadcast', function (Request $request) {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $subject = $request->subject;
        $messageBody = $request->message;

        $subscribersFile = storage_path('app/subscribers_' . tenant('id') . '.json');
        $subscribers = [];
        if (file_exists($subscribersFile)) {
            $subscribers = json_decode(file_get_contents($subscribersFile), true) ?? [];
        }

        if (count($subscribers) === 0) {
            return redirect('/shop/subscribers')->with('error', 'No subscribers available to receive emails.');
        }

        $sentCount = 0;
        $errors = [];

        foreach ($subscribers as $sub) {
            $email = $sub['email'];
            try {
                $mailable = new class($messageBody) extends \Illuminate\Mail\Mailable {
                    public function __construct(public $body) {}
                    public function build() {
                        return $this->html($this->body);
                    }
                };
                $mailable->subject($subject);
                Mail::to($email)->send($mailable);
                $sentCount++;
            } catch (\Exception $e) {
                $errors[] = $email;
            }
        }

        if (count($errors) > 0) {
            return redirect('/shop/subscribers')->with('success', "Campaign completed. Sent to {$sentCount} subscribers. Failed to send to: " . implode(', ', $errors));
        }

        return redirect('/shop/subscribers')->with('success', "Campaign broadcast sent successfully to all {$sentCount} subscribers! 🚀");
    });

    // 📧 نیوز لیٹر سبسکرائب کرنا
    Route::post('/newsletter-subscribe', function (Request $request) {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        $subscribersFile = storage_path('app/subscribers_' . tenant('id') . '.json');
        $subscribers = [];
        if (file_exists($subscribersFile)) {
            $subscribers = json_decode(file_get_contents($subscribersFile), true) ?? [];
        }

        // Check if email already exists
        $emails = array_column($subscribers, 'email');
        if (in_array($validated['email'], $emails)) {
            return response()->json([
                'success' => false,
                'message' => 'You are already subscribed to our newsletter! ✉️'
            ]);
        }

        $validated['created_at'] = now()->toDateTimeString();
        $validated['ip'] = getRealIpAddress($request);
        $subscribers[] = $validated;

        if (!is_dir(dirname($subscribersFile))) {
            mkdir(dirname($subscribersFile), 0755, true);
        }

        file_put_contents($subscribersFile, json_encode($subscribers, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'message' => 'Thank you for subscribing to our newsletter! 🎉'
        ]);
    });

    // ✉️ رابطہ فارم موصول کرنا
    Route::post('/contact-submit', function (Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $messagesFile = storage_path('app/contact_messages_' . tenant('id') . '.json');
        $messages = [];
        if (file_exists($messagesFile)) {
            $messages = json_decode(file_get_contents($messagesFile), true) ?? [];
        }

        $validated['created_at'] = now()->toDateTimeString();
        $validated['ip'] = getRealIpAddress($request);
        $messages[] = $validated;

        if (!is_dir(dirname($messagesFile))) {
            mkdir(dirname($messagesFile), 0755, true);
        }

        file_put_contents($messagesFile, json_encode($messages, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'message' => 'Your message has been sent successfully! We will get back to you soon.'
        ]);
    });

    // 🗺️ نیویگیشن مینو محفوظ کرنا
    Route::post('/shop/settings/navigation', function (Request $request) {
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        
        $parseJsonMenu = function($itemsJson) {
            $decoded = json_decode($itemsJson, true);
            return is_array($decoded) ? $decoded : null;
        };

        $settings->update([
            'header_menu' => $parseJsonMenu($request->header_menu_json),
            'footer_quick_links' => $parseJsonMenu($request->footer_quick_links_json),
            'footer_policies_links' => $parseJsonMenu($request->footer_policies_links_json),
        ]);

        return redirect('/shop/settings')->with('success', 'Navigation Settings updated successfully! 🗺️');
    });

    // 📄 اسٹور فرنٹ پیج رینڈر کرنا
    Route::get('/page/{slug}', function ($slug) {
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        $page = App\Models\Page::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('tenant.page', [
            'tenantId' => tenant('id'),
            'settings' => $settings,
            'page' => $page
        ]);
    });

    // 🛍️ 1. اسٹور کلیکشن پیج (Collection Catalog)
    Route::get('/collection', function (Request $request) {
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        $query = Product::query();

        // 🔍 فلٹر بائے پرائس
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (int)$request->price_max);
        }

        // 🔍 فلٹر بائے اویلیبلٹی
        if ($request->filled('availability')) {
            if ($request->availability === 'in_stock') {
                $query->where('stock', '>', 0);
            }
        }

        // 🔀 سورٹنگ (Sorting)
        $sortBy = $request->sort_by ?? 'latest';
        if ($sortBy === 'alpha_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sortBy === 'alpha_desc') {
            $query->orderBy('name', 'desc');
        } elseif ($sortBy === 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($sortBy === 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->get();

        return view('tenant.collection', [
            'tenantId' => tenant('id'),
            'settings' => $settings,
            'products' => $products
        ]);
    });

    // 💳 2. چیک آؤٹ پیج (Checkout Page)
    Route::get('/checkout', function () {
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        return view('tenant.checkout', [
            'tenantId' => tenant('id'),
            'settings' => $settings
        ]);
    });

    Route::post('/checkout', function (Request $request) {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:255',
            'customer_address' => 'required|string',
            'customer_city' => 'required|string|max:255',
            'cart_items_json' => 'required|json',
            'payment_method' => 'nullable|string|in:cod,bank,easypaisa,jazzcash',
        ]);

        $cartItems = json_decode($request->cart_items_json, true);
        if (!is_array($cartItems) || empty($cartItems)) {
            return redirect()->back()->with('error', 'آپ کا کارٹ خالی ہے! 🛒');
        }

        // Calculate Subtotal & Total
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += (int)($item['price'] ?? 0) * (int)($item['qty'] ?? 1);
        }

        // Calculate shipping dynamically from settings
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        $shippingMode = $settings->shipping_mode ?? 'conditional';
        $shippingFlatFee = (int)($settings->shipping_flat_fee ?? 250);
        $shippingThreshold = (int)($settings->shipping_threshold ?? 2000);

        $shippingFee = 0;
        if ($shippingMode === 'flat') {
            $shippingFee = $shippingFlatFee;
        } elseif ($shippingMode === 'conditional') {
            if ($subtotal >= $shippingThreshold) {
                $shippingFee = 0;
            } else {
                $shippingFee = $shippingFlatFee;
            }
        } else {
            $shippingFee = 0; // free delivery
        }

        $total = $subtotal + $shippingFee;

        // Resolve client IP and geolocate (Frontend values take priority, server-side lookup is fallback)
        $ip = $request->client_ip ?? getRealIpAddress($request);
        if ($ip === '127.0.0.1' || $ip === '::1') {
            $ip = '39.40.120.50'; // Mock real Lahore IP for local testing/assertions
        }

        $geoCountry = $request->ip_country ?? 'Pakistan';
        $geoCity = $request->ip_city ?? $request->customer_city ?? 'Lahore';
        $geoLat = $request->latitude ?? '31.5497';
        $geoLon = $request->longitude ?? '74.3436';
        $geoIsp = $request->ip_isp ?? 'PTCL';

        if (empty($request->ip_city) || empty($request->latitude)) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
                if ($response->successful() && $response->json('status') === 'success') {
                    $geoCountry = $response->json('country') ?? $geoCountry;
                    $geoCity = $response->json('city') ?? $geoCity;
                    $geoIsp = $response->json('isp') ?? $geoIsp;
                    
                    // Only overwrite coordinates if they weren't pinpointed via client-side GPS
                    if (empty($request->latitude)) {
                        $geoLat = (string)$response->json('lat') ?? $geoLat;
                        $geoLon = (string)$response->json('lon') ?? $geoLon;
                    }
                }
            } catch (\Exception $e) {
                // Fallback
            }
        }

        // Calculate COD Advance required if applicable
        $codAdvanceRequired = 0;
        if (($request->payment_method ?? 'cod') === 'cod' && $settings->cod_require_advance) {
            if ($settings->cod_advance_type === 'flat') {
                $codAdvanceRequired = min((float)$settings->cod_advance_value, (float)$total);
            } elseif ($settings->cod_advance_type === 'percentage') {
                $codAdvanceRequired = round(((float)$total * (float)$settings->cod_advance_value) / 100);
            }
        }

        $order = App\Models\Order::create([
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'customer_city' => $request->customer_city,
            'cart_items' => $cartItems,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'total' => $total,
            'status' => 'pending',
            'ip_address' => $ip,
            'ip_country' => $geoCountry,
            'ip_city' => $geoCity,
            'latitude' => $geoLat,
            'longitude' => $geoLon,
            'isp' => $geoIsp,
            'customer_id' => auth('customer')->id(),
            'payment_method' => $request->payment_method ?? 'cod',
            'cod_advance_required' => $codAdvanceRequired,
            'cod_advance_paid' => false,
        ]);

        // Sync order to Supabase for WhatsApp CRM chat
        try {
            $supabaseUrl = 'https://zwdumolledeoxlvqckka.supabase.co';
            $supabaseKey = 'sb_publishable_uuH260DGvElg-m8JIZwxAA_Yq3YJ3hy';
            $supabaseData = [
                'id'          => $order->id,
                'customer'    => $request->customer_name,
                'mobile'      => $request->customer_phone,
                'status'      => 'pending',
                'price'       => $total,
                'created_at'  => now()->toIso8601String(),
            ];
            \Log::info('Supabase sync: inserting order', $supabaseData);
            \Http::withHeaders([
                'apikey'        => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
                'Prefer'        => 'return=minimal',
            ])->post($supabaseUrl . '/rest/v1/orders', $supabaseData);
            \Log::info('Order synced to Supabase', ['order_id' => $order->id]);
        } catch (\Exception $e) {
            \Log::error('Supabase sync failed for order ' . $order->id . ': ' . $e->getMessage());
        }

        // Send WhatsApp order pending notification with Confirm/Cancel buttons
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        if ($settings->whatsapp_crm_active) {
            $whatsapp = new \App\Services\WhatsAppCRM();
            if ($whatsapp->isConfigured()) {
                try {
                    $whatsapp->sendOrderPending($order);
                } catch (\Exception $e) {
                    \Log::error('WhatsApp order pending notification failed: ' . $e->getMessage());
                }
            }
        }

        return redirect('/order-success/' . $order->id);
    });

    // 🎉 3. آرڈر کی کامیابی کا پیج (Order Success)
    Route::get('/order-success/{id}', function ($id) {
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        $order = App\Models\Order::findOrFail($id);
        return view('tenant.order-success', [
            'tenantId' => tenant('id'),
            'settings' => $settings,
            'order' => $order
        ]);
    });

    // 📋 4. ایڈمن آرڈرز لسٹ (Merchant Dashboard Orders)
    Route::get('/shop/orders', function () {
        $orders = App\Models\Order::latest()->get();
        return view('tenant.orders.index', [
            'tenantId' => tenant('id'),
            'orders' => $orders
        ]);
    });

    // 📦 5. ایڈمن آرڈر ڈیٹیل (Merchant Dashboard Order View)
    Route::get('/shop/orders/{id}', function ($id) {
        $order = App\Models\Order::findOrFail($id);
        return view('tenant.orders.show', [
            'tenantId' => tenant('id'),
            'order' => $order
        ]);
    });

    // ✏️ 6. ایڈمن آرڈر سٹیٹس اپ ڈیٹ (Update Order Status)
    Route::post('/shop/orders/{id}/status', function (Request $request, $id) {
        $order = App\Models\Order::findOrFail($id);

        // Block processing/completed status if COD advance is required but unpaid
        if (in_array($request->status, ['processing', 'completed']) && 
            $order->payment_method === 'cod' && 
            $order->cod_advance_required > 0 && 
            !$order->cod_advance_paid) {
            return redirect()->back()->with('error', '⚠️ Cannot process order! COD Advance Payment (کیش آن ڈلیوری ایڈوانس پیمنٹ) has not been paid/verified yet.');
        }

        $order->update(['status' => $request->status]);

        // Sync status update to Supabase
        try {
            $supabaseUrl = 'https://zwdumolledeoxlvqckka.supabase.co';
            $supabaseKey = 'sb_publishable_uuH260DGvElg-m8JIZwxAA_Yq3YJ3hy';
            \Log::info('Supabase sync: updating status', ['order_id' => $order->id, 'status' => $request->status]);
            \Http::withHeaders([
                'apikey'        => $supabaseKey,
                'Authorization' => 'Bearer ' . $supabaseKey,
                'Prefer'        => 'return=minimal',
            ])->patch($supabaseUrl . '/rest/v1/orders?id=eq.' . $order->id, [
                'status' => $request->status,
            ]);
        } catch (\Exception $e) {
            \Log::error('Supabase status sync failed for order ' . $order->id . ': ' . $e->getMessage());
        }

        // Send WhatsApp notification based on status
        $settings = App\Models\StoreSetting::firstOrCreate(['id' => 1]);
        if ($settings->whatsapp_crm_active) {
            $whatsapp = new \App\Services\WhatsAppCRM();
            if ($whatsapp->isConfigured()) {
                try {
                    switch ($request->status) {
                        case 'confirmed':
                            $whatsapp->sendOrderConfirmed($order);
                            break;
                        case 'processing':
                            $whatsapp->sendOrderProcessing($order);
                            break;
                        case 'completed':
                            $whatsapp->sendOrderCompleted($order);
                            break;
                        case 'cancelled':
                            $whatsapp->sendOrderCancelled($order);
                            break;
                    }
                } catch (\Exception $e) {
                    \Log::error('WhatsApp notification failed: ' . $e->getMessage());
                }
            }
        }

        return redirect('/shop/orders/' . $id)->with('success', 'Order status updated successfully!');
    });

    // 💳 verify COD advance payment
    Route::post('/shop/orders/{id}/toggle-advance', function ($id) {
        $order = App\Models\Order::findOrFail($id);
        $order->update(['cod_advance_paid' => !$order->cod_advance_paid]);
        $msg = $order->cod_advance_paid ? 'COD Advance Payment verified! 💳' : 'COD Advance Payment marked as unpaid! ⚠️';
        return redirect()->back()->with('success', $msg);
    });

    // 🗑️ 7. ایڈمن آرڈر مستقل ڈیلیٹ کریں (Delete Order permanently)
    Route::post('/shop/orders/{id}/delete', function ($id) {
        App\Models\Order::findOrFail($id)->delete();
        return redirect('/shop/orders')->with('success', 'Order has been permanently deleted! 🗑️');
    });

    // 🗑️ 8. ایڈمن آرڈرز بلک ڈیلیٹ کریں (Bulk Delete Orders)
    Route::post('/shop/orders/bulk-delete', function (Request $request) {
        $ids = json_decode($request->order_ids, true);
        if (is_array($ids) && !empty($ids)) {
            App\Models\Order::whereIn('id', $ids)->delete();
            return redirect('/shop/orders')->with('success', count($ids) . ' orders permanently deleted! 🗑️');
        }
        return redirect('/shop/orders')->with('error', 'No orders selected for deletion.');
    });

    // 👥 9. ایڈمن کسٹمرز لسٹ (Merchant Dashboard Customers)
    Route::get('/shop/customers', function () {
        $type = request('type', 'all');
        $delivery = request('delivery', 'all');
        $search = request('search', '');

        $registeredCustomers = App\Models\Customer::all();
        $guestOrders = App\Models\Order::whereNull('customer_id')->get();
        $guestGroups = $guestOrders->groupBy(function($order) {
            return $order->customer_phone ?: $order->customer_name;
        });

        $unifiedCustomers = collect();

        // Helper functions
        if (!function_exists('getInitials')) {
            function getInitials($name) {
                $words = explode(' ', trim($name));
                $initials = '';
                foreach ($words as $word) {
                    $initials .= strtoupper(substr($word, 0, 1));
                    if (strlen($initials) >= 2) break;
                }
                return $initials ?: 'C';
            }
        }

        if (!function_exists('getAvatarColorClass')) {
            function getAvatarColorClass($name) {
                $colors = [
                    'bg-rose-500', 'bg-indigo-500', 'bg-emerald-500', 'bg-amber-500', 
                    'bg-violet-500', 'bg-sky-500', 'bg-fuchsia-500', 'bg-teal-500'
                ];
                $hash = crc32($name);
                return $colors[abs($hash) % count($colors)];
            }
        }

        // 1. Add registered customers
        foreach ($registeredCustomers as $customer) {
            $cOrders = App\Models\Order::where('customer_id', $customer->id)->get();
            $totalSpent = $cOrders->sum('total');
            $lastOrder = $cOrders->max('created_at');
            
            $unifiedCustomers->push((object)[
                'id' => $customer->id,
                'is_guest' => false,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'city' => $customer->city,
                'address' => $customer->address,
                'orders_count' => $cOrders->count(),
                'total_spent' => $totalSpent,
                'last_order' => $lastOrder,
                'orders' => $cOrders,
                'created_at' => $customer->created_at,
                'initials' => getInitials($customer->name),
                'avatar_color' => getAvatarColorClass($customer->name),
            ]);
        }

        // 2. Add guest customers
        $registeredPhones = $registeredCustomers->pluck('phone')->filter()->toArray();
        foreach ($guestGroups as $phoneOrName => $orders) {
            $firstOrder = $orders->first();
            if ($firstOrder->customer_phone && in_array($firstOrder->customer_phone, $registeredPhones)) {
                continue; // skip, already registered
            }
            
            $totalSpent = $orders->sum('total');
            $lastOrder = $orders->max('created_at');
            
            $unifiedCustomers->push((object)[
                'id' => null,
                'is_guest' => true,
                'guest_key' => base64_encode($firstOrder->customer_phone ?: $firstOrder->customer_name),
                'name' => $firstOrder->customer_name,
                'email' => 'Guest Customer',
                'phone' => $firstOrder->customer_phone,
                'city' => $firstOrder->customer_city,
                'address' => $firstOrder->customer_address,
                'orders_count' => $orders->count(),
                'total_spent' => $totalSpent,
                'last_order' => $lastOrder,
                'orders' => $orders,
                'created_at' => $orders->min('created_at'),
                'initials' => getInitials($firstOrder->customer_name),
                'avatar_color' => getAvatarColorClass($firstOrder->customer_name),
            ]);
        }

        // Compute metrics BEFORE applying search/filters so stats stay static
        $totalCustomers = $unifiedCustomers->count();
        
        $newThisMonth = App\Models\Customer::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $newGuestsThisMonth = 0;
        foreach ($guestGroups as $phoneOrName => $orders) {
            $firstOrder = $orders->first();
            if ($firstOrder->customer_phone && in_array($firstOrder->customer_phone, $registeredPhones)) {
                continue;
            }
            $firstOrderDate = $orders->min('created_at');
            if ($firstOrderDate && $firstOrderDate->format('Y-m') === now()->format('Y-m')) {
                $newGuestsThisMonth++;
            }
        }
        $newThisMonth += $newGuestsThisMonth;

        $repeatCustomers = $unifiedCustomers->filter(fn($c) => $c->orders_count > 1)->count();

        $citiesServed = max(
            App\Models\Customer::whereNotNull('city')->where('city', '!=', '')->distinct()->count('city'),
            App\Models\Order::whereNotNull('customer_city')->where('customer_city', '!=', '')->distinct()->count('customer_city')
        );

        // Apply Timeframe / Group Filters
        if ($type === 'new') {
            $unifiedCustomers = $unifiedCustomers->filter(fn($c) => $c->orders_count === 1);
        } elseif ($type === 'this_month') {
            $unifiedCustomers = $unifiedCustomers->filter(function($c) {
                $joinedThisMonth = $c->created_at && \Carbon\Carbon::parse($c->created_at)->format('Y-m') === now()->format('Y-m');
                $firstOrderThisMonth = $c->last_order && \Carbon\Carbon::parse($c->last_order)->format('Y-m') === now()->format('Y-m');
                return $joinedThisMonth || $firstOrderThisMonth;
            });
        } elseif ($type === 'repeat') {
            $unifiedCustomers = $unifiedCustomers->filter(fn($c) => $c->orders_count > 1);
        }

        // Apply Delivery Status Filters
        if ($delivery === 'delivered') {
            $unifiedCustomers = $unifiedCustomers->filter(function($c) {
                return $c->orders->contains(fn($o) => $o->status === 'completed');
            });
        } elseif ($delivery === 'returned') {
            $unifiedCustomers = $unifiedCustomers->filter(function($c) {
                return $c->orders->contains(fn($o) => $o->status === 'cancelled');
            });
        } elseif ($delivery === 'no_orders') {
            $unifiedCustomers = $unifiedCustomers->filter(fn($c) => $c->orders_count === 0);
        }

        // Apply Search Filter
        if (!empty($search)) {
            $searchLower = strtolower($search);
            $unifiedCustomers = $unifiedCustomers->filter(function($c) use ($searchLower) {
                return str_contains(strtolower($c->name), $searchLower) ||
                       str_contains(strtolower($c->email), $searchLower) ||
                       str_contains(strtolower($c->phone ?? ''), $searchLower) ||
                       str_contains(strtolower($c->city ?? ''), $searchLower) ||
                       str_contains(strtolower($c->address ?? ''), $searchLower);
            });
        }

        // Sort by last order date desc
        $filteredCustomers = $unifiedCustomers->sortByDesc('last_order')->values();

        return view('tenant.customers.index', [
            'tenantId' => tenant('id'),
            'customers' => $filteredCustomers,
            'totalCustomers' => $totalCustomers,
            'newThisMonth' => $newThisMonth,
            'repeatCustomers' => $repeatCustomers,
            'citiesServed' => $citiesServed
        ]);
    });

    // 📥 10. ایڈمن کسٹمرز سی ایس وی ایکسپورٹ (Export Customers to CSV with Filters)
    Route::get('/shop/customers/export', function () {
        $type = request('type', 'all');
        $delivery = request('delivery', 'all');
        $search = request('search', '');

        $registeredCustomers = App\Models\Customer::all();
        $guestOrders = App\Models\Order::whereNull('customer_id')->get();
        $guestGroups = $guestOrders->groupBy(function($order) {
            return $order->customer_phone ?: $order->customer_name;
        });

        $unifiedCustomers = collect();

        // 1. Add registered customers
        foreach ($registeredCustomers as $customer) {
            $cOrders = App\Models\Order::where('customer_id', $customer->id)->get();
            $unifiedCustomers->push((object)[
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'city' => $customer->city,
                'address' => $customer->address,
                'orders_count' => $cOrders->count(),
                'total_spent' => $cOrders->sum('total'),
                'orders' => $cOrders,
                'created_at' => $customer->created_at,
                'last_order' => $cOrders->max('created_at'),
                'is_guest' => 'Registered'
            ]);
        }

        // 2. Add guest customers
        $registeredPhones = $registeredCustomers->pluck('phone')->filter()->toArray();
        foreach ($guestGroups as $phoneOrName => $orders) {
            $firstOrder = $orders->first();
            if ($firstOrder->customer_phone && in_array($firstOrder->customer_phone, $registeredPhones)) {
                continue;
            }
            $unifiedCustomers->push((object)[
                'name' => $firstOrder->customer_name,
                'email' => 'Guest Customer',
                'phone' => $firstOrder->customer_phone,
                'city' => $firstOrder->customer_city,
                'address' => $firstOrder->customer_address,
                'orders_count' => $orders->count(),
                'total_spent' => $orders->sum('total'),
                'orders' => $orders,
                'created_at' => $orders->min('created_at'),
                'last_order' => $orders->max('created_at'),
                'is_guest' => 'Guest'
            ]);
        }

        // Apply Timeframe / Group Filters
        if ($type === 'new') {
            $unifiedCustomers = $unifiedCustomers->filter(fn($c) => $c->orders_count === 1);
        } elseif ($type === 'this_month') {
            $unifiedCustomers = $unifiedCustomers->filter(function($c) {
                $joinedThisMonth = $c->created_at && \Carbon\Carbon::parse($c->created_at)->format('Y-m') === now()->format('Y-m');
                $firstOrderThisMonth = $c->last_order && \Carbon\Carbon::parse($c->last_order)->format('Y-m') === now()->format('Y-m');
                return $joinedThisMonth || $firstOrderThisMonth;
            });
        } elseif ($type === 'repeat') {
            $unifiedCustomers = $unifiedCustomers->filter(fn($c) => $c->orders_count > 1);
        }

        // Apply Delivery Status Filters
        if ($delivery === 'delivered') {
            $unifiedCustomers = $unifiedCustomers->filter(function($c) {
                return $c->orders->contains(fn($o) => $o->status === 'completed');
            });
        } elseif ($delivery === 'returned') {
            $unifiedCustomers = $unifiedCustomers->filter(function($c) {
                return $c->orders->contains(fn($o) => $o->status === 'cancelled');
            });
        } elseif ($delivery === 'no_orders') {
            $unifiedCustomers = $unifiedCustomers->filter(fn($c) => $c->orders_count === 0);
        }

        // Apply Search Filter
        if (!empty($search)) {
            $searchLower = strtolower($search);
            $unifiedCustomers = $unifiedCustomers->filter(function($c) use ($searchLower) {
                return str_contains(strtolower($c->name), $searchLower) ||
                       str_contains(strtolower($c->email), $searchLower) ||
                       str_contains(strtolower($c->phone ?? ''), $searchLower) ||
                       str_contains(strtolower($c->city ?? ''), $searchLower) ||
                       str_contains(strtolower($c->address ?? ''), $searchLower);
            });
        }

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=customers_export_" . date('Y-m-d') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($unifiedCustomers) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['Name', 'Email/Type', 'Phone', 'City', 'Address', 'Total Orders', 'Total Spent (Rs)', 'Account Status']);
            foreach ($unifiedCustomers as $c) {
                fputcsv($file, [
                    $c->name,
                    $c->email,
                    $c->phone,
                    $c->city,
                    $c->address,
                    $c->orders_count,
                    $c->total_spent,
                    $c->is_guest
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    });

    // 👤 11. کسٹمر کی تفصیلات (Registered Customer Details)
    Route::get('/shop/customers/{id}', function ($id) {
        $customer = App\Models\Customer::findOrFail($id);
        $orders = App\Models\Order::where('customer_id', $customer->id)->latest()->get();
        $totalSpent = $orders->sum('total');
        $aov = $orders->count() > 0 ? round($totalSpent / $orders->count()) : 0;

        return view('tenant.customers.show', [
            'tenantId' => tenant('id'),
            'customer' => $customer,
            'orders' => $orders,
            'totalSpent' => $totalSpent,
            'aov' => $aov
        ]);
    });

    // 👤 12. گیسٹ کسٹمر کی تفصیلات (Guest Customer Details)
    Route::get('/shop/customers/guest/{key}', function ($key) {
        $decoded = base64_decode($key);
        $orders = App\Models\Order::whereNull('customer_id')
            ->where(function($query) use ($decoded) {
                $query->where('customer_phone', $decoded)
                      ->orWhere('customer_name', $decoded);
            })
            ->latest()
            ->get();

        if ($orders->isEmpty()) {
            abort(404);
        }

        $firstOrder = $orders->first();
        $totalSpent = $orders->sum('total');
        $aov = $orders->count() > 0 ? round($totalSpent / $orders->count()) : 0;

        $customer = (object)[
            'id' => null,
            'is_guest' => true,
            'guest_key' => $key,
            'name' => $firstOrder->customer_name,
            'email' => 'Guest Customer',
            'phone' => $firstOrder->customer_phone,
            'city' => $firstOrder->customer_city,
            'address' => $firstOrder->customer_address,
            'created_at' => $orders->min('created_at'),
        ];

        return view('tenant.customers.show', [
            'tenantId' => tenant('id'),
            'customer' => $customer,
            'orders' => $orders,
            'totalSpent' => $totalSpent,
            'aov' => $aov
        ]);
    });

    // ---------------- CUSTOMER AUTH & DASHBOARD ----------------
    
    // Customer Registration
    Route::get('/customer/register', function () {
        if (auth('customer')->check()) {
            return redirect('/customer/dashboard');
        }
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        return view('tenant.customer.register', [
            'tenantId' => tenant('id'),
            'settings' => $settings
        ]);
    });

    Route::post('/customer/register', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $customer = App\Models\Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'city' => $request->city,
            'address' => $request->address,
            'password' => Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        auth('customer')->login($customer);

        return redirect('/customer/dashboard')->with('success', 'Registration successful! Welcome to your dashboard. 🎉');
    });

    // Customer Login
    Route::get('/customer/login', function () {
        if (auth('customer')->check()) {
            return redirect('/customer/dashboard');
        }
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        return view('tenant.customer.login', [
            'tenantId' => tenant('id'),
            'settings' => $settings
        ]);
    });

    Route::post('/customer/login', function (Request $request) {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (auth('customer')->attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect('/customer/dashboard')->with('success', 'Logged in successfully! 👋');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    });

    // Customer Logout
    Route::post('/customer/logout', function (Request $request) {
        auth('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Logged out successfully! 👋');
    });

    // Customer Dashboard
    Route::get('/customer/dashboard', function () {
        if (!auth('customer')->check()) {
            return redirect('/customer/login');
        }
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        $customer = auth('customer')->user();
        $orders = App\Models\Order::where('customer_id', $customer->id)->latest()->get();

        return view('tenant.customer.dashboard', [
            'tenantId' => tenant('id'),
            'settings' => $settings,
            'customer' => $customer,
            'orders' => $orders
        ]);
    });

});

// ============================================================
// WHATSAPP WEBHOOK ROUTES — OUTSIDE tenant middleware (no CSRF)
// ============================================================

// Webhook verification (GET) — Meta sends this to verify callback URL
Route::get('/webhook/whatsapp/{tenantId}', function ($tenantId, Request $request) {
    $verifyToken = $request->query('hub_verify_token');
    $challenge = $request->query('hub_challenge');

    Log::info('Webhook verification attempt', [
        'tenant_id' => $tenantId,
        'token' => $verifyToken,
        'challenge' => $challenge,
    ]);

    // Check central provider verify token first
    $centralToken = '';
    try {
        $provider = \DB::connection(config('tenancy.database.central_connection'))
            ->table('whatsapp_providers')
            ->where('is_active', true)
            ->first();
        $centralToken = $provider->verify_token ?? '';
    } catch (\Exception $e) {
        Log::warning('Webhook: Could not load central provider: ' . $e->getMessage());
    }

    // Fallback: check tenant settings verify token
    $tenantToken = '';
    try {
        if (tenancy()->initialized) {
            $settings = \App\Models\StoreSetting::firstOrCreate(['id' => 1]);
            $tenantToken = $settings->whatsapp_verify_token ?? '';
        }
    } catch (\Exception $e) {}

    Log::info('Webhook token check', ['central' => $centralToken, 'tenant' => $tenantToken, 'sent' => $verifyToken]);

    if (!empty($verifyToken) && $verifyToken === $centralToken && !empty($centralToken)) {
        Log::info('Webhook verified successfully (central token)');
        return response($challenge, 200)->header('Content-Type', 'text/plain');
    }

    if (!empty($verifyToken) && !empty($tenantToken) && $verifyToken === $tenantToken) {
        Log::info('Webhook verified successfully (tenant token)');
        return response($challenge, 200)->header('Content-Type', 'text/plain');
    }

    Log::warning('Webhook verification FAILED', ['sent' => $verifyToken, 'central' => $centralToken, 'tenant' => $tenantToken]);
    return response('Forbidden', 403);
});

// Webhook incoming messages (POST)
Route::post('/webhook/whatsapp/{tenantId}', function ($tenantId, Request $request) {
    $payload = $request->all();
    try {
        tenancy()->initialize(tenant($tenantId));
    } catch (\Exception $e) {
        return response('Tenant not found', 404);
    }

    $whatsapp = new \App\Services\WhatsAppCRM();
    $result = $whatsapp->handleWebhook($payload);
    return response()->json($result);
});

// Cloud API incoming webhook — store messages from customers
Route::post('/webhook/whatsapp/{tenantId}/incoming', function ($tenantId, Request $request) {
    $payload = $request->all();
    try {
        tenancy()->initialize(tenant($tenantId));
    } catch (\Exception $e) {
        return response('Tenant not found', 404);
    }

    $entry = $payload['entry'][0] ?? null;
    $changes = $entry['changes'][0] ?? null;
    $messages = ($changes['value'] ?? [])['messages'] ?? [];

    foreach ($messages as $msg) {
        $from = preg_replace('/[^0-9]/', '', $msg['from'] ?? '');
        $text = $msg['text']['body'] ?? '';
        $msgType = $msg['type'] ?? 'text';

        if (!$from || !$text) continue;

        $conversation = \DB::table('whatsapp_conversations')
            ->where('tenant_id', $tenantId)
            ->where('customer_phone', $from)
            ->orderByDesc('last_message_at')
            ->first();

        if ($conversation) {
            \DB::table('whatsapp_messages')->insert([
                'conversation_id' => $conversation->id,
                'tenant_id' => $tenantId,
                'direction' => 'inbound',
                'message_type' => $msgType,
                'message_body' => $text,
                'from_phone' => $from,
                'to_phone' => '',
                'status' => 'received',
                'is_auto' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \DB::table('whatsapp_conversations')
                ->where('id', $conversation->id)
                ->update([
                    'last_message_at' => now(),
                    'unread_count' => \DB::raw('unread_count + 1'),
                ]);
        }
    }

    return response()->json(['status' => 'processed']);
});