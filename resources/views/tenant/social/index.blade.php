<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <base href="{{ tenant_store_url('/') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ strtoupper($tenantId) }} - Social Media & Tracking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .card-premium {
            background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.04);
            border-radius: 28px; padding: 30px;
        }
        .input-premium {
            background-color: #ffffff; border: 2px solid #e2e8f0;
            font-size: 0.875rem; font-weight: 700; color: #0f172a;
            border-radius: 16px; padding: 14px 16px; width: 100%;
            outline: none; transition: border-color 0.15s;
        }
        .input-premium:focus { border-color: #6366f1; background: #ffffff; }
        .tracking-block { max-height: 0; overflow: hidden; transition: max-height 0.35s cubic-bezier(0,1,0,1), opacity 0.25s; opacity: 0; }
        .tracking-block.open { max-height: 800px; opacity: 1; }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased bg-slate-50/50 pb-32">

    <!-- Nav -->
    <nav class="bg-slate-950 border-b border-slate-800 sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-600 text-white p-2.5 rounded-xl shadow-lg shadow-indigo-600/25">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </div>
                    <div>
                        <span class="text-white font-extrabold text-sm tracking-tight uppercase">{{ strtoupper($tenantId) }}</span>
                        <span class="text-indigo-400 text-[10px] font-bold block uppercase tracking-wider">Social Media & Tracking</span>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/shop" class="text-slate-400 hover:text-white font-bold text-xs transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        <span>Dashboard</span>
                    </a>
                    <span class="text-slate-700">|</span>
                    <a href="{{ tenant_store_url() }}" target="_blank" class="text-slate-400 hover:text-white text-xs font-bold transition">View Storefront</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="relative z-10 max-w-7xl mx-auto mt-12 px-6">

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl font-bold flex items-center gap-3 shadow-sm">
                <span class="text-lg">✅</span>
                <span class="text-xs font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Header -->
        <div class="flex justify-between items-center mb-8 pb-5 border-b border-slate-200">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">Social Media & Tracking Pixels</h1>
                <p class="text-xs text-slate-500 mt-1 font-medium">Connect ad platforms, analytics pixels, and social media profiles to your storefront.</p>
            </div>
        </div>

        <form action="/shop/social" method="POST" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <!-- LEFT: Tracking Pixels (7 cols) -->
                <div class="lg:col-span-7 space-y-6">

                    <!-- Meta / Facebook Pixel -->
                    <div class="card-premium relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-blue-500"></div>
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-500 to-blue-700 flex items-center justify-center text-white shadow-md">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base">Meta Pixel (Facebook/Instagram)</h3>
                                    <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Track conversions & run ads</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="meta_pixel_active" value="1" class="sr-only peer" {{ $settings->meta_pixel_active ? 'checked' : '' }} onchange="toggleBlock('meta_fields', this)">
                                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-blue-600 peer-focus:ring-2 peer-focus:ring-blue-300 transition after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>
                        <div id="meta_fields" class="tracking-block {{ $settings->meta_pixel_active ? 'open' : '' }} space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Meta Pixel ID</label>
                                <input type="text" name="meta_pixel_id" value="{{ $settings->meta_pixel_id ?? '' }}" placeholder="e.g. 1379136546872653" class="input-premium">
                                <p class="text-[10px] text-slate-400 mt-1.5">Find this in Meta Events Manager > Data Sources</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Custom Events (one per line)</label>
                                <textarea name="meta_pixel_events" rows="3" placeholder="e.g.&#10;ViewContent&#10;AddToCart&#10;Purchase" class="input-premium text-xs">{{ $settings->meta_pixel_events ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Google Ads & Analytics -->
                    <div class="card-premium relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-red-500"></div>
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-red-500 to-yellow-500 flex items-center justify-center text-white shadow-md">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base">Google Ads & Analytics</h3>
                                    <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Conversion tracking & GA4</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="google_ads_active" value="1" class="sr-only peer" {{ $settings->google_ads_active ? 'checked' : '' }} onchange="toggleBlock('google_fields', this)">
                                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-blue-600 peer-focus:ring-2 peer-focus:ring-blue-300 transition after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>
                        <div id="google_fields" class="tracking-block {{ $settings->google_ads_active ? 'open' : '' }} space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Conversion ID</label>
                                    <input type="text" name="google_ads_conversion_id" value="{{ $settings->google_ads_conversion_id ?? '' }}" placeholder="e.g. AW-1234567890" class="input-premium">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Conversion Label</label>
                                    <input type="text" name="google_ads_conversion_label" value="{{ $settings->google_ads_conversion_label ?? '' }}" placeholder="e.g. AbCdEfGh1234" class="input-premium">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Google Analytics ID (GA4)</label>
                                <input type="text" name="google_analytics_id" value="{{ $settings->google_analytics_id ?? '' }}" placeholder="e.g. G-XXXXXXXXXX" class="input-premium">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Google Tag Manager ID</label>
                                <input type="text" name="google_tag_manager_id" value="{{ $settings->google_tag_manager_id ?? '' }}" placeholder="e.g. GTM-XXXXXXX" class="input-premium">
                            </div>
                        </div>
                    </div>

                    <!-- TikTok Pixel -->
                    <div class="card-premium relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-pink-500"></div>
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-pink-500 to-black flex items-center justify-center text-white shadow-md">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 0010.86 4.48V13.2a8.16 8.16 0 005.58 2.17v-3.44a4.85 4.85 0 01-5.58-2.75V6.69h5.58z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base">TikTok Pixel</h3>
                                    <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Track TikTok ad conversions</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="tiktok_pixel_active" value="1" class="sr-only peer" {{ $settings->tiktok_pixel_active ? 'checked' : '' }} onchange="toggleBlock('tiktok_fields', this)">
                                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-blue-600 peer-focus:ring-2 peer-focus:ring-blue-300 transition after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>
                        <div id="tiktok_fields" class="tracking-block {{ $settings->tiktok_pixel_active ? 'open' : '' }} space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">TikTok Pixel ID</label>
                                <input type="text" name="tiktok_pixel_id" value="{{ $settings->tiktok_pixel_id ?? '' }}" placeholder="e.g. Cxxxxxxxxxxxxxxxxx" class="input-premium">
                            </div>
                        </div>
                    </div>

                    <!-- Snapchat Pixel -->
                    <div class="card-premium relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-yellow-400"></div>
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-yellow-400 to-yellow-500 flex items-center justify-center text-white shadow-md">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12.206.793c.99 0 4.347.276 5.93 3.821.529 1.193.403 3.219.299 4.847l-.003.06c-.012.18-.022.345-.03.51.075.045.203.09.401.09.3-.016.659-.12.923-.214.094-.042.199-.06.3-.06.333 0 .653.155.866.393.174.197.23.46.146.71-.27.844-1.146.984-1.436 1.014-.047.005-.09.012-.138.02-.296.045-.557.192-.666.451-.09.211-.06.46.091.66.486.656 1.14.98 1.826 1.284.13.06.263.116.396.173.48.21.936.536 1.078 1.088.09.36.067.75-.137 1.11-.465.816-1.587 1.2-2.713 1.321-.36.04-.726.042-1.083.042-.16 0-.314-.006-.462-.016a7.59 7.59 0 00-.184-.012c-.442 0-.886.03-1.32.078l-.155.02c-.22.04-.44.086-.652.148-.09.026-.18.054-.266.086l-.048.018c-.55.195-.96.54-1.417.92l-.017.014c-.67.555-1.436 1.186-2.826 1.186-1.41 0-2.173-.622-2.834-1.166l-.024-.02c-.452-.375-.86-.717-1.413-.916l-.05-.018c-.215-.063-.437-.11-.66-.15l-.144-.024c-.442-.048-.89-.078-1.34-.078-.166 0-.33.006-.494.016a8.3 8.3 0 00-.186.013c-.443.02-.896.004-1.326-.042-1.142-.12-2.25-.505-2.723-1.336-.2-.352-.225-.745-.133-1.108.14-.55.594-.874 1.07-1.082.135-.057.27-.115.4-.174.69-.306 1.35-.63 1.84-1.292.148-.196.177-.444.09-.654-.108-.26-.37-.407-.667-.451-.045-.007-.088-.014-.134-.019-.292-.03-1.172-.17-1.442-1.015-.087-.252-.03-.516.143-.713.213-.238.533-.392.865-.392.102 0 .204.018.3.06.273.096.642.2 1.005.2.19 0 .322-.045.39-.09-.005-.165-.016-.33-.028-.51l-.003-.06c-.104-1.628-.23-3.654.3-4.847C7.855 1.07 11.216.794 12.206.794z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base">Snapchat Pixel</h3>
                                    <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Track Snapchat ad conversions</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="snapchat_pixel_active" value="1" class="sr-only peer" {{ $settings->snapchat_pixel_active ? 'checked' : '' }} onchange="toggleBlock('snapchat_fields', this)">
                                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-blue-600 peer-focus:ring-2 peer-focus:ring-blue-300 transition after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>
                        <div id="snapchat_fields" class="tracking-block {{ $settings->snapchat_pixel_active ? 'open' : '' }} space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Snapchat Pixel ID</label>
                                <input type="text" name="snapchat_pixel_id" value="{{ $settings->snapchat_pixel_id ?? '' }}" placeholder="e.g. xxxxxxxxxxxxxxxxxxxx" class="input-premium">
                            </div>
                        </div>
                    </div>

                    <!-- Pinterest Tag -->
                    <div class="card-premium relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-red-600"></div>
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-red-500 to-red-700 flex items-center justify-center text-white shadow-md">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base">Pinterest Tag</h3>
                                    <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Track Pinterest ad conversions</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="pinterest_tag_active" value="1" class="sr-only peer" {{ $settings->pinterest_tag_active ? 'checked' : '' }} onchange="toggleBlock('pinterest_fields', this)">
                                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-blue-600 peer-focus:ring-2 peer-focus:ring-blue-300 transition after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>
                        <div id="pinterest_fields" class="tracking-block {{ $settings->pinterest_tag_active ? 'open' : '' }} space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Pinterest Tag ID</label>
                                <input type="text" name="pinterest_tag_id" value="{{ $settings->pinterest_tag_id ?? '' }}" placeholder="e.g. 2613xxxxxxxxxxxx" class="input-premium">
                            </div>
                        </div>
                    </div>

                    <!-- Twitter/X Pixel -->
                    <div class="card-premium relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-slate-900"></div>
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-900 flex items-center justify-center text-white shadow-md">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base">Twitter / X Pixel</h3>
                                    <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Track X ad conversions</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="twitter_pixel_active" value="1" class="sr-only peer" {{ $settings->twitter_pixel_active ? 'checked' : '' }} onchange="toggleBlock('twitter_fields', this)">
                                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-blue-600 peer-focus:ring-2 peer-focus:ring-blue-300 transition after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>
                        <div id="twitter_fields" class="tracking-block {{ $settings->twitter_pixel_active ? 'open' : '' }} space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Twitter / X Pixel ID</label>
                                <input type="text" name="twitter_pixel_id" value="{{ $settings->twitter_pixel_id ?? '' }}" placeholder="e.g. o8p6r" class="input-premium">
                            </div>
                        </div>
                    </div>

                    <!-- Custom Tracking Code -->
                    <div class="card-premium relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-purple-500"></div>
                        <div class="flex items-center justify-between mb-6 pb-5 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-500 to-indigo-600 flex items-center justify-center text-white shadow-md">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-extrabold text-slate-900 text-base">Custom Tracking Code</h3>
                                    <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider">Add any custom script or pixel</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="custom_tracking_active" value="1" class="sr-only peer" {{ $settings->custom_tracking_active ? 'checked' : '' }} onchange="toggleBlock('custom_fields', this)">
                                <div class="w-11 h-6 bg-slate-200 rounded-full peer peer-checked:bg-blue-600 peer-focus:ring-2 peer-focus:ring-blue-300 transition after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full"></div>
                            </label>
                        </div>
                        <div id="custom_fields" class="tracking-block {{ $settings->custom_tracking_active ? 'open' : '' }} space-y-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Head Scripts (inside &lt;head&gt;)</label>
                                <textarea name="custom_tracking_head" rows="5" placeholder="Paste your &lt;head&gt; tracking code here..." class="input-premium text-xs font-mono">{{ $settings->custom_tracking_head ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Body Scripts (after &lt;body&gt;)</label>
                                <textarea name="custom_tracking_body" rows="5" placeholder="Paste your &lt;body&gt; tracking code here..." class="input-premium text-xs font-mono">{{ $settings->custom_tracking_body ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Social Profiles (5 cols) -->
                <div class="lg:col-span-5 space-y-6">

                    <div class="card-premium relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-24 h-[3px] bg-indigo-500"></div>
                        <h3 class="font-extrabold text-slate-900 text-base mb-2">Social Media Profiles</h3>
                        <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider mb-6 pb-5 border-b border-slate-100">Add your social profile URLs</p>

                        <div class="space-y-4">
                            <!-- Facebook -->
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="w-10 h-10 rounded-lg bg-blue-600 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </div>
                                <input type="url" name="facebook_page_url" value="{{ $settings->facebook_page_url ?? '' }}" placeholder="Facebook Page URL" class="input-premium text-xs">
                            </div>
                            <!-- Instagram -->
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-purple-500 via-pink-500 to-orange-400 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </div>
                                <input type="url" name="instagram_url" value="{{ $settings->instagram_url ?? '' }}" placeholder="Instagram Profile URL" class="input-premium text-xs">
                            </div>
                            <!-- TikTok -->
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="w-10 h-10 rounded-lg bg-black flex items-center justify-center text-white shrink-0">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1v-3.5a6.37 6.37 0 00-.79-.05A6.34 6.34 0 003.15 15.2a6.34 6.34 0 0010.86 4.48V13.2a8.16 8.16 0 005.58 2.17v-3.44a4.85 4.85 0 01-5.58-2.75V6.69h5.58z"/></svg>
                                </div>
                                <input type="url" name="tiktok_url" value="{{ $settings->tiktok_url ?? '' }}" placeholder="TikTok Profile URL" class="input-premium text-xs">
                            </div>
                            <!-- YouTube -->
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="w-10 h-10 rounded-lg bg-red-600 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </div>
                                <input type="url" name="youtube_url" value="{{ $settings->youtube_url ?? '' }}" placeholder="YouTube Channel URL" class="input-premium text-xs">
                            </div>
                            <!-- Twitter/X -->
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="w-10 h-10 rounded-lg bg-slate-900 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </div>
                                <input type="url" name="twitter_url" value="{{ $settings->twitter_url ?? '' }}" placeholder="Twitter / X Profile URL" class="input-premium text-xs">
                            </div>
                            <!-- Snapchat -->
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="w-10 h-10 rounded-lg bg-yellow-400 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.206.793c.99 0 4.347.276 5.93 3.821.529 1.193.403 3.219.299 4.847l-.003.06c-.012.18-.022.345-.03.51.075.045.203.09.401.09.3-.016.659-.12.923-.214.094-.042.199-.06.3-.06.333 0 .653.155.866.393.174.197.23.46.146.71-.27.844-1.146.984-1.436 1.014-.047.005-.09.012-.138.02-.296.045-.557.192-.666.451-.09.211-.06.46.091.66.486.656 1.14.98 1.826 1.284.13.06.263.116.396.173.48.21.936.536 1.078 1.088.09.36.067.75-.137 1.11-.465.816-1.587 1.2-2.713 1.321-.36.04-.726.042-1.083.042-.16 0-.314-.006-.462-.016a7.59 7.59 0 00-.184-.012c-.442 0-.886.03-1.32.078l-.155.02c-.22.044-.44.086-.652.148-.09.026-.18.054-.266.086l-.048.018c-.55.195-.96.54-1.417.92l-.017.014c-.67.555-1.436 1.186-2.826 1.186-1.41 0-2.173-.622-2.834-1.166l-.024-.02c-.452-.375-.86-.717-1.413-.916l-.05-.018c-.215-.063-.437-.11-.66-.15l-.144-.024c-.442-.048-.89-.078-1.34-.078-.166 0-.33.006-.494.016a8.3 8.3 0 00-.186.013c-.443.02-.896.004-1.326-.042-1.142-.12-2.25-.505-2.723-1.336-.2-.352-.225-.745-.133-1.108.14-.55.594-.874 1.07-1.082.135-.057.27-.115.4-.174.69-.306 1.35-.63 1.84-1.292.148-.196.177-.444.09-.654-.108-.26-.37-.407-.667-.451-.045-.007-.088-.014-.134-.019-.292-.03-1.172-.17-1.442-1.015-.087-.252-.03-.516.143-.713.213-.238.533-.392.865-.392.102 0 .204.018.3.06.273.096.642.2 1.005.2.19 0 .322-.045.39-.09-.005-.165-.016-.33-.028-.51l-.003-.06c-.104-1.628-.23-3.654.3-4.847C7.855 1.07 11.216.794 12.206.794z"/></svg>
                                </div>
                                <input type="url" name="snapchat_url" value="{{ $settings->snapchat_url ?? '' }}" placeholder="Snapchat Profile URL" class="input-premium text-xs">
                            </div>
                            <!-- Pinterest -->
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="w-10 h-10 rounded-lg bg-red-600 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>
                                </div>
                                <input type="url" name="pinterest_url" value="{{ $settings->pinterest_url ?? '' }}" placeholder="Pinterest Profile URL" class="input-premium text-xs">
                            </div>
                            <!-- Telegram -->
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0a12 12 0 00-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                                </div>
                                <input type="url" name="telegram_url" value="{{ $settings->telegram_url ?? '' }}" placeholder="Telegram Channel URL" class="input-premium text-xs">
                            </div>
                            <!-- WhatsApp Business -->
                            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <div class="w-10 h-10 rounded-lg bg-green-500 flex items-center justify-center text-white shrink-0">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                </div>
                                <input type="url" name="whatsapp_business_url" value="{{ $settings->whatsapp_business_url ?? '' }}" placeholder="WhatsApp Business URL" class="input-premium text-xs">
                            </div>
                        </div>
                    </div>

                    <!-- Info Box -->
                    <div class="card-premium relative overflow-hidden bg-indigo-50/50 border-indigo-100">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 shrink-0 mt-0.5">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-extrabold text-indigo-900">How Tracking Works</h4>
                                <p class="text-[11px] text-indigo-700 mt-1 leading-relaxed">Enable a platform, enter your Pixel/Conversion ID, and save. The tracking code will be automatically injected into your storefront's &lt;head&gt; and &lt;body&gt; tags.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Save Bar -->
            <div class="fixed bottom-0 inset-x-0 bg-white/90 backdrop-blur-md border-t border-slate-200 z-50">
                <div class="max-w-7xl mx-auto px-6 py-4 flex justify-end">
                    <button type="submit" class="bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-black px-8 py-3.5 rounded-xl shadow-lg shadow-indigo-600/20 text-xs transition duration-150">
                        Save Social & Tracking Settings
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function toggleBlock(id, checkbox) {
            const block = document.getElementById(id);
            if (checkbox.checked) {
                block.classList.add('open');
            } else {
                block.classList.remove('open');
            }
        }
    </script>
</body>
</html>
