<!DOCTYPE html>
<html lang="en" class="h-screen overflow-hidden bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Customizer - {{ strtoupper($tenantId) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    <script>
        // Fix form actions: prefix all /shop/* paths with tenant prefix
        document.addEventListener('DOMContentLoaded', function() {
            var path = window.location.pathname;
            var parts = path.split('/').filter(Boolean);
            var tenantPrefix = parts.length > 0 && parts[0] !== 'shop' ? '/' + parts[0] : '';
            document.querySelectorAll('form[action^="/shop/"]').forEach(function(form) {
                form.setAttribute('action', tenantPrefix + form.getAttribute('action'));
            });
        });
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow: hidden;
        }
        aside {
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.04) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(139, 92, 246, 0.04) 0px, transparent 50%),
                radial-gradient(at 50% 100%, rgba(14, 165, 233, 0.04) 0px, transparent 50%);
            background-size: cover;
            position: relative;
        }
        .dotted-overlay {
            background-image: radial-gradient(#cbd5e1 0.8px, transparent 0.8px);
            background-size: 24px 24px;
        }
        .card-premium-settings {
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 12px -5px rgba(0, 0, 0, 0.02);
            border-radius: 16px;
            padding: 16px;
            transition: all 0.25s ease;
        }
        /* Custom scrollbar for control panel */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        /* Tab and content transitions */
        .tab-content { display: none; }
        .tab-content.active { display: flex; flex-direction: column; height: 100%; animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(4px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Form element enhancements */
        .input-premium {
            transition: all 0.2s ease-in-out;
        }
        .input-premium:focus {
            background-color: #ffffff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
        /* Accordion transition */
        .accordion-content {
            transition: max-height 0.3s cubic-bezier(0, 1, 0, 1);
        }
    </style>
</head>
<body class="h-screen flex flex-col bg-slate-900 text-slate-800 antialiased overflow-hidden">

    <!-- Top Design Header -->
    <header class="h-16 border-b border-slate-800 bg-slate-950 px-6 flex items-center justify-between z-50 shrink-0">
        <div class="flex items-center gap-3">
            <span class="bg-indigo-600 text-white p-2 rounded-xl shadow-lg shadow-indigo-600/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                </svg>
            </span>
            <div>
                <h1 class="text-sm font-black text-white tracking-tight flex items-center gap-2">
                    {{ strtoupper($tenantId) }} <span class="text-xs px-2 py-0.5 bg-slate-800 text-slate-400 font-bold rounded-full">Customizer</span>
                </h1>
                <div class="flex items-center gap-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Live Editing Mode</span>
                </div>
            </div>
        </div>

        <!-- Resolution Switcher (Center) -->
        <div class="flex items-center bg-slate-900 rounded-xl p-1 border border-slate-800 gap-1">
            <button type="button" onclick="setPreviewDevice('desktop')" id="btn-device-desktop" class="bg-slate-800 text-white px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2 shadow-sm border border-slate-700/50">
                💻 <span class="hidden sm:inline">Desktop</span>
            </button>
            <button type="button" onclick="setPreviewDevice('mobile')" id="btn-device-mobile" class="text-slate-400 hover:text-slate-200 px-3.5 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2">
                📱 <span class="hidden sm:inline">Mobile</span>
            </button>
        </div>

        <!-- Right Side Links -->
        <div class="flex items-center gap-4">
            <a href="{{ tenant_store_url() }}" target="_blank" class="hidden md:flex items-center gap-1.5 text-xs font-bold text-slate-400 hover:text-white transition">
                <span>View Store</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
            <a href="/shop" class="text-xs font-extrabold text-slate-300 hover:text-white bg-slate-900 hover:bg-slate-800 px-4 py-2 rounded-xl transition border border-slate-800">
                Exit Editor
            </a>
        </div>
    </header>

    <!-- Workspace Container -->
    <div class="flex flex-1 min-h-0 overflow-hidden">
        
        <!-- Left Panel: Sidebar Controller -->
        <aside class="w-[450px] border-r border-slate-200 flex flex-col shrink-0 z-40 shadow-xl shadow-slate-900/5 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-[4px] bg-pink-500 z-50"></div>
            <div class="absolute inset-0 dotted-overlay opacity-20 pointer-events-none z-0"></div>
            <div class="relative z-10 flex flex-col h-full min-h-0 w-full">
                
                <!-- Sidebar Navigation Tabs -->
                <div class="px-5 pt-4 pb-1 border-b border-slate-100 bg-transparent shrink-0">
                <div class="grid grid-cols-5 gap-y-2 gap-x-1 text-center">
                    <button onclick="switchTab('global')" id="tab-global" class="tab-btn pb-2.5 text-slate-500 hover:text-indigo-600 font-extrabold text-[10px] tracking-wider uppercase border-b-2 border-transparent transition flex items-center justify-center gap-1 active text-indigo-600 !border-indigo-600">
                        🎨 Theme
                    </button>
                    <button onclick="switchTab('sections')" id="tab-sections" class="tab-btn pb-2.5 text-slate-500 hover:text-indigo-600 font-extrabold text-[10px] tracking-wider uppercase border-b-2 border-transparent transition flex items-center justify-center gap-1">
                        🧩 Sections
                    </button>
                    <button onclick="switchTab('pages')" id="tab-pages" class="tab-btn pb-2.5 text-slate-500 hover:text-indigo-600 font-extrabold text-[10px] tracking-wider uppercase border-b-2 border-transparent transition flex items-center justify-center gap-1">
                        📄 Pages
                    </button>
                    <button onclick="switchTab('navigation')" id="tab-navigation" class="tab-btn pb-2.5 text-slate-500 hover:text-indigo-600 font-extrabold text-[10px] tracking-wider uppercase border-b-2 border-transparent transition flex items-center justify-center gap-1">
                        🗺️ Menu
                    </button>
                    <button onclick="switchTab('shoppage')" id="tab-shoppage" class="tab-btn pb-2.5 text-slate-500 hover:text-indigo-600 font-extrabold text-[10px] tracking-wider uppercase border-b-2 border-transparent transition flex items-center justify-center gap-1">
                        🛒 Shop
                    </button>
                </div>
            </div>

            <!-- Scrollable Content Area -->
            <div id="sidebar-scroll-container" class="flex-1 overflow-y-auto custom-scrollbar p-6 bg-transparent">
                
                <!-- ================= TAB 1: GLOBAL SETTINGS ================= -->
                <div id="content-global" class="tab-content active">
                    <form action="/shop/settings" method="POST" enctype="multipart/form-data" id="globalForm" class="space-y-6">
                        @csrf
                        <input type="hidden" name="form_type" value="global">
                        
                        <!-- Accordion Group -->
                        <div class="divide-y divide-slate-100">
                            
                            <!-- Accordion: Announcement -->
                            <div class="py-4 first:pt-0">
                                <button type="button" onclick="toggleAccordion('acc-announcement')" class="flex justify-between items-center w-full text-left py-2 group">
                                    <span class="flex items-center text-xs tracking-wide font-extrabold uppercase text-slate-600 group-hover:text-indigo-600 transition">
                                        <span class="bg-yellow-50 text-yellow-600 p-2 rounded-xl mr-3 text-base shadow-sm">📢</span> Announcement Bar
                                    </span>
                                    <svg id="arrow-acc-announcement" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition-transform duration-200 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div id="acc-announcement" class="accordion-content pt-4 space-y-4">
                                    <!-- Toggle Switch -->
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                        <div>
                                            <span class="text-xs font-bold text-slate-700">Display Announcement</span>
                                            <p class="text-[10px] text-slate-400">Show notification bar at storefront top</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="announcement_active" value="1" class="sr-only peer" {{ ($settings->announcement_active ?? false) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-350 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#004c3f]"></div>
                                        </label>
                                    </div>
                                    <!-- Marquee Toggle Switch -->
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                        <div>
                                            <span class="text-xs font-bold text-slate-700">Scrolling Marquee Text</span>
                                            <p class="text-[10px] text-slate-400">Make announcement text scroll continuously</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="announcement_marquee" value="1" class="sr-only peer" {{ ($settings->announcement_marquee ?? false) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-350 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#004c3f]"></div>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Text Message</label>
                                        <input type="text" name="announcement_text" value="{{ $settings->announcement_text }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Background</label>
                                            <div class="flex items-center gap-3">
                                                <div class="relative w-10 h-10 rounded-xl overflow-hidden border border-slate-200 shadow-sm cursor-pointer shrink-0">
                                                    <input type="color" name="announcement_bg_color" value="{{ $settings->announcement_bg_color }}" oninput="updateColorValue(this)" class="absolute inset-0 w-full h-full p-0 border-0 cursor-pointer scale-150 bg-transparent">
                                                </div>
                                                <div class="flex-1">
                                                    <input type="text" id="val-announcement_bg_color" value="{{ $settings->announcement_bg_color }}" readonly class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono text-slate-500 outline-none select-all">
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Text Color</label>
                                            <div class="flex items-center gap-3">
                                                <div class="relative w-10 h-10 rounded-xl overflow-hidden border border-slate-200 shadow-sm cursor-pointer shrink-0">
                                                    <input type="color" name="announcement_text_color" value="{{ $settings->announcement_text_color }}" oninput="updateColorValue(this)" class="absolute inset-0 w-full h-full p-0 border-0 cursor-pointer scale-150 bg-transparent">
                                                </div>
                                                <div class="flex-1">
                                                    <input type="text" id="val-announcement_text_color" value="{{ $settings->announcement_text_color }}" readonly class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono text-slate-500 outline-none select-all">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Font Size (فونٹ کا سائز)</label>
                                        <select name="announcement_font_size" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none text-slate-700">
                                            <option value="12px" {{ ($settings->announcement_font_size ?? '14px') == '12px' ? 'selected' : '' }}>12px (Small / چھوٹا)</option>
                                            <option value="14px" {{ ($settings->announcement_font_size ?? '14px') == '14px' ? 'selected' : '' }}>14px (Default / نارمل)</option>
                                            <option value="16px" {{ ($settings->announcement_font_size ?? '16px') == '16px' ? 'selected' : '' }}>16px (Medium / درمیانہ)</option>
                                            <option value="18px" {{ ($settings->announcement_font_size ?? '18px') == '18px' ? 'selected' : '' }}>18px (Large / بڑا)</option>
                                            <option value="20px" {{ ($settings->announcement_font_size ?? '20px') == '20px' ? 'selected' : '' }}>20px (Extra Large / بہت بڑا)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Accordion: Header -->
                            <div class="py-4">
                                <button type="button" onclick="toggleAccordion('acc-header')" class="flex justify-between items-center w-full text-left py-2 group">
                                    <span class="flex items-center text-xs tracking-wide font-extrabold uppercase text-slate-600 group-hover:text-indigo-600 transition">
                                        <span class="bg-blue-50 text-blue-600 p-2 rounded-xl mr-3 text-base shadow-sm">🏠</span> Header & Menu
                                    </span>
                                    <svg id="arrow-acc-header" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div id="acc-header" class="accordion-content hidden pt-4 space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Store Logo</label>
                                        @if($settings->header_logo)
                                            <div class="mb-3 p-2 border border-slate-100 rounded-xl bg-slate-50 flex items-center justify-between">
                                                <img class="h-10 w-auto object-contain" src="{{ tenant_asset($settings->header_logo) }}" alt="Logo">
                                                <span class="text-[10px] bg-indigo-50 text-indigo-600 font-bold px-2 py-1 rounded-full uppercase">Active</span>
                                            </div>
                                        @endif
                                        <div class="relative border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-xl p-4 text-center cursor-pointer transition bg-slate-50/50 group">
                                            <input type="file" name="header_logo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateFileName(this)">
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="text-xl group-hover:scale-110 transition duration-200">📁</span>
                                                <span class="text-xs font-bold text-slate-700" id="file-header_logo-label">Upload New Logo</span>
                                                <span class="text-[10px] text-slate-400">PNG, JPG up to 2MB</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Logo Height (لوگو کی اونچائی)</label>
                                        <div class="flex items-center gap-3">
                                            <input type="number" name="header_logo_height" value="{{ $settings->header_logo_height ?? 56 }}" min="20" max="250" class="w-24 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none text-slate-700">
                                            <span class="text-xs text-slate-400 font-bold">px</span>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex justify-between items-center mb-1">
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Navigation Links</label>
                                            <span class="text-[10px] text-indigo-500 font-black">Format: Label | URL</span>
                                        </div>
                                        @php 
                                            $menuText = ''; 
                                            if(is_array($settings->header_menu)) { 
                                                foreach($settings->header_menu as $item) { 
                                                    $menuText .= $item['label'] . ' | ' . $item['url'] . "\n"; 
                                                } 
                                            } 
                                        @endphp
                                        <textarea name="header_menu_links_text" rows="4" placeholder="Home | /&#10;Herbal Tea | #products&#10;About Us | /about" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs text-slate-700 outline-none input-premium leading-relaxed">{{ trim($menuText) }}</textarea>
                                    </div>
                                    <div class="p-3 bg-slate-50/50 border border-slate-150 rounded-2xl space-y-3">
                                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Menu Colors (مینو بار کلرز)</h4>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Background Color</label>
                                                <div class="flex items-center gap-1.5">
                                                    <input type="color" id="header_menu_bg_picker" value="{{ $settings->header_menu_bg ?? '#ffffff' }}" oninput="document.getElementById('header_menu_bg').value = this.value" class="w-7 h-7 border border-slate-200 rounded-md cursor-pointer bg-transparent p-0">
                                                    <input type="text" name="header_menu_bg" id="header_menu_bg" value="{{ $settings->header_menu_bg ?? '#ffffff' }}" oninput="document.getElementById('header_menu_bg_picker').value = this.value" class="flex-1 text-[10px] font-bold px-2 py-1.5 border border-slate-200 rounded-md outline-none">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Text Color</label>
                                                <div class="flex items-center gap-1.5">
                                                    <input type="color" id="header_menu_text_picker" value="{{ $settings->header_menu_text ?? '#1f2937' }}" oninput="document.getElementById('header_menu_text').value = this.value" class="w-7 h-7 border border-slate-200 rounded-md cursor-pointer bg-transparent p-0">
                                                    <input type="text" name="header_menu_text" id="header_menu_text" value="{{ $settings->header_menu_text ?? '#1f2937' }}" oninput="document.getElementById('header_menu_text_picker').value = this.value" class="flex-1 text-[10px] font-bold px-2 py-1.5 border border-slate-200 rounded-md outline-none">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Active BG Color</label>
                                                <div class="flex items-center gap-1.5">
                                                    <input type="color" id="header_menu_active_bg_picker" value="{{ $settings->header_menu_active_bg ?? '#f3f4f6' }}" oninput="document.getElementById('header_menu_active_bg').value = this.value" class="w-7 h-7 border border-slate-200 rounded-md cursor-pointer bg-transparent p-0">
                                                    <input type="text" name="header_menu_active_bg" id="header_menu_active_bg" value="{{ $settings->header_menu_active_bg ?? '#f3f4f6' }}" oninput="document.getElementById('header_menu_active_bg_picker').value = this.value" class="flex-1 text-[10px] font-bold px-2 py-1.5 border border-slate-200 rounded-md outline-none">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Active Text Color</label>
                                                <div class="flex items-center gap-1.5">
                                                    <input type="color" id="header_menu_active_text_picker" value="{{ $settings->header_menu_active_text ?? '#16a34a' }}" oninput="document.getElementById('header_menu_active_text').value = this.value" class="w-7 h-7 border border-slate-200 rounded-md cursor-pointer bg-transparent p-0">
                                                    <input type="text" name="header_menu_active_text" id="header_menu_active_text" value="{{ $settings->header_menu_active_text ?? '#16a34a' }}" oninput="document.getElementById('header_menu_active_text_picker').value = this.value" class="flex-1 text-[10px] font-bold px-2 py-1.5 border border-slate-200 rounded-md outline-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Accordion: Hero Banner -->
                            <div class="py-4">
                                <button type="button" onclick="toggleAccordion('acc-hero')" class="flex justify-between items-center w-full text-left py-2 group">
                                    <span class="flex items-center text-xs tracking-wide font-extrabold uppercase text-slate-600 group-hover:text-indigo-600 transition">
                                        <span class="bg-purple-50 text-purple-600 p-2 rounded-xl mr-3 text-base shadow-sm">🌟</span> Hero Banner
                                    </span>
                                    <svg id="arrow-acc-hero" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div id="acc-hero" class="accordion-content hidden pt-4 space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Background Layout Type</label>
                                        <select name="hero_layout_type" onchange="toggleHeroFields()" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none input-premium">
                                            <option value="none" {{ ($settings->hero_layout_type ?? '') == 'none' ? 'selected' : '' }}>None (Hide Hero Banner)</option>
                                            <option value="color" {{ ($settings->hero_layout_type ?? '') == 'color' ? 'selected' : '' }}>Solid Background Color</option>
                                            <option value="image" {{ ($settings->hero_layout_type ?? '') == 'image' ? 'selected' : '' }}>Full Image Background</option>
                                            <option value="custom_code" {{ ($settings->hero_layout_type ?? '') == 'custom_code' ? 'selected' : '' }}>Custom Banner Code (HTML)</option>
                                        </select>
                                    </div>

                                    <!-- Block for Text/Title (Applicable for Color and Image backgrounds) -->
                                    <div id="hero-text-controls" class="space-y-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Main Title</label>
                                            <input type="text" name="hero_title" value="{{ $settings->hero_title }}" placeholder="Welcome to Our Store!" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Subtitle</label>
                                            <textarea name="hero_subtitle" rows="2" placeholder="Discover our premium products..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none">{{ $settings->hero_subtitle }}</textarea>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Button Text</label>
                                                <input type="text" name="hero_btn_text" value="{{ $settings->hero_btn_text ?? '' }}" placeholder="Shop Now" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Button Link</label>
                                                <input type="text" name="hero_btn_link" value="{{ $settings->hero_btn_link ?? '' }}" placeholder="#products" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Button 2 Text</label>
                                                <input type="text" name="hero_btn2_text" value="{{ $settings->hero_btn2_text ?? '' }}" placeholder="Learn More" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Button 2 Link</label>
                                                <input type="text" name="hero_btn2_link" value="{{ $settings->hero_btn2_link ?? '' }}" placeholder="#products" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none">
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Banner Height</label>
                                                <select name="hero_height" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none input-premium">
                                                    <option value="small" {{ ($settings->hero_height ?? '') == 'small' ? 'selected' : '' }}>Small</option>
                                                    <option value="medium" {{ ($settings->hero_height ?? 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                                    <option value="large" {{ ($settings->hero_height ?? '') == 'large' ? 'selected' : '' }}>Large</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Content Alignment</label>
                                                <select name="hero_align" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none input-premium">
                                                    <option value="left" {{ ($settings->hero_align ?? '') == 'left' ? 'selected' : '' }}>Left</option>
                                                    <option value="center" {{ ($settings->hero_align ?? 'center') == 'center' ? 'selected' : '' }}>Center</option>
                                                    <option value="right" {{ ($settings->hero_align ?? '') == 'right' ? 'selected' : '' }}>Right</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                            <div>
                                                <span class="text-xs font-bold text-slate-700">Show Container on Desktop</span>
                                                <p class="text-[10px] text-slate-400">Puts text inside a solid card</p>
                                            </div>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="hero_show_container" value="1" class="sr-only peer" {{ ($settings->hero_show_container ?? false) ? 'checked' : '' }}>
                                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-350 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#004c3f]"></div>
                                            </label>
                                        </div>
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Background Overlay Opacity</label>
                                                <span class="text-[10px] text-indigo-500 font-black" id="opacity-val-label">{{ $settings->hero_overlay_opacity ?? 50 }}%</span>
                                            </div>
                                            <input type="range" name="hero_overlay_opacity" min="0" max="95" step="5" value="{{ $settings->hero_overlay_opacity ?? 50 }}" oninput="document.getElementById('opacity-val-label').innerText = this.value + '%'" class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
                                        </div>
                                    </div>

                                    <!-- Fields for Solid Color layout -->
                                    <div id="hero-block-color" class="grid grid-cols-2 gap-4 hidden">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">BG Color</label>
                                            <div class="flex items-center gap-2">
                                                <div class="relative w-8 h-8 rounded-lg overflow-hidden border border-slate-200 cursor-pointer shrink-0">
                                                    <input type="color" name="hero_bg_color" value="{{ $settings->hero_bg_color ?? '#eff6ff' }}" oninput="updateColorValue(this)" class="absolute inset-0 w-full h-full p-0 border-0 cursor-pointer scale-150 bg-transparent">
                                                </div>
                                                <input type="text" id="val-hero_bg_color" value="{{ $settings->hero_bg_color ?? '#eff6ff' }}" readonly class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-mono text-slate-500 outline-none">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Text Color</label>
                                            <div class="flex items-center gap-2">
                                                <div class="relative w-8 h-8 rounded-lg overflow-hidden border border-slate-200 cursor-pointer shrink-0">
                                                    <input type="color" name="hero_text_color" value="{{ $settings->hero_text_color ?? '#1e3a8a' }}" oninput="updateColorValue(this)" class="absolute inset-0 w-full h-full p-0 border-0 cursor-pointer scale-150 bg-transparent">
                                                </div>
                                                <input type="text" id="val-hero_text_color" value="{{ $settings->hero_text_color ?? '#1e3a8a' }}" readonly class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-mono text-slate-500 outline-none">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fields for Image Background layout -->
                                    <div id="hero-block-image" class="space-y-3 hidden">
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Hero Banner Images (Slider / سلائیڈر کے لیے ایک یا زیادہ تصاویر اپلوڈ کریں)</label>
                                        @php
                                            $heroImages = $settings->hero_images ?? ($settings->hero_image ? [$settings->hero_image] : []);
                                        @endphp
                                        <div id="hero-images-container" class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                            @foreach($heroImages as $img)
                                                <div class="relative group border border-slate-200 rounded-xl overflow-hidden bg-slate-50 aspect-video flex items-center justify-center" data-image-path="{{ $img }}">
                                                    <img class="w-full h-full object-cover" src="{{ tenant_asset($img) }}" alt="Hero Image">
                                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity duration-200">
                                                        <button type="button" onclick="deleteHeroImage('{{ $img }}')" class="bg-red-600 hover:bg-red-700 text-white p-2 rounded-full shadow-lg transition-transform transform hover:scale-110">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <input type="hidden" name="remaining_hero_images" id="remaining_hero_images" value="{{ json_encode($heroImages) }}">
                                        
                                        <div class="relative border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-xl p-4 text-center cursor-pointer transition bg-slate-50/50 group mt-3">
                                            <input type="file" name="hero_images[]" accept="image/*" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateHeroFilesLabel(this)">
                                            <div class="flex flex-col items-center gap-1">
                                                <span class="text-xl group-hover:scale-110 transition duration-200">📸</span>
                                                <span class="text-xs font-bold text-slate-700" id="file-hero_images-label">Upload One or More Banner Images</span>
                                                <span class="text-[10px] text-slate-400">Optimal size: 1920x800px. Multiple files allowed.</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fields for Custom Code layout -->
                                    <div id="hero-block-code" class="hidden">
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Custom HTML Banner Code</label>
                                        <textarea name="hero_custom_code" rows="5" placeholder="<div class='custom-banner'>...</div>" class="w-full px-4 py-3 bg-slate-900 border border-slate-800 rounded-xl font-mono text-xs text-emerald-400 outline-none leading-relaxed">{{ $settings->hero_custom_code }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Accordion: Shop Page Design -->
                            <div class="py-4">
                                <button type="button" onclick="toggleAccordion('acc-shop-design')" class="flex justify-between items-center w-full text-left py-2 group">
                                    <span class="flex items-center text-xs tracking-wide font-extrabold uppercase text-slate-600 group-hover:text-indigo-600 transition">
                                        <span class="bg-indigo-50 text-indigo-600 p-2 rounded-xl mr-3 text-base shadow-sm">🛍️</span> Shop Page Design
                                    </span>
                                    <svg id="arrow-acc-shop-design" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div id="acc-shop-design" class="accordion-content hidden pt-4 space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                        <div>
                                            <span class="text-xs font-bold text-slate-700">Show Page Header Banner</span>
                                            <p class="text-[10px] text-slate-400">Display colored banner at the top of shop catalog</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="collection_show_banner" value="1" class="sr-only peer" {{ ($settings->collection_show_banner ?? true) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-350 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Shop Banner Title</label>
                                        <input type="text" name="collection_title" value="{{ $settings->collection_title }}" placeholder="e.g. All Products" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Shop Banner Subtitle</label>
                                        <textarea name="collection_subtitle" rows="2" placeholder="e.g. Discover our premium herbal collections" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none">{{ $settings->collection_subtitle }}</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Banner BG</label>
                                            <div class="flex items-center gap-2">
                                                <div class="relative w-8 h-8 rounded-lg overflow-hidden border border-slate-200 cursor-pointer shrink-0">
                                                    <input type="color" name="collection_banner_bg" value="{{ $settings->collection_banner_bg ?? '#eff6ff' }}" oninput="updateColorValue(this)" class="absolute inset-0 w-full h-full p-0 border-0 cursor-pointer scale-150 bg-transparent">
                                                </div>
                                                <input type="text" id="val-collection_banner_bg" value="{{ $settings->collection_banner_bg ?? '#eff6ff' }}" readonly class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-mono text-slate-500 outline-none">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Banner Text Color</label>
                                            <div class="flex items-center gap-2">
                                                <div class="relative w-8 h-8 rounded-lg overflow-hidden border border-slate-200 cursor-pointer shrink-0">
                                                    <input type="color" name="collection_banner_text_color" value="{{ $settings->collection_banner_text_color ?? '#1e3a8a' }}" oninput="updateColorValue(this)" class="absolute inset-0 w-full h-full p-0 border-0 cursor-pointer scale-150 bg-transparent">
                                                </div>
                                                <input type="text" id="val-collection_banner_text_color" value="{{ $settings->collection_banner_text_color ?? '#1e3a8a' }}" readonly class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-mono text-slate-500 outline-none">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Accordion: Footer -->
                            <div class="py-4">
                                <button type="button" onclick="toggleAccordion('acc-footer')" class="flex justify-between items-center w-full text-left py-2 group">
                                    <span class="flex items-center text-xs tracking-wide font-extrabold uppercase text-slate-600 group-hover:text-indigo-600 transition">
                                        <span class="bg-emerald-50 text-emerald-600 p-2 rounded-xl mr-3 text-base shadow-sm">📋</span> Footer Configuration
                                    </span>
                                    <svg id="arrow-acc-footer" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div id="acc-footer" class="accordion-content hidden pt-4 space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Footer About Text</label>
                                        <textarea name="footer_about" rows="3" placeholder="Herbal and wellness storefront..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none">{{ $settings->footer_about }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Support Email</label>
                                        <input type="email" name="footer_email" value="{{ $settings->footer_email }}" placeholder="contact@shop.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Support Phone</label>
                                            <input type="text" name="footer_phone" value="{{ $settings->footer_phone }}" placeholder="+923001234567" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">WhatsApp Number</label>
                                            <input type="text" name="footer_whatsapp" value="{{ $settings->footer_whatsapp }}" placeholder="03001234567" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Physical Address</label>
                                        <input type="text" name="footer_address" value="{{ $settings->footer_address }}" placeholder="Office 12, Floor 2, Block A, City" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Footer BG</label>
                                            <div class="flex items-center gap-2">
                                                <div class="relative w-8 h-8 rounded-lg overflow-hidden border border-slate-200 cursor-pointer shrink-0">
                                                    <input type="color" name="footer_bg_color" value="{{ $settings->footer_bg_color ?? '#1e293b' }}" oninput="updateColorValue(this)" class="absolute inset-0 w-full h-full p-0 border-0 cursor-pointer scale-150 bg-transparent">
                                                </div>
                                                <input type="text" id="val-footer_bg_color" value="{{ $settings->footer_bg_color ?? '#1e293b' }}" readonly class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-mono text-slate-500 outline-none">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Footer Text</label>
                                            <div class="flex items-center gap-2">
                                                <div class="relative w-8 h-8 rounded-lg overflow-hidden border border-slate-200 cursor-pointer shrink-0">
                                                    <input type="color" name="footer_text_color" value="{{ $settings->footer_text_color ?? '#ffffff' }}" oninput="updateColorValue(this)" class="absolute inset-0 w-full h-full p-0 border-0 cursor-pointer scale-150 bg-transparent">
                                                </div>
                                                <input type="text" id="val-footer_text_color" value="{{ $settings->footer_text_color ?? '#ffffff' }}" readonly class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-mono text-slate-500 outline-none">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Footer Bottom BG</label>
                                            <div class="flex items-center gap-2">
                                                <div class="relative w-8 h-8 rounded-lg overflow-hidden border border-slate-200 cursor-pointer shrink-0">
                                                    <input type="color" name="footer_bottom_bg_color" value="{{ $settings->footer_bottom_bg_color ?? '#1B5E20' }}" oninput="updateColorValue(this)" class="absolute inset-0 w-full h-full p-0 border-0 cursor-pointer scale-150 bg-transparent">
                                                </div>
                                                <input type="text" id="val-footer_bottom_bg_color" value="{{ $settings->footer_bottom_bg_color ?? '#1B5E20' }}" readonly class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-mono text-slate-500 outline-none">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Footer Bottom Text</label>
                                            <div class="flex items-center gap-2">
                                                <div class="relative w-8 h-8 rounded-lg overflow-hidden border border-slate-200 cursor-pointer shrink-0">
                                                    <input type="color" name="footer_bottom_text_color" value="{{ $settings->footer_bottom_text_color ?? '#ffffff' }}" oninput="updateColorValue(this)" class="absolute inset-0 w-full h-full p-0 border-0 cursor-pointer scale-150 bg-transparent">
                                                </div>
                                                <input type="text" id="val-footer_bottom_text_color" value="{{ $settings->footer_bottom_text_color ?? '#ffffff' }}" readonly class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-mono text-slate-500 outline-none">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Quick Links</label>
                                            </div>
                                            @php $qlText = ''; if(is_array($settings->footer_quick_links)) { foreach($settings->footer_quick_links as $l) { $qlText .= $l['label'] . ' | ' . $l['url'] . "\n"; } } @endphp
                                            <textarea name="footer_quick_links_text" rows="3" placeholder="Home | /&#10;Shop | #products" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono text-[10px] text-slate-700 outline-none input-premium leading-normal">{{ trim($qlText) }}</textarea>
                                        </div>
                                        <div>
                                            <div class="flex justify-between items-center mb-1">
                                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Policy Links</label>
                                            </div>
                                            @php $plText = ''; if(is_array($settings->footer_policies_links)) { foreach($settings->footer_policies_links as $l) { $plText .= $l['label'] . ' | ' . $l['url'] . "\n"; } } @endphp
                                            <textarea name="footer_policies_links_text" rows="3" placeholder="Privacy Policy | /privacy" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-mono text-[10px] text-slate-700 outline-none input-premium leading-normal">{{ trim($plText) }}</textarea>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Newsletter Sub-title Text</label>
                                        <input type="text" name="footer_newsletter_text" value="{{ $settings->footer_newsletter_text ?? '' }}" placeholder="Join our email list for exclusive offers." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Copyright text</label>
                                        <input type="text" name="footer_copyright" value="{{ $settings->footer_copyright ?? '' }}" placeholder="StoreName All rights reserved" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                    </div>
                                </div>
                            </div>

                            <!-- Accordion: Marketing & RTL -->
                            <div class="py-4">
                                <button type="button" onclick="toggleAccordion('acc-marketing')" class="flex justify-between items-center w-full text-left py-2 group">
                                    <span class="flex items-center text-xs tracking-wide font-extrabold uppercase text-slate-600 group-hover:text-indigo-600 transition">
                                        <span class="bg-red-50 text-red-600 p-2 rounded-xl mr-3 text-base shadow-sm">🚀</span> Marketing & RTL Settings
                                    </span>
                                    <svg id="arrow-acc-marketing" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div id="acc-marketing" class="accordion-content hidden pt-4 space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Facebook Pixel ID</label>
                                        <input type="text" name="facebook_pixel_id" value="{{ $settings->facebook_pixel_id ?? '' }}" placeholder="e.g. 1379136..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none">
                                    </div>

                                    <!-- RTL Setup -->
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                        <div>
                                            <span class="text-xs font-bold text-slate-700">RTL Urdu Typography</span>
                                            <p class="text-[10px] text-slate-400">Enable Jameel Noori Nastaleeq font storefront</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="enable_rtl" value="1" class="sr-only peer" {{ ($settings->enable_rtl ?? false) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-350 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>

                                    <!-- Live Sales Toggle -->
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                        <div>
                                            <span class="text-xs font-bold text-slate-700">Live Sales Notifications</span>
                                            <p class="text-[10px] text-slate-400">Display order popups to site visitors</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="enable_sales_popup" value="1" class="sr-only peer" {{ ($settings->enable_sales_popup ?? false) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-350 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>

                                    <!-- Sales Popup Data (Missing Field Added!) -->
                                    <div>
                                        <div class="flex justify-between items-center mb-1">
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Sales Notification Data</label>
                                            <span class="text-[10px] text-indigo-500 font-black">Format: Name | City | Item | Time</span>
                                        </div>
                                        @php
                                            $popupText = '';
                                            if (is_array($settings->sales_popup_data)) {
                                                foreach ($settings->sales_popup_data as $popup) {
                                                    $popupText .= ($popup['name'] ?? '') . ' | ' . ($popup['city'] ?? '') . ' | ' . ($popup['item'] ?? '') . ' | ' . ($popup['time'] ?? 'ابھی ابھی') . "\n";
                                                }
                                            }
                                        @endphp
                                        <textarea name="sales_popup_data_text" rows="4" placeholder="احمد | کراچی | ہربل شیمپو | ابھی ابھی&#10;سارہ | اسلام آباد | بیوٹی کریم | 5 منٹ پہلے" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-mono text-xs text-slate-750 outline-none input-premium leading-normal">{{ trim($popupText) }}</textarea>
                                        <span class="text-[10px] text-slate-400 mt-1 block">Put one customer order per line. Fields split by vertical bar (|).</span>
                                    </div>

                                    <!-- Disable Right Click/Inspect Toggle -->
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                        <div>
                                            <span class="text-xs font-bold text-slate-700">Protect Store Content</span>
                                            <p class="text-[10px] text-slate-400">Disable copy pasting and right click</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="disable_inspect" value="1" class="sr-only peer" {{ ($settings->disable_inspect ?? false) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-350 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Accordion: Storefront Checkout Buttons Customization -->
                            <div class="py-4 border-t border-slate-100/60">
                                <button type="button" onclick="toggleAccordion('acc-buttons')" class="flex justify-between items-center w-full text-left py-2 group">
                                    <span class="flex items-center text-xs tracking-wide font-extrabold uppercase text-slate-600 group-hover:text-indigo-600 transition">
                                        <span class="bg-green-50 text-green-600 p-2 rounded-xl mr-3 text-base shadow-sm">🛒</span> Buttons Customization
                                    </span>
                                    <svg id="arrow-acc-buttons" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div id="acc-buttons" class="accordion-content hidden pt-4 space-y-4">
                                    
                                    <!-- Add to Cart Button -->
                                    <div class="p-3 bg-slate-50/50 border border-slate-150 rounded-2xl space-y-3">
                                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Button 1: Add to Cart (کارٹ بٹن)</h4>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Button Text</label>
                                            <input type="text" name="btn_add_to_cart_text" value="{{ $settings->btn_add_to_cart_text ?? 'ADD TO CART' }}" placeholder="ADD TO CART" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold outline-none focus:bg-white focus:border-indigo-500 transition">
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Background Color</label>
                                                <div class="flex items-center gap-1.5">
                                                    <input type="color" id="btn_add_to_cart_bg_picker" value="{{ $settings->btn_add_to_cart_bg ?? '#16a34a' }}" oninput="document.getElementById('btn_add_to_cart_bg').value = this.value" class="w-7 h-7 border border-slate-200 rounded-md cursor-pointer bg-transparent p-0">
                                                    <input type="text" name="btn_add_to_cart_bg" id="btn_add_to_cart_bg" value="{{ $settings->btn_add_to_cart_bg ?? '#16a34a' }}" oninput="document.getElementById('btn_add_to_cart_bg_picker').value = this.value" class="flex-1 text-[10px] font-bold px-2 py-1.5 border border-slate-200 rounded-md outline-none">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Text Color</label>
                                                <div class="flex items-center gap-1.5">
                                                    <input type="color" id="btn_add_to_cart_text_color_picker" value="{{ $settings->btn_add_to_cart_text_color ?? '#ffffff' }}" oninput="document.getElementById('btn_add_to_cart_text_color').value = this.value" class="w-7 h-7 border border-slate-200 rounded-md cursor-pointer bg-transparent p-0">
                                                    <input type="text" name="btn_add_to_cart_text_color" id="btn_add_to_cart_text_color" value="{{ $settings->btn_add_to_cart_text_color ?? '#ffffff' }}" oninput="document.getElementById('btn_add_to_cart_text_color_picker').value = this.value" class="flex-1 text-[10px] font-bold px-2 py-1.5 border border-slate-200 rounded-md outline-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Buy Now Button -->
                                    <div class="p-3 bg-slate-50/50 border border-slate-150 rounded-2xl space-y-3">
                                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Button 2: Cash on Delivery (ڈلیوری بٹن)</h4>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Button Text</label>
                                            <input type="text" name="btn_buy_now_text" value="{{ $settings->btn_buy_now_text ?? 'Order Now - Cash on Delivery' }}" placeholder="Order Now - Cash on Delivery" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold outline-none focus:bg-white focus:border-indigo-500 transition">
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Background Color</label>
                                                <div class="flex items-center gap-1.5">
                                                    <input type="color" id="btn_buy_now_bg_picker" value="{{ $settings->btn_buy_now_bg ?? '#84cc16' }}" oninput="document.getElementById('btn_buy_now_bg').value = this.value" class="w-7 h-7 border border-slate-200 rounded-md cursor-pointer bg-transparent p-0">
                                                    <input type="text" name="btn_buy_now_bg" id="btn_buy_now_bg" value="{{ $settings->btn_buy_now_bg ?? '#84cc16' }}" oninput="document.getElementById('btn_buy_now_bg_picker').value = this.value" class="flex-1 text-[10px] font-bold px-2 py-1.5 border border-slate-200 rounded-md outline-none">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Text Color</label>
                                                <div class="flex items-center gap-1.5">
                                                    <input type="color" id="btn_buy_now_text_color_picker" value="{{ $settings->btn_buy_now_text_color ?? '#ffffff' }}" oninput="document.getElementById('btn_buy_now_text_color').value = this.value" class="w-7 h-7 border border-slate-200 rounded-md cursor-pointer bg-transparent p-0">
                                                    <input type="text" name="btn_buy_now_text_color" id="btn_buy_now_text_color" value="{{ $settings->btn_buy_now_text_color ?? '#ffffff' }}" oninput="document.getElementById('btn_buy_now_text_color_picker').value = this.value" class="flex-1 text-[10px] font-bold px-2 py-1.5 border border-slate-200 rounded-md outline-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Primary Button Customization -->
                                    <div class="p-3 bg-slate-50/50 border border-slate-150 rounded-2xl space-y-3">
                                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Global Primary Button (بنیادی بٹن کلرز)</h4>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Background Color</label>
                                                <div class="flex items-center gap-1.5">
                                                    <input type="color" id="btn_primary_bg_picker" value="{{ $settings->btn_primary_bg ?? '#16a34a' }}" oninput="document.getElementById('btn_primary_bg').value = this.value" class="w-7 h-7 border border-slate-200 rounded-md cursor-pointer bg-transparent p-0">
                                                    <input type="text" name="btn_primary_bg" id="btn_primary_bg" value="{{ $settings->btn_primary_bg ?? '#16a34a' }}" oninput="document.getElementById('btn_primary_bg_picker').value = this.value" class="flex-1 text-[10px] font-bold px-2 py-1.5 border border-slate-200 rounded-md outline-none">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Text Color</label>
                                                <div class="flex items-center gap-1.5">
                                                    <input type="color" id="btn_primary_text_picker" value="{{ $settings->btn_primary_text ?? '#ffffff' }}" oninput="document.getElementById('btn_primary_text').value = this.value" class="w-7 h-7 border border-slate-200 rounded-md cursor-pointer bg-transparent p-0">
                                                    <input type="text" name="btn_primary_text" id="btn_primary_text" value="{{ $settings->btn_primary_text ?? '#ffffff' }}" oninput="document.getElementById('btn_primary_text_picker').value = this.value" class="flex-1 text-[10px] font-bold px-2 py-1.5 border border-slate-200 rounded-md outline-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Secondary Button Customization -->
                                    <div class="p-3 bg-slate-50/50 border border-slate-150 rounded-2xl space-y-3">
                                        <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Global Secondary Button (ثانوی بٹن کلرز)</h4>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Background Color</label>
                                                <div class="flex items-center gap-1.5">
                                                    <input type="color" id="btn_secondary_bg_picker" value="{{ $settings->btn_secondary_bg ?? '#1f2937' }}" oninput="document.getElementById('btn_secondary_bg').value = this.value" class="w-7 h-7 border border-slate-200 rounded-md cursor-pointer bg-transparent p-0">
                                                    <input type="text" name="btn_secondary_bg" id="btn_secondary_bg" value="{{ $settings->btn_secondary_bg ?? '#1f2937' }}" oninput="document.getElementById('btn_secondary_bg_picker').value = this.value" class="flex-1 text-[10px] font-bold px-2 py-1.5 border border-slate-200 rounded-md outline-none">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Text Color</label>
                                                <div class="flex items-center gap-1.5">
                                                    <input type="color" id="btn_secondary_text_picker" value="{{ $settings->btn_secondary_text ?? '#ffffff' }}" oninput="document.getElementById('btn_secondary_text').value = this.value" class="w-7 h-7 border border-slate-200 rounded-md cursor-pointer bg-transparent p-0">
                                                    <input type="text" name="btn_secondary_text" id="btn_secondary_text" value="{{ $settings->btn_secondary_text ?? '#ffffff' }}" oninput="document.getElementById('btn_secondary_text_picker').value = this.value" class="flex-1 text-[10px] font-bold px-2 py-1.5 border border-slate-200 rounded-md outline-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Sticky Footer inside sidebar for Theme Save -->
                        <div class="pt-4 border-t border-slate-100 bg-white/80 backdrop-blur-md sticky bottom-0 z-10">
                            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-extrabold py-3.5 px-6 rounded-xl shadow-lg shadow-indigo-600/15 hover:shadow-indigo-600/25 transition duration-200 flex items-center justify-center gap-2">
                                <span>Save Theme Design</span> ✨
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= TAB 2: PAGE SECTIONS BUILDER ================= -->
                <div id="content-sections" class="tab-content">
                    
                    <!-- View A: List of active sections -->
                    <div id="sections-list-container" class="space-y-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Page Sections</h3>
                                <p class="text-[10px] text-slate-400 mt-0.5">Arrange and configure section cards</p>
                            </div>
                            <span class="text-xs bg-indigo-50 text-indigo-700 font-bold px-2.5 py-1 rounded-full uppercase">{{ count($sections) }} Sections</span>
                        </div>

                        <!-- Sections Visual Stack -->
                        <div class="space-y-3">
                            @forelse($sections as $sec)
                                <div id="section-item-{{ $sec->id }}" class="flex items-center justify-between bg-white border border-slate-200 hover:border-indigo-400 p-4 rounded-xl shadow-sm transition group cursor-pointer" onclick="editSection({{ $sec->id }}, '{{ $sec->type }}', '{{ addslashes($sec->title) }}', '{{ base64_encode(json_encode($sec->settings)) }}', '{{ base64_encode($sec->content ?? '') }}')">
                                    <div class="flex items-center gap-3">
                                        <div class="text-slate-300 group-hover:text-indigo-500 cursor-grab transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 8h16M4 16h16"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-800 text-xs tracking-tight">{{ $sec->title }}</h4>
                                            <span class="text-[9px] font-extrabold text-indigo-500 uppercase tracking-wider mt-0.5 block">
                                                @php
                                                    $icons = ['image_with_text'=>'🖼️ Image Text', 'discount_banner'=>'🎁 Discount Banner', 'features_bar'=>'✨ Trust Badges', 'testimonials'=>'⭐ Reviews', 'faq'=>'❓ FAQ', 'custom_code'=>'🧑‍💻 Custom Code'];
                                                    echo $icons[$sec->type] ?? str_replace('_', ' ', $sec->type);
                                                @endphp
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <form action="/shop/settings/section/delete/{{ $sec->id }}" method="POST" class="inline" onsubmit="localStorage.removeItem('editingSectionId'); event.stopPropagation();">
                                            @csrf
                                            <button type="submit" class="text-slate-300 hover:text-red-500 transition p-1.5 hover:bg-slate-50 rounded-lg" title="Delete Section">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50">
                                    <span class="text-3xl mb-2 block">📭</span>
                                    <p class="text-xs font-bold text-slate-400">Store Page is empty</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Add custom sections to populate page</p>
                                </div>
                            @endforelse
                        </div>

                        <button onclick="openSectionModal()" class="w-full bg-slate-50 hover:bg-slate-100/80 text-slate-800 font-extrabold py-3 px-4 rounded-xl flex items-center justify-center transition border border-slate-200 shadow-sm text-xs gap-2">
                            <span class="text-indigo-600 text-lg">+</span> Add Custom Section
                        </button>
                    </div>

                    <!-- View B: Dynamic Section Editor form -->
                    <form action="/shop/settings/section" method="POST" enctype="multipart/form-data" id="dynamic_section_form" class="hidden space-y-6">
                        @csrf
                        <input type="hidden" name="section_id" id="hidden_section_id">
                        <input type="hidden" name="section_type" id="hidden_section_type">

                        <!-- Editor Header -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <button type="button" onclick="cancelSection(); localStorage.removeItem('editingSectionId');" class="text-xs font-bold text-slate-400 hover:text-slate-700 flex items-center gap-1.5 transition">
                                <span>&larr; Back</span>
                            </button>
                            <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider" id="selected_section_title">Configure Section</h3>
                        </div>

                        <!-- Section Title (Usually Hidden but editable internally) -->
                        <input type="text" name="section_title" id="auto_section_title" class="w-full px-4 py-3 bg-slate-50 border border-slate-250 rounded-xl text-sm font-semibold input-premium outline-none hidden">

                        <!-- Sub-Form: Custom Code -->
                        <div id="fields_custom_code" class="section-fields hidden space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">HTML / CSS Code</label>
                                <textarea name="section_content" rows="12" placeholder="<!-- Write your custom code here -->" class="w-full px-4 py-3 bg-slate-950 border border-slate-900 rounded-xl font-mono text-xs text-emerald-400 outline-none leading-relaxed shadow-inner"></textarea>
                            </div>
                        </div>

                        <!-- Sub-Form: Discount Banner -->
                        <div id="fields_discount_banner" class="section-fields hidden space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Sale Badge</label>
                                <input type="text" name="banner_badge" placeholder="e.g. MEGA SALE" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Heading</label>
                                <input type="text" name="banner_heading" placeholder="e.g. Special Herbal Tea Deal" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Highlight Offer</label>
                                <input type="text" name="banner_highlight" placeholder="e.g. 50% OFF" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Banner Description</label>
                                <textarea name="banner_description" rows="3" placeholder="Explain details of this promotion..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Button Text</label>
                                <input type="text" name="banner_btn_text" placeholder="e.g. Buy Now" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                        </div>

                        <!-- Sub-Form: Image with Text -->
                        <div id="fields_image_with_text" class="section-fields hidden space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Upload Display Image</label>
                                <div class="relative border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-xl p-4 text-center cursor-pointer transition bg-slate-50/50 group">
                                    <input type="file" name="iwt_image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateFileName(this)">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="text-xl group-hover:scale-110 transition duration-200">🖼️</span>
                                        <span class="text-xs font-bold text-slate-700" id="file-iwt_image-label">Choose Image</span>
                                        <span class="text-[10px] text-slate-400">PNG, JPG up to 3MB</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Image Alignment</label>
                                <select name="iwt_layout" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none input-premium">
                                    <option value="image_left">Image on Left Side</option>
                                    <option value="image_right">Image on Right Side</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Heading</label>
                                <input type="text" name="iwt_heading" placeholder="e.g. Crafted with pure ingredients" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Description Paragraph</label>
                                <textarea name="iwt_text" rows="4" placeholder="Write description content..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none leading-relaxed"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Button Text</label>
                                    <input type="text" name="iwt_btn_text" placeholder="e.g. Read More" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Button Link</label>
                                    <input type="text" name="iwt_btn_link" placeholder="e.g. #products" value="#products" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Form: Features Bar -->
                        <div id="fields_features_bar" class="section-fields hidden space-y-4">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Features / Trust Badges (Max 4)</span>
                            <div class="grid grid-cols-2 gap-4">
                                @foreach([1,2,3,4] as $i)
                                    <div class="p-3 bg-slate-50 border border-slate-100 rounded-xl space-y-2.5">
                                        <span class="block text-[10px] font-black text-slate-400">Badge {{ $i }}</span>
                                        <div class="relative border border-dashed border-slate-200 hover:border-indigo-400 rounded-lg p-2 text-center cursor-pointer transition bg-white group">
                                            <input type="file" name="f{{$i}}_icon" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateFileName(this)">
                                            <div class="flex flex-col items-center gap-0.5">
                                                <span class="text-[10px] font-bold text-slate-600" id="file-f{{$i}}_icon-label">Add Icon</span>
                                            </div>
                                        </div>
                                        <input type="text" name="f{{$i}}_title" placeholder="Title" class="w-full px-2.5 py-1.5 border border-slate-200 bg-white rounded-lg text-xs font-bold text-slate-700 outline-none">
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Sub-Form: Testimonials -->
                        <div id="fields_testimonials" class="section-fields hidden space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Section Heading</label>
                                <input type="text" name="testi_heading" placeholder="e.g. Customer Reviews" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                            <div class="space-y-3">
                                @foreach([1,2,3] as $i)
                                    <div class="p-4 border border-indigo-50 rounded-2xl bg-indigo-50/20 space-y-2">
                                        <span class="text-[10px] font-black text-indigo-600 uppercase tracking-wider">Reviewer {{ $i }}</span>
                                        <input type="text" name="r{{$i}}_name" placeholder="Customer Name" class="w-full px-3 py-2 border border-slate-200 bg-white rounded-lg text-xs font-bold outline-none">
                                        <textarea name="r{{$i}}_text" rows="2" placeholder="5-Star Review Text..." class="w-full px-3 py-2 border border-slate-200 bg-white rounded-lg text-xs outline-none leading-normal"></textarea>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Sub-Form: FAQ -->
                        <div id="fields_faq" class="section-fields hidden space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">FAQ Heading</label>
                                <input type="text" name="faq_heading" placeholder="e.g. Frequently Asked Questions" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                            <div id="faq_dynamic_list" class="space-y-3.5">
                                <!-- Dynamic faq elements injected -->
                            </div>
                            <button type="button" onclick="addFaqField()" class="w-full border border-dashed border-indigo-200 text-indigo-650 bg-indigo-50/40 font-bold py-2.5 rounded-xl hover:bg-indigo-100/40 transition flex items-center justify-center text-xs gap-1.5">
                                <span>+ Add Question</span>
                            </button>
                        </div>

                        <!-- Sub-Form: Featured Products -->
                        <div id="fields_featured_products" class="section-fields hidden space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Section Heading</label>
                                <input type="text" name="fp_heading" placeholder="e.g. Featured Products" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Display Count</label>
                                <select name="fp_count" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none input-premium">
                                    <option value="4">4 Products</option>
                                    <option value="8">8 Products</option>
                                    <option value="12">12 Products</option>
                                </select>
                            </div>
                        </div>

                        <!-- Sub-Form: Video Banner -->
                        <div id="fields_video_banner" class="section-fields hidden space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Video Heading</label>
                                <input type="text" name="video_heading" placeholder="e.g. Watch Our Story" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Subheading</label>
                                <input type="text" name="video_subheading" placeholder="e.g. Learn how we harvest tea leaves..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Video Embed URL (MP4, YouTube, Vimeo)</label>
                                <input type="text" name="video_url" placeholder="e.g. https://www.youtube.com/embed/..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Button Text</label>
                                    <input type="text" name="video_btn_text" placeholder="e.g. Play Video" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Button Link</label>
                                    <input type="text" name="video_btn_link" placeholder="e.g. #products" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Form: Newsletter Form -->
                        <div id="fields_newsletter_form" class="section-fields hidden space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Newsletter Title</label>
                                <input type="text" name="news_heading" placeholder="e.g. Subscribe to our newsletter" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Subheading Description</label>
                                <input type="text" name="news_subheading" placeholder="e.g. Get weekly promotions and updates..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Input Placeholder</label>
                                    <input type="text" name="news_placeholder" placeholder="e.g. Enter your email" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Subscribe Button Text</label>
                                    <input type="text" name="news_btn_text" placeholder="e.g. Subscribe" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Form: Rich Text Banner -->
                        <div id="fields_rich_text" class="section-fields hidden space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Main Title</label>
                                <input type="text" name="rt_heading" placeholder="e.g. Talk about your brand" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Banner Rich Text Content</label>
                                <textarea name="rt_text" rows="5" placeholder="Share store details, highlights, promotions or brand values with your shoppers..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none leading-relaxed"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Button Label</label>
                                    <input type="text" name="rt_btn_text" placeholder="e.g. Learn More" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Button Link</label>
                                    <input type="text" name="rt_btn_link" placeholder="e.g. #products" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold input-premium outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Spacing (Padding) Controls -->
                        <div class="pt-4 border-t border-slate-100 space-y-4">
                            <span class="block text-[10px] font-black text-slate-400 uppercase tracking-wider">Section Layout Padding</span>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Top Padding</label>
                                    <select name="pt" id="spacing_pt" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none">
                                        <option value="pt-0">None (0)</option>
                                        <option value="pt-8">Small (pt-8)</option>
                                        <option value="pt-16" selected>Medium (pt-16)</option>
                                        <option value="pt-24">Large (pt-24)</option>
                                        <option value="pt-32">Extra Large (pt-32)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1.5">Bottom Padding</label>
                                    <select name="pb" id="spacing_pb" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 outline-none">
                                        <option value="pb-0">None (0)</option>
                                        <option value="pb-8">Small (pb-8)</option>
                                        <option value="pb-16" selected>Medium (pb-16)</option>
                                        <option value="pb-24">Large (pb-24)</option>
                                        <option value="pb-32">Extra Large (pb-32)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Section -->
                        <div class="pt-4 sticky bottom-0 bg-white/80 backdrop-blur-md">
                            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-extrabold py-3.5 px-6 rounded-xl shadow-lg hover:shadow-indigo-650/20 transition duration-200 flex items-center justify-center gap-1.5">
                                <span>Save Section Layout</span> ✨
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= TAB 3: PAGES MANAGER ================= -->
                <div id="content-pages" class="tab-content">
                    
                    <!-- View A: Pages List -->
                    <div id="pages-list-container" class="space-y-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Store Pages</h3>
                                <p class="text-[10px] text-slate-400 mt-0.5">Create and manage content or policy pages</p>
                            </div>
                            <span class="text-xs bg-indigo-50 text-indigo-700 font-bold px-2.5 py-1 rounded-full uppercase">{{ count($pages) }} Pages</span>
                        </div>

                        <!-- Pages stack -->
                        <div class="space-y-3">
                            @forelse($pages as $page)
                                <div class="flex items-center justify-between bg-white border border-slate-200 hover:border-indigo-400 p-4 rounded-xl shadow-sm transition group cursor-pointer" onclick="editPage({{ $page->id }}, '{{ addslashes($page->title) }}', '{{ $page->slug }}', '{{ base64_encode($page->content) }}', {{ $page->is_active ? 'true' : 'false' }}, {{ $page->is_policy ? 'true' : 'false' }})">
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-xs tracking-tight">{{ $page->title }}</h4>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-[9px] font-mono text-slate-400">/pages/{{ $page->slug }}</span>
                                            @if($page->is_policy)
                                                <span class="text-[8px] bg-emerald-50 text-emerald-600 border border-emerald-200 font-extrabold px-1.5 py-0.5 rounded-full uppercase">Policy</span>
                                            @endif
                                            @if(!$page->is_active)
                                                <span class="text-[8px] bg-red-50 text-red-500 border border-red-100 font-extrabold px-1.5 py-0.5 rounded-full uppercase">Draft</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <form action="/shop/settings/page/delete/{{ $page->id }}" method="POST" class="inline" onsubmit="event.stopPropagation(); return confirm('Are you sure you want to delete this page?');">
                                            @csrf
                                            <button type="submit" class="text-slate-350 hover:text-red-500 transition p-1.5 hover:bg-slate-50 rounded-lg" title="Delete Page">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50">
                                    <span class="text-3xl mb-2 block">📄</span>
                                    <p class="text-xs font-bold text-slate-400">No pages created yet</p>
                                    <p class="text-[10px] text-slate-400 mt-1">Add custom pages to links or policies</p>
                                </div>
                            @endforelse
                        </div>

                        <button type="button" onclick="showAddPage()" class="w-full bg-slate-50 hover:bg-slate-100/80 text-slate-800 font-extrabold py-3 px-4 rounded-xl flex items-center justify-center transition border border-slate-200 shadow-sm text-xs gap-2">
                            <span class="text-indigo-600 text-lg">+</span> Create New Page
                        </button>


                    </div>

                    <!-- View B: Add/Edit Page Form -->
                    <form action="/shop/settings/page" method="POST" id="dynamic_page_form" class="hidden space-y-6">
                        @csrf
                        <input type="hidden" name="page_id" id="page_id_hidden">
                        
                        <!-- Header -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <button type="button" onclick="cancelPageEdit()" class="text-xs font-bold text-slate-400 hover:text-slate-700 flex items-center gap-1.5 transition">
                                <span>&larr; Back</span>
                            </button>
                            <h3 class="font-extrabold text-slate-800 text-xs uppercase tracking-wider" id="page_editor_title">Add Page</h3>
                        </div>

                        <div class="space-y-4">
                            <!-- Page Layout Templates -->
                            <div class="bg-indigo-50/40 border border-indigo-100/75 rounded-2xl p-4 mb-4">
                                <label class="block text-[10px] font-black text-indigo-700 uppercase tracking-wider mb-2.5">رابطہ پیج ٹیمپلیٹس (Contact Page Templates)</label>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <button type="button" onclick="applyPageTemplate('blank')" id="tpl-blank" class="p-3 bg-white border-2 border-indigo-500 bg-indigo-50/25 rounded-xl text-center transition flex flex-col items-center justify-center group active-template">
                                        <span class="text-xl mb-1.5">📄</span>
                                        <span class="text-[9px] font-extrabold text-slate-700 group-hover:text-indigo-650">Blank Page</span>
                                    </button>
                                    <button type="button" onclick="applyPageTemplate('contact-split')" id="tpl-contact-split" class="p-3 bg-white border-2 border-slate-200 hover:border-indigo-500 rounded-xl text-center transition flex flex-col items-center justify-center group">
                                        <span class="text-xl mb-1.5">🌓</span>
                                        <span class="text-[9px] font-extrabold text-slate-700 group-hover:text-indigo-650">Split Column</span>
                                    </button>
                                    <button type="button" onclick="applyPageTemplate('contact-card')" id="tpl-contact-card" class="p-3 bg-white border-2 border-slate-200 hover:border-indigo-500 rounded-xl text-center transition flex flex-col items-center justify-center group">
                                        <span class="text-xl mb-1.5">📇</span>
                                        <span class="text-[9px] font-extrabold text-slate-700 group-hover:text-indigo-650">Centered Card</span>
                                    </button>
                                    <button type="button" onclick="applyPageTemplate('contact-grid')" id="tpl-contact-grid" class="p-3 bg-white border-2 border-slate-200 hover:border-indigo-500 rounded-xl text-center transition flex flex-col items-center justify-center group">
                                        <span class="text-xl mb-1.5">🏢</span>
                                        <span class="text-[9px] font-extrabold text-slate-700 group-hover:text-indigo-650">3-Col Grid</span>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Page Title</label>
                                <input type="text" name="title" id="page_title" required placeholder="e.g. Terms of Service" oninput="autoGenerateSlug(this.value)" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">URL Slug</label>
                                <input type="text" name="slug" id="page_slug" required placeholder="e.g. terms-of-service" class="w-full px-4 py-3 bg-slate-50 border border-slate-250 rounded-xl text-sm font-semibold input-premium outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Page Content</label>
                                <textarea name="content" id="page_content" class="hidden"></textarea>
                                <div id="page-content-editor" class="bg-white border border-slate-200 rounded-xl min-h-[300px] text-xs"></div>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                <div>
                                    <span class="text-xs font-bold text-slate-700">Publish Page</span>
                                    <p class="text-[10px] text-slate-400">Make this page active on storefront</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" id="page_is_active" value="1" class="sr-only peer" checked>
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-350 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                <div>
                                    <span class="text-xs font-bold text-slate-700">This is a Policy Page</span>
                                    <p class="text-[10px] text-slate-400">Tag it for standard store footer policies list</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_policy" id="page_is_policy" value="1" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-350 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                <div>
                                    <span class="text-xs font-bold text-slate-700">Add to Header Menu</span>
                                    <p class="text-[10px] text-slate-400">Add link to this page in header navigation automatically</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="add_to_header" id="page_add_to_header" value="1" class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-350 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                        </div>

                        <div class="pt-4 sticky bottom-0 bg-white/80 backdrop-blur-md">
                            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-extrabold py-3.5 px-6 rounded-xl shadow-lg transition duration-200 flex items-center justify-center gap-1.5">
                                <span>Save Page Details</span> 📄
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= TAB 4: NAVIGATION BUILDER ================= -->
                <div id="content-navigation" class="tab-content">
                    <form action="/shop/settings/navigation" method="POST" onsubmit="prepareNavigationSubmit()" class="space-y-8">
                        @csrf
                        <input type="hidden" name="header_menu_json" id="header_menu_json">
                        <input type="hidden" name="footer_quick_links_json" id="footer_quick_links_json">
                        <input type="hidden" name="footer_policies_links_json" id="footer_policies_links_json">

                        <!-- SECTION 1: HEADER MENU -->
                        <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/60 space-y-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Header Navigation Menu</h4>
                                    <p class="text-[9px] text-slate-400 mt-0.5">Displays links at storefront top header</p>
                                </div>
                            </div>
                            <div id="menu-items-header" class="space-y-2 max-h-64 overflow-y-auto custom-scrollbar pr-1">
                                <!-- JavaScript renders header menu items here -->
                            </div>
                            <div class="pt-3 border-t border-slate-200/50 grid grid-cols-1 gap-2.5">
                                <span class="block text-[9px] font-black text-slate-400 uppercase tracking-wider">Add Item to Header</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" id="add-header-label" placeholder="Link Label (e.g. About)" class="px-3 py-2 border border-slate-200 rounded-lg text-xs font-semibold outline-none bg-white">
                                    <select id="add-header-type" onchange="toggleLinkInput('header')" class="px-2 py-2 border border-slate-200 rounded-lg text-xs font-bold outline-none bg-white">
                                        <option value="home">Home Page (/)</option>
                                        <option value="shop">Products (#products)</option>
                                        <option value="page">Dynamic Page</option>
                                        <option value="custom">Custom URL</option>
                                    </select>
                                </div>
                                <div class="hidden" id="add-header-page-wrapper">
                                    <select id="add-header-page" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs font-bold outline-none bg-white">
                                        @foreach($pages as $p)
                                            <option value="/pages/{{ $p->slug }}">{{ $p->title }} (/pages/{{ $p->slug }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="hidden" id="add-header-custom-wrapper">
                                    <input type="text" id="add-header-custom" placeholder="URL (e.g. https://google.com)" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs font-semibold outline-none bg-white">
                                </div>
                                <button type="button" onclick="addNavigationItem('header')" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold py-2 px-4 rounded-xl text-[10px] transition border border-indigo-100">
                                    + Append Header Item
                                </button>
                            </div>
                        </div>

                        <!-- SECTION 2: FOOTER QUICK LINKS -->
                        <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/60 space-y-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Footer Quick Links</h4>
                                    <p class="text-[9px] text-slate-400 mt-0.5">Displays sitemap/quick lists in footer</p>
                                </div>
                            </div>
                            <div id="menu-items-quick" class="space-y-2 max-h-64 overflow-y-auto custom-scrollbar pr-1">
                                <!-- JavaScript renders quick links here -->
                            </div>
                            <div class="pt-3 border-t border-slate-200/50 grid grid-cols-1 gap-2.5">
                                <span class="block text-[9px] font-black text-slate-400 uppercase tracking-wider">Add Item to Quick Links</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" id="add-quick-label" placeholder="Link Label" class="px-3 py-2 border border-slate-200 rounded-lg text-xs font-semibold outline-none bg-white">
                                    <select id="add-quick-type" onchange="toggleLinkInput('quick')" class="px-2 py-2 border border-slate-200 rounded-lg text-xs font-bold outline-none bg-white">
                                        <option value="home">Home Page (/)</option>
                                        <option value="shop">Products (#products)</option>
                                        <option value="page">Dynamic Page</option>
                                        <option value="custom">Custom URL</option>
                                    </select>
                                </div>
                                <div class="hidden" id="add-quick-page-wrapper">
                                    <select id="add-quick-page" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs font-bold outline-none bg-white">
                                        @foreach($pages as $p)
                                            <option value="/pages/{{ $p->slug }}">{{ $p->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="hidden" id="add-quick-custom-wrapper">
                                    <input type="text" id="add-quick-custom" placeholder="URL" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs font-semibold outline-none bg-white">
                                </div>
                                <button type="button" onclick="addNavigationItem('quick')" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold py-2 px-4 rounded-xl text-[10px] transition border border-indigo-100">
                                    + Append Quick Link
                                </button>
                            </div>
                        </div>

                        <!-- SECTION 3: FOOTER POLICIES -->
                        <div class="bg-slate-50/70 p-5 rounded-2xl border border-slate-200/60 space-y-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Footer Policies Links</h4>
                                    <p class="text-[9px] text-slate-400 mt-0.5">Displays policy document links in footer</p>
                                </div>
                            </div>
                            <div id="menu-items-policies" class="space-y-2 max-h-64 overflow-y-auto custom-scrollbar pr-1">
                                <!-- JavaScript renders policy links here -->
                            </div>
                            <div class="pt-3 border-t border-slate-200/50 grid grid-cols-1 gap-2.5">
                                <span class="block text-[9px] font-black text-slate-400 uppercase tracking-wider">Add Item to Policies</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="text" id="add-policies-label" placeholder="Link Label" class="px-3 py-2 border border-slate-200 rounded-lg text-xs font-semibold outline-none bg-white">
                                    <select id="add-policies-type" onchange="toggleLinkInput('policies')" class="px-2 py-2 border border-slate-200 rounded-lg text-xs font-bold outline-none bg-white">
                                        <option value="home">Home Page (/)</option>
                                        <option value="shop">Products (#products)</option>
                                        <option value="page">Dynamic Page</option>
                                        <option value="custom">Custom URL</option>
                                    </select>
                                </div>
                                <div class="hidden" id="add-policies-page-wrapper">
                                    <select id="add-policies-page" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs font-bold outline-none bg-white">
                                        @foreach($pages as $p)
                                            <option value="/pages/{{ $p->slug }}">{{ $p->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="hidden" id="add-policies-custom-wrapper">
                                    <input type="text" id="add-policies-custom" placeholder="URL" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs font-semibold outline-none bg-white">
                                </div>
                                <button type="button" onclick="addNavigationItem('policies')" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold py-2 px-4 rounded-xl text-[10px] transition border border-indigo-100">
                                    + Append Policy Link
                                </button>
                            </div>
                        </div>

                        <!-- Submit Navigation -->
                        <div class="pt-4 sticky bottom-0 bg-white/80 backdrop-blur-md z-10 border-t border-slate-100">
                            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-extrabold py-3.5 px-6 rounded-xl shadow-lg transition duration-200 flex items-center justify-center gap-1.5">
                                <span>Save Navigation Menus</span> 🗺️
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ================= TAB 5: SHOP PAGE DESIGN ================= -->
                <div id="content-shoppage" class="tab-content">
                    <form action="/shop/settings" method="POST" enctype="multipart/form-data" onsubmit="localStorage.setItem('activeTab', 'shoppage')" class="space-y-6">
                        @csrf
                        <input type="hidden" name="form_type" value="shoppage">

                        <div class="divide-y divide-slate-100">

                            <!-- Collection Banner -->
                            <div class="py-4 first:pt-0">
                                <button type="button" onclick="toggleAccordion('acc-collection')" class="flex justify-between items-center w-full text-left py-2 group">
                                    <span class="flex items-center text-xs tracking-wide font-extrabold uppercase text-slate-600 group-hover:text-indigo-600 transition">
                                        <span class="bg-green-50 text-green-600 p-2 rounded-xl mr-3 text-base shadow-sm">🛒</span> Shop Page Banner
                                    </span>
                                    <svg id="arrow-acc-collection" class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transition-transform duration-200 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                <div id="acc-collection" class="accordion-content pt-4 space-y-4">
                                    <!-- Show Banner Toggle -->
                                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl">
                                        <div>
                                            <span class="text-xs font-bold text-slate-700">Show Collection Banner</span>
                                            <p class="text-[10px] text-slate-400">Display a styled header banner at top of shop page</p>
                                        </div>
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="collection_show_banner" value="1" class="sr-only peer" {{ ($settings->collection_show_banner ?? false) ? 'checked' : '' }}>
                                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-350 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#004c3f]"></div>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Shop Page Title</label>
                                        <input type="text" name="collection_title" value="{{ $settings->collection_title }}" placeholder="e.g. All Products / ہماری تمام پروڈکٹس" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Shop Page Subtitle</label>
                                        <textarea name="collection_subtitle" rows="2" placeholder="e.g. Browse our full catalog of products" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold input-premium outline-none resize-none">{{ $settings->collection_subtitle }}</textarea>
                                    </div>
                                    <!-- Banner Colors -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Banner Background</label>
                                            <div class="flex items-center gap-3">
                                                <div class="relative w-10 h-10 rounded-xl overflow-hidden border border-slate-200 shadow-sm cursor-pointer shrink-0">
                                                    <input type="color" name="collection_banner_bg" value="{{ $settings->collection_banner_bg ?? '#eff6ff' }}" oninput="updateColorValue(this)" class="absolute inset-0 w-full h-full p-0 border-0 cursor-pointer scale-150 bg-transparent">
                                                </div>
                                                <div class="flex-1">
                                                    <input type="text" id="val-collection_banner_bg" value="{{ $settings->collection_banner_bg ?? '#eff6ff' }}" readonly class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono text-slate-500 outline-none select-all">
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Banner Text Color</label>
                                            <div class="flex items-center gap-3">
                                                <div class="relative w-10 h-10 rounded-xl overflow-hidden border border-slate-200 shadow-sm cursor-pointer shrink-0">
                                                    <input type="color" name="collection_banner_text_color" value="{{ $settings->collection_banner_text_color ?? '#1e3a8a' }}" oninput="updateColorValue(this)" class="absolute inset-0 w-full h-full p-0 border-0 cursor-pointer scale-150 bg-transparent">
                                                </div>
                                                <div class="flex-1">
                                                    <input type="text" id="val-collection_banner_text_color" value="{{ $settings->collection_banner_text_color ?? '#1e3a8a' }}" readonly class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono text-slate-500 outline-none select-all">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Info -->
                            <div class="py-4">
                                <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100 rounded-2xl p-5 space-y-3">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-green-600 text-lg">🛍️</span>
                                        <span class="text-xs font-extrabold text-slate-700 uppercase tracking-wider">Shop Page Info</span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 leading-relaxed font-medium">
                                        The <strong class="text-green-700">/collection</strong> page shows all your products with sorting & filtering. It is automatically linked from your storefront navigation and "View All" buttons.
                                    </p>
                                    <a href="{{ tenant_store_url('/collection') }}" target="_blank" class="inline-flex items-center gap-1.5 text-[10px] font-extrabold text-white bg-green-600 hover:bg-green-700 px-3 py-2 rounded-xl transition">
                                        Preview Shop Page →
                                    </a>
                                </div>
                            </div>

                        </div>

                        <div class="pt-4 sticky bottom-0 bg-white/80 backdrop-blur-md">
                            <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-extrabold py-3.5 px-6 rounded-xl shadow-lg transition duration-200 flex items-center justify-center gap-1.5">
                                <span>Save Shop Page Settings</span> 🛒
                            </button>
                        </div>
                    </form>
                </div>

            </div>
            </div>
        </aside>

        <!-- Right Panel: Store Canvas Live Preview -->
        <main class="flex-1 bg-slate-900 flex flex-col items-center justify-center p-6 md:p-8 overflow-hidden relative select-none">
            
            <!-- Custom Browser Top Shell -->
            <div class="w-full max-w-5xl bg-slate-950 text-slate-400 px-4 py-2.5 rounded-t-2xl border border-slate-800 border-b-0 flex items-center gap-4 text-[10px] font-mono shrink-0 shadow-lg shadow-slate-950/20">
                <div class="flex gap-1.5 shrink-0">
                    <div class="w-3.5 h-3.5 rounded-full bg-rose-500/80 border border-rose-600/40"></div>
                    <div class="w-3.5 h-3.5 rounded-full bg-amber-500/80 border border-amber-600/40"></div>
                    <div class="w-3.5 h-3.5 rounded-full bg-emerald-500/80 border border-emerald-600/40"></div>
                </div>
                <!-- Mock Navigation buttons -->
                <div class="hidden sm:flex gap-1 shrink-0 text-slate-600">
                    <svg class="w-4 h-4 cursor-not-allowed" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    <svg class="w-4 h-4 cursor-not-allowed" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </div>
                <!-- Mock Address Input -->
                <div class="bg-slate-900 px-4 py-1.5 rounded-lg flex-1 text-center flex items-center justify-center gap-2 border border-slate-800 text-slate-400 font-semibold select-all font-sans text-xs">
                    <span class="text-emerald-500 text-[10px]">🔒 Secure Connection |</span> {{ tenant_store_url() }}
                </div>
                <!-- Refresh Button -->
                <button type="button" onclick="refreshPreview()" class="text-slate-400 hover:text-white transition p-1 hover:bg-slate-800 rounded-lg shrink-0" title="Refresh Live Storefront">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.5"></path></svg>
                </button>
            </div>
            
            <!-- Iframe Canvas frame wrapper -->
            <div id="preview-frame-wrapper" class="w-full max-w-5xl h-[calc(100vh-165px)] bg-white rounded-b-2xl border border-slate-800 overflow-hidden shadow-2xl transition-all duration-300">
                <iframe id="preview-iframe" src="{{ tenant_store_url('/') }}" class="w-full h-full border-0"></iframe>
            </div>
        </main>
    </div>

    <!-- ================= MODALS & OVERLAYS ================= -->

    <!-- Elegant Section Picker Modal -->
    <div id="sectionModal" class="fixed inset-0 z-[100] hidden">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity" onclick="closeSectionModal()"></div>
        <!-- Modal Content Container -->
        <div class="absolute inset-x-0 bottom-0 md:inset-auto md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 bg-white w-full md:w-[850px] md:rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh] transform transition-all border border-slate-100">
            <!-- Modal Header -->
            <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/80">
                <div>
                    <h3 class="font-extrabold text-slate-800 text-lg flex items-center">
                        <span class="bg-indigo-100 text-indigo-600 p-2 rounded-xl mr-3 text-base shadow-sm">🧩</span> Choose Section Template
                    </h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Select a prebuilt widget layout for your shop page</p>
                </div>
                <button onclick="closeSectionModal()" class="text-slate-400 hover:text-red-500 bg-white border border-slate-200/60 shadow-sm p-1.5 rounded-xl transition hover:bg-red-50">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Grid list of templates -->
            <div class="p-8 overflow-y-auto bg-slate-50/30 custom-scrollbar">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                    
                    <!-- Item: Image text -->
                    <div onclick="selectSection('image_with_text', 'Image with Text')" class="group bg-white border border-slate-200/80 hover:border-indigo-500 rounded-2xl p-5 hover:shadow-lg hover:shadow-indigo-500/5 cursor-pointer transition transform hover:-translate-y-0.5 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-3.5 group-hover:scale-105 transition duration-200 text-xl shadow-sm">🖼️</div>
                        <h4 class="font-extrabold text-slate-800 text-xs mb-1">Image with Text</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Pair photos/media alongside descriptive text layout.</p>
                    </div>

                    <!-- Item: discount banner -->
                    <div onclick="selectSection('discount_banner', 'Mega Discount Banner')" class="group bg-white border border-slate-200/80 hover:border-rose-500 rounded-2xl p-5 hover:shadow-lg hover:shadow-rose-500/5 cursor-pointer transition transform hover:-translate-y-0.5 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-3.5 group-hover:scale-105 transition duration-200 text-xl shadow-sm">🎁</div>
                        <h4 class="font-extrabold text-slate-800 text-xs mb-1">Discount Banner</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Promo banner card to highlight mega deals or vouchers.</p>
                    </div>

                    <!-- Item: features bar -->
                    <div onclick="selectSection('features_bar', 'Features / Trust Badges')" class="group bg-white border border-slate-200/80 hover:border-emerald-500 rounded-2xl p-5 hover:shadow-lg hover:shadow-emerald-500/5 cursor-pointer transition transform hover:-translate-y-0.5 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-3.5 group-hover:scale-105 transition duration-200 text-xl shadow-sm">✨</div>
                        <h4 class="font-extrabold text-slate-800 text-xs mb-1">Trust Badges</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Show icons representing shipping, guarantees or support.</p>
                    </div>
                    
                    <!-- Item: testimonials -->
                    <div onclick="selectSection('testimonials', 'Customer Reviews')" class="group bg-white border border-slate-200/80 hover:border-amber-500 rounded-2xl p-5 hover:shadow-lg hover:shadow-amber-500/5 cursor-pointer transition transform hover:-translate-y-0.5 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-3.5 group-hover:scale-105 transition duration-200 text-xl shadow-sm">⭐</div>
                        <h4 class="font-extrabold text-slate-800 text-xs mb-1">Customer Reviews</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Build consumer trust using slider customer reviews.</p>
                    </div>

                    <!-- Item: faq -->
                    <div onclick="selectSection('faq', 'Frequently Asked Questions')" class="group bg-white border border-slate-200/80 hover:border-violet-500 rounded-2xl p-5 hover:shadow-lg hover:shadow-violet-500/5 cursor-pointer transition transform hover:-translate-y-0.5 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-violet-50 text-violet-600 rounded-2xl flex items-center justify-center mb-3.5 group-hover:scale-105 transition duration-200 text-xl shadow-sm">❓</div>
                        <h4 class="font-extrabold text-slate-800 text-xs mb-1">FAQ Accordion</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Add collapsible Q/A section for buyer inquiries.</p>
                    </div>

                    <!-- Item: custom_code -->
                    <div onclick="selectSection('custom_code', 'Custom HTML/CSS')" class="group bg-white border border-slate-200/80 hover:border-slate-800 rounded-2xl p-5 hover:shadow-lg hover:shadow-slate-800/5 cursor-pointer transition transform hover:-translate-y-0.5 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center mb-3.5 group-hover:scale-105 transition duration-200 text-xl shadow-sm">🧑‍💻</div>
                        <h4 class="font-extrabold text-slate-800 text-xs mb-1">Custom Code</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Paste direct HTML/CSS blocks to render custom widgets.</p>
                    </div>

                    <!-- Item: featured_products -->
                    <div onclick="selectSection('featured_products', 'Featured Products Grid')" class="group bg-white border border-slate-200/80 hover:border-emerald-500 rounded-2xl p-5 hover:shadow-lg hover:shadow-emerald-500/5 cursor-pointer transition transform hover:-translate-y-0.5 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-3.5 group-hover:scale-105 transition duration-200 text-xl shadow-sm">🛍️</div>
                        <h4 class="font-extrabold text-slate-800 text-xs mb-1">Featured Products</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Display a clean grid of your shop's latest products.</p>
                    </div>

                    <!-- Item: video_banner -->
                    <div onclick="selectSection('video_banner', 'Video Banner Overlay')" class="group bg-white border border-slate-200/80 hover:border-blue-500 rounded-2xl p-5 hover:shadow-lg hover:shadow-blue-500/5 cursor-pointer transition transform hover:-translate-y-0.5 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-3.5 group-hover:scale-105 transition duration-200 text-xl shadow-sm">🎥</div>
                        <h4 class="font-extrabold text-slate-800 text-xs mb-1">Video Banner</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Embed YouTube, Vimeo or direct video with text overlays.</p>
                    </div>

                    <!-- Item: newsletter_form -->
                    <div onclick="selectSection('newsletter_form', 'Email Newsletter Form')" class="group bg-white border border-slate-200/80 hover:border-indigo-500 rounded-2xl p-5 hover:shadow-lg hover:shadow-indigo-500/5 cursor-pointer transition transform hover:-translate-y-0.5 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-3.5 group-hover:scale-105 transition duration-200 text-xl shadow-sm">✉️</div>
                        <h4 class="font-extrabold text-slate-800 text-xs mb-1">Newsletter Signup</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Collect email signups with customizable text cards.</p>
                    </div>

                    <!-- Item: rich_text -->
                    <div onclick="selectSection('rich_text', 'Rich Text Display')" class="group bg-white border border-slate-200/80 hover:border-amber-500 rounded-2xl p-5 hover:shadow-lg hover:shadow-amber-500/5 cursor-pointer transition transform hover:-translate-y-0.5 flex flex-col items-center text-center">
                        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-3.5 group-hover:scale-105 transition duration-200 text-xl shadow-sm">📝</div>
                        <h4 class="font-extrabold text-slate-800 text-xs mb-1">Rich Text Banner</h4>
                        <p class="text-[10px] text-slate-400 font-medium">Display clean titles and text blocks with buttons.</p>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Floating Success Toast Notification -->
    @if(session('success'))
    <div id="success-toast" class="fixed bottom-6 right-6 z-[200] transform translate-y-10 opacity-0 transition-all duration-300 ease-out pointer-events-none">
        <div class="bg-slate-950 text-white px-5 py-4 rounded-2xl shadow-2xl flex items-center gap-3.5 border border-slate-800 max-w-sm">
            <span class="bg-green-500 text-slate-950 w-7 h-7 flex items-center justify-center rounded-xl text-xs font-bold shrink-0">✓</span>
            <div class="flex flex-col">
                <span class="text-xs font-bold text-white">Customizer updated</span>
                <span class="text-[10px] text-slate-400 mt-0.5">{{ session('success') }}</span>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toast = document.getElementById('success-toast');
            if (toast) {
                // Show toast
                setTimeout(() => {
                    toast.classList.remove('translate-y-10', 'opacity-0');
                    toast.classList.add('translate-y-0', 'opacity-100');
                }, 100);
                
                // Hide toast
                setTimeout(() => {
                    toast.classList.remove('translate-y-0', 'opacity-100');
                    toast.classList.add('translate-y-10', 'opacity-0');
                }, 4000);
            }
        });
    </script>
    @endif

    <!-- Workspace JavaScript Controller -->
    <script>
        var pageQuill;
        // Switch Layout Tabs
        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active', 'text-indigo-600', 'border-indigo-600'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            const btn = document.getElementById('tab-' + tab);
            const content = document.getElementById('content-' + tab);
            
            if (btn) btn.classList.add('active', 'text-indigo-600', 'border-indigo-600');
            if (content) content.classList.add('active');
            
            localStorage.setItem('activeTab', tab);
        }

        // Accordion Management
        function toggleAccordion(id) {
            const content = document.getElementById(id);
            const arrow = document.getElementById('arrow-' + id);
            
            if (!content) return;
            
            const isHidden = content.classList.contains('hidden');
            
            // Close other accordions to maintain neat sidebar layout
            document.querySelectorAll('.accordion-content').forEach(el => {
                if (el.id !== id) {
                    el.classList.add('hidden');
                    const otherArrow = document.getElementById('arrow-' + el.id);
                    if (otherArrow) otherArrow.classList.remove('rotate-180');
                }
            });

            if (isHidden) {
                content.classList.remove('hidden');
                if (arrow) arrow.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                if (arrow) arrow.classList.remove('rotate-180');
            }
        }

        // Switch device preview sizing representation
        function setPreviewDevice(device) {
            const frame = document.getElementById('preview-frame-wrapper');
            const btnDesktop = document.getElementById('btn-device-desktop');
            const btnMobile = document.getElementById('btn-device-mobile');
            
            if (!frame) return;

            if (device === 'mobile') {
                frame.style.maxWidth = '375px';
                frame.style.height = '667px';
                frame.classList.add('border-8', 'border-slate-950', 'rounded-[2rem]', 'shadow-slate-950/50');
                
                if (btnMobile) {
                    btnMobile.classList.add('bg-slate-800', 'text-white', 'border', 'border-slate-700/50', 'shadow-sm');
                    btnMobile.classList.remove('text-slate-400');
                }
                if (btnDesktop) {
                    btnDesktop.classList.remove('bg-slate-800', 'text-white', 'border', 'border-slate-700/50', 'shadow-sm');
                    btnDesktop.classList.add('text-slate-400');
                }
            } else {
                frame.style.maxWidth = '100%';
                frame.style.height = 'calc(100vh - 165px)';
                frame.classList.remove('border-8', 'border-slate-950', 'rounded-[2rem]', 'shadow-slate-950/50');
                
                if (btnDesktop) {
                    btnDesktop.classList.add('bg-slate-800', 'text-white', 'border', 'border-slate-700/50', 'shadow-sm');
                    btnDesktop.classList.remove('text-slate-400');
                }
                if (btnMobile) {
                    btnMobile.classList.remove('bg-slate-800', 'text-white', 'border', 'border-slate-700/50', 'shadow-sm');
                    btnMobile.classList.add('text-slate-400');
                }
            }
        }

        // Force Iframe reload
        function refreshPreview() {
            const iframe = document.getElementById('preview-iframe');
            if (iframe) iframe.src = iframe.src;
        }

        // Update customized file selection labels
        function updateFileName(input) {
            const name = input.name;
            const label = document.getElementById('file-' + name + '-label');
            if (!label) return;

            if (input.files && input.files[0]) {
                label.innerText = 'File: ' + (input.files[0].name.length > 20 ? input.files[0].name.substring(0, 18) + '...' : input.files[0].name);
                label.classList.add('text-indigo-600');
            } else {
                label.innerText = 'Choose File';
                label.classList.remove('text-indigo-600');
            }
        }

        function deleteHeroImage(path) {
            if (confirm('Are you sure you want to delete this image?')) {
                // Find element
                const el = document.querySelector(`[data-image-path="${CSS.escape(path)}"]`);
                if (el) {
                    el.remove();
                }
                // Update remaining images hidden input
                const remainingInput = document.getElementById('remaining_hero_images');
                if (remainingInput) {
                    let remaining = JSON.parse(remainingInput.value || '[]');
                    remaining = remaining.filter(item => item !== path);
                    remainingInput.value = JSON.stringify(remaining);
                }
            }
        }

        function updateHeroFilesLabel(input) {
            const label = document.getElementById('file-hero_images-label');
            if (!label) return;
            if (input.files && input.files.length > 0) {
                label.innerText = `${input.files.length} file(s) selected for upload`;
                label.classList.add('text-indigo-600');
            } else {
                label.innerText = "Upload One or More Banner Images";
                label.classList.remove('text-indigo-600');
            }
        }

        // Update read-only text with hex color input
        function updateColorValue(input) {
            const textVal = document.getElementById('val-' + input.name);
            if (textVal) textVal.value = input.value;
        }

        // Conditionally display hero layouts
        function toggleHeroFields() {
            const layoutSelect = document.querySelector('select[name="hero_layout_type"]');
            if (!layoutSelect) return;

            const layout = layoutSelect.value;
            const blockColor = document.getElementById('hero-block-color');
            const blockImage = document.getElementById('hero-block-image');
            const blockCode = document.getElementById('hero-block-code');
            const blockTexts = document.getElementById('hero-text-controls');

            // Reset
            if (blockColor) blockColor.classList.add('hidden');
            if (blockImage) blockImage.classList.add('hidden');
            if (blockCode) blockCode.classList.add('hidden');
            if (blockTexts) blockTexts.classList.remove('hidden');

            if (layout === 'color') {
                if (blockColor) blockColor.classList.remove('hidden');
            } else if (layout === 'image') {
                if (blockImage) blockImage.classList.remove('hidden');
            } else if (layout === 'custom_code') {
                if (blockCode) blockCode.classList.remove('hidden');
                if (blockTexts) blockTexts.classList.add('hidden'); // Code layouts override default titles
            }
        }

        // Conditionally display shipping configurations based on mode
        function toggleShippingFields() {
            const mode = document.getElementById('shipping_mode').value;
            const flatFeeWrapper = document.getElementById('shipping_flat_fee_wrapper');
            const thresholdWrapper = document.getElementById('shipping_threshold_wrapper');

            if (!flatFeeWrapper || !thresholdWrapper) return;

            if (mode === 'free') {
                flatFeeWrapper.classList.add('hidden');
                thresholdWrapper.classList.add('hidden');
            } else if (mode === 'flat') {
                flatFeeWrapper.classList.remove('hidden');
                thresholdWrapper.classList.add('hidden');
            } else if (mode === 'conditional') {
                flatFeeWrapper.classList.remove('hidden');
                thresholdWrapper.classList.remove('hidden');
            }
        }

        // Conditionally display mobile/bank payment account fields
        function togglePaymentFieldBlock(type) {
            const checkbox = document.getElementById('payment_' + type + '_active');
            const block = document.getElementById('payment_' + type + '_fields');
            if (checkbox && block) {
                if (checkbox.checked) {
                    block.classList.remove('hidden');
                } else {
                    block.classList.add('hidden');
                }
            }
        }

        // Section Modals
        function openSectionModal() {
            const modal = document.getElementById('sectionModal');
            if (modal) modal.classList.remove('hidden');
        }
        function closeSectionModal() {
            const modal = document.getElementById('sectionModal');
            if (modal) modal.classList.add('hidden');
        }

        // Load Add Section Form Template in Sidebar
        function selectSection(type, title) {
            closeSectionModal();
            
            // Hide section list, show editor form
            const listCont = document.getElementById('sections-list-container');
            const formEl = document.getElementById('dynamic_section_form');
            if (listCont) listCont.classList.add('hidden');
            if (formEl) formEl.classList.remove('hidden');
            
            document.getElementById('selected_section_title').innerHTML = `Add: ${title}`;
            document.getElementById('hidden_section_type').value = type;
            document.getElementById('hidden_section_id').value = ''; 
            document.getElementById('auto_section_title').value = title;
            
            // Reset Spacing selectors
            const pt = document.getElementById('spacing_pt');
            const pb = document.getElementById('spacing_pb');
            if (pt) pt.value = 'pt-16';
            if (pb) pb.value = 'pb-16';

            // Toggle specific fields block
            document.querySelectorAll('.section-fields').forEach(el => el.classList.add('hidden'));
            const fieldBlock = document.getElementById('fields_' + type);
            if (fieldBlock) fieldBlock.classList.remove('hidden');
            
            if (type === 'faq') {
                const faqContainer = document.getElementById('faq_dynamic_list');
                if (faqContainer) {
                    faqContainer.innerHTML = '';
                    addFaqField();
                }
            }

            // Scroll control panel to top
            document.getElementById('sidebar-scroll-container').scrollTop = 0;
            
            // Set localStorage editing section state
            localStorage.setItem('editingSectionId', 'new');
            localStorage.setItem('editingSectionType', type);
            localStorage.setItem('editingSectionTitle', title);
        }

        // Cancel editing and return to list view
        function cancelSection() { 
            const listCont = document.getElementById('sections-list-container');
            const formEl = document.getElementById('dynamic_section_form');
            if (listCont) listCont.classList.remove('hidden');
            if (formEl) formEl.classList.add('hidden');
            
            document.getElementById('hidden_section_id').value = ''; 
            localStorage.removeItem('editingSectionId');
            localStorage.removeItem('editingSectionType');
            localStorage.removeItem('editingSectionTitle');
            localStorage.removeItem('editingSectionSettings');
        }

        // Load Edit Section Form Template in Sidebar
        function editSection(id, type, title, settingsBase64, contentBase64 = '') {
            closeSectionModal();
            
            const listCont = document.getElementById('sections-list-container');
            const formEl = document.getElementById('dynamic_section_form');
            if (listCont) listCont.classList.add('hidden');
            if (formEl) formEl.classList.remove('hidden');
            
            document.getElementById('selected_section_title').innerHTML = `Edit: ${title}`;
            document.getElementById('hidden_section_type').value = type;
            document.getElementById('hidden_section_id').value = id; 
            document.getElementById('auto_section_title').value = title;

            // Toggle fields block
            document.querySelectorAll('.section-fields').forEach(el => el.classList.add('hidden'));
            const fieldBlock = document.getElementById('fields_' + type);
            if (fieldBlock) fieldBlock.classList.remove('hidden');

            let s = {};
            try {
                s = JSON.parse(atob(settingsBase64) || '{}');
            } catch(e) {
                console.error("Failed to parse settings JSON", e);
            }

            // Spacing values
            const pt = document.getElementById('spacing_pt');
            const pb = document.getElementById('spacing_pb');
            if (pt) pt.value = s.pt || 'pt-16';
            if (pb) pb.value = s.pb || 'pb-16';

            let content = contentBase64 ? atob(contentBase64) : '';

            // Populate specific fields
            if (type === 'custom_code') {
                const ta = document.querySelector('textarea[name="section_content"]');
                if (ta) ta.value = content;
            } 
            else if (type === 'discount_banner') {
                const badge = document.querySelector('input[name="banner_badge"]');
                const heading = document.querySelector('input[name="banner_heading"]');
                const highlight = document.querySelector('input[name="banner_highlight"]');
                const desc = document.querySelector('textarea[name="banner_description"]');
                const btn = document.querySelector('input[name="banner_btn_text"]');

                if (badge) badge.value = s.badge || '';
                if (heading) heading.value = s.heading || '';
                if (highlight) highlight.value = s.highlight || '';
                if (desc) desc.value = s.description || '';
                if (btn) btn.value = s.btn_text || '';
            } 
            else if (type === 'image_with_text') {
                const layout = document.querySelector('select[name="iwt_layout"]');
                const heading = document.querySelector('input[name="iwt_heading"]');
                const txt = document.querySelector('textarea[name="iwt_text"]');
                const btnText = document.querySelector('input[name="iwt_btn_text"]');
                const btnLink = document.querySelector('input[name="iwt_btn_link"]');
                const fileLabel = document.getElementById('file-iwt_image-label');

                if (layout) layout.value = s.layout || 'image_left';
                if (heading) heading.value = s.heading || '';
                if (txt) txt.value = s.text || '';
                if (btnText) btnText.value = s.btn_text || '';
                if (btnLink) btnLink.value = s.btn_link || '#products';
                if (fileLabel && s.image) {
                    fileLabel.innerText = 'Image Uploaded: ' + s.image.split('/').pop();
                    fileLabel.classList.add('text-indigo-600');
                }
            } 
            else if (type === 'features_bar') {
                for (let i = 1; i <= 4; i++) {
                    const titleInput = document.querySelector(`input[name="f${i}_title"]`);
                    const fileLabel = document.getElementById(`file-f${i}_icon-label`);
                    if (titleInput) titleInput.value = s['f' + i] || '';
                    if (fileLabel && s[`f${i}_icon`]) {
                        fileLabel.innerText = 'Icon Uploaded';
                        fileLabel.classList.add('text-indigo-600');
                    }
                }
            } 
            else if (type === 'testimonials') {
                const heading = document.querySelector('input[name="testi_heading"]');
                if (heading) heading.value = s.heading || '';
                
                for (let i = 1; i <= 3; i++) {
                    const name = document.querySelector(`input[name="r${i}_name"]`);
                    const txt = document.querySelector(`textarea[name="r${i}_text"]`);
                    if (name) name.value = s[`r${i}_name`] || '';
                    if (txt) txt.value = s[`r${i}_text`] || '';
                }
            } 
            else if (type === 'faq') {
                const heading = document.querySelector('input[name="faq_heading"]');
                if (heading) heading.value = s.heading || '';
                
                const faqContainer = document.getElementById('faq_dynamic_list');
                if (faqContainer) {
                    faqContainer.innerHTML = '';
                    if (s.faqs && s.faqs.length > 0) {
                        s.faqs.forEach(faq => {
                            const item = document.createElement('div');
                            item.className = 'faq-item p-4 border border-indigo-100 rounded-xl bg-indigo-50/20 space-y-2 relative shadow-sm';
                            item.innerHTML = `
                                <button type="button" onclick="this.parentElement.remove()" class="absolute -top-2 -right-2 bg-white border border-rose-200 text-rose-500 font-extrabold w-6 h-6 rounded-full flex items-center justify-center hover:bg-rose-500 hover:text-white transition shadow-sm" title="Delete Question">×</button>
                                <input type="text" name="faq_q[]" value="${(faq.q || '').replace(/"/g, '&quot;')}" placeholder="Question (سوال) ?" class="w-full px-3 py-2 border border-slate-200 rounded-lg font-bold bg-white text-xs outline-none" required>
                                <textarea name="faq_a[]" rows="2" placeholder="Answer (جواب) ..." class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-white text-xs outline-none leading-normal" required>${faq.a || ''}</textarea>
                            `;
                            faqContainer.appendChild(item);
                        });
                    } else {
                        addFaqField();
                    }
                }
            }
            else if (type === 'featured_products') {
                const heading = document.querySelector('input[name="fp_heading"]');
                const count = document.querySelector('select[name="fp_count"]');
                if (heading) heading.value = s.heading || '';
                if (count) count.value = s.product_count || '4';
            }
            else if (type === 'video_banner') {
                const heading = document.querySelector('input[name="video_heading"]');
                const sub = document.querySelector('input[name="video_subheading"]');
                const url = document.querySelector('input[name="video_url"]');
                const btnText = document.querySelector('input[name="video_btn_text"]');
                const btnLink = document.querySelector('input[name="video_btn_link"]');
                if (heading) heading.value = s.heading || '';
                if (sub) sub.value = s.subheading || '';
                if (url) url.value = s.video_url || '';
                if (btnText) btnText.value = s.btn_text || '';
                if (btnLink) btnLink.value = s.btn_link || '';
            }
            else if (type === 'newsletter_form') {
                const heading = document.querySelector('input[name="news_heading"]');
                const sub = document.querySelector('input[name="news_subheading"]');
                const placeholder = document.querySelector('input[name="news_placeholder"]');
                const btnText = document.querySelector('input[name="news_btn_text"]');
                if (heading) heading.value = s.heading || '';
                if (sub) sub.value = s.subheading || '';
                if (placeholder) placeholder.value = s.placeholder || 'Enter your email';
                if (btnText) btnText.value = s.btn_text || 'Subscribe';
            }
            else if (type === 'rich_text') {
                const heading = document.querySelector('input[name="rt_heading"]');
                const txt = document.querySelector('textarea[name="rt_text"]');
                const btnText = document.querySelector('input[name="rt_btn_text"]');
                const btnLink = document.querySelector('input[name="rt_btn_link"]');
                if (heading) heading.value = s.heading || '';
                if (txt) txt.value = s.text || '';
                if (btnText) btnText.value = s.btn_text || '';
                if (btnLink) btnLink.value = s.btn_link || '';
            }
            
            // Set localStorage editing section state
            localStorage.setItem('editingSectionId', id);
            localStorage.setItem('editingSectionType', type);
            localStorage.setItem('editingSectionTitle', title);
            localStorage.setItem('editingSectionSettings', settingsBase64);
        }

        // Add FAQ dynamic Q/A rows
        function addFaqField() {
            const container = document.getElementById('faq_dynamic_list');
            if (!container) return;

            const item = document.createElement('div');
            item.className = 'faq-item p-4 border border-indigo-100 rounded-xl bg-indigo-50/20 space-y-2 relative shadow-sm';
            item.innerHTML = `
                <button type="button" onclick="this.parentElement.remove()" class="absolute -top-2 -right-2 bg-white border border-rose-200 text-rose-500 font-extrabold w-6 h-6 rounded-full flex items-center justify-center hover:bg-rose-500 hover:text-white transition shadow-sm" title="Delete Question">×</button>
                <input type="text" name="faq_q[]" placeholder="Question (سوال) ?" class="w-full px-3 py-2 border border-slate-200 rounded-lg font-bold bg-white text-xs outline-none" required>
                <textarea name="faq_a[]" rows="2" placeholder="Answer (جواب) ..." class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-white text-xs outline-none leading-normal" required></textarea>
            `;
            container.appendChild(item);
        }

        // Page State Persistence on DOM Load
        document.addEventListener('DOMContentLoaded', () => {
            // Restore Tab
            const savedTab = localStorage.getItem('activeTab') || 'global';
            switchTab(savedTab);

            // Restore Scroll position
            const savedScroll = localStorage.getItem('sidebarScrollPosition');
            if (savedScroll) {
                document.getElementById('sidebar-scroll-container').scrollTop = parseInt(savedScroll, 10);
            }

            // Bind Scroll listener
            document.getElementById('sidebar-scroll-container').addEventListener('scroll', (e) => {
                localStorage.setItem('sidebarScrollPosition', e.target.scrollTop);
            });

            // Initialize Hero Layout fields visibility
            toggleHeroFields();

            // Initialize Shipping and Payment fields visibility
            if (document.getElementById('shipping_mode')) {
                toggleShippingFields();
            }
            ['bank', 'easypaisa', 'jazzcash'].forEach(type => {
                if (document.getElementById('payment_' + type + '_active')) {
                    togglePaymentFieldBlock(type);
                }
            });

            // Intercept Global Settings submit to save state
            const gForm = document.getElementById('globalForm');
            if (gForm) {
                gForm.addEventListener('submit', () => {
                    localStorage.setItem('activeTab', 'global');
                    localStorage.removeItem('editingSectionId');
                    localStorage.removeItem('editingSectionType');
                    localStorage.removeItem('editingSectionTitle');
                    localStorage.removeItem('editingSectionSettings');
                });
            }

            // Intercept Section Form submit to save state
            const sForm = document.getElementById('dynamic_section_form');
            if (sForm) {
                sForm.addEventListener('submit', () => {
                    localStorage.setItem('activeTab', 'sections');
                    const sectionId = document.getElementById('hidden_section_id').value;
                    const sectionType = document.getElementById('hidden_section_type').value;
                    const sectionTitle = document.getElementById('auto_section_title').value;

                    if (sectionId) {
                        localStorage.setItem('editingSectionId', sectionId);
                    } else {
                        // For new sections, we let the customizer return to the main sections list
                        localStorage.removeItem('editingSectionId');
                    }
                    localStorage.setItem('editingSectionType', sectionType);
                    localStorage.setItem('editingSectionTitle', sectionTitle);
                });
            }

            // Click programmatically if we were editing a section prior to reload
            if (savedTab === 'sections') {
                const savedSectionId = localStorage.getItem('editingSectionId');
                if (savedSectionId && savedSectionId !== 'new') {
                    const el = document.getElementById('section-item-' + savedSectionId);
                    if (el) {
                        // Click with delay to allow rendering
                        setTimeout(() => { el.click(); }, 150);
                    }
                }
            }

            // Render navigation lists
            renderNavigation('header');
            renderNavigation('quick');
            renderNavigation('policies');

            // Restore active Page editing state if page tab is re-selected
            if (savedTab === 'pages') {
                const savedPageId = localStorage.getItem('editingPageId');
                if (savedPageId === 'new') {
                    showAddPage();
                } else if (savedPageId) {
                    localStorage.removeItem('editingPageId');
                }
            }

            // Initialize Quill Editor for dynamic page content
            if (document.getElementById('page-content-editor')) {
                pageQuill = new Quill('#page-content-editor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'color': [] }, { 'background': [] }],
                            [{ 'align': [] }],
                            ['link', 'image', 'video'],
                            ['blockquote', 'code-block'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            ['clean']
                        ]
                    }
                });
            }

            // Intercept Page Form submit to save tab state
            const pForm = document.getElementById('dynamic_page_form');
            if (pForm) {
                pForm.addEventListener('submit', () => {
                    if (pageQuill) {
                        document.getElementById('page_content').value = pageQuill.root.innerHTML;
                    }
                    localStorage.setItem('activeTab', 'pages');
                    localStorage.removeItem('editingPageId');
                });
            }
        });

        // ================= DYNAMIC PAGES JS CONTROLLER =================
        const pageTemplates = {
            blank: '',
            'contact-split': `<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 my-8 items-stretch">
    <!-- Left Column: Contact details -->
    <div class="lg:col-span-5 bg-gradient-to-br from-slate-900 to-indigo-950 text-white rounded-3xl p-8 md:p-10 flex flex-col justify-between shadow-2xl relative overflow-hidden border border-indigo-900/40">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_30%,rgba(99,102,241,0.15),transparent)] pointer-events-none"></div>
        <div class="relative z-10 space-y-8">
            <div>
                <span class="text-xs font-black tracking-widest text-indigo-400 uppercase">Contact Information</span>
                <h2 class="text-3xl font-black mt-2 leading-tight">Get in Touch</h2>
                <p class="text-xs text-slate-400 mt-3 font-medium leading-relaxed">Have questions or feedback? We would love to hear from you. Reach out, and our team will get back to you shortly.</p>
            </div>
            
            <div class="space-y-6">
                <!-- Address -->
                <div class="flex items-start gap-4">
                    <div class="bg-indigo-900/50 p-3 rounded-2xl border border-indigo-500/20 text-indigo-400 mt-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-slate-200">Our Address</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">123 Wellness Way, Suite 400<br>London, UK</p>
                    </div>
                </div>
                <!-- Phone -->
                <div class="flex items-start gap-4">
                    <div class="bg-indigo-900/50 p-3 rounded-2xl border border-indigo-500/20 text-indigo-400 mt-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-slate-200">Call Us</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">+1 (555) 123-4567<br>Mon - Fri, 9am - 6pm</p>
                    </div>
                </div>
                <!-- Email -->
                <div class="flex items-start gap-4">
                    <div class="bg-indigo-900/50 p-3 rounded-2xl border border-indigo-500/20 text-indigo-400 mt-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-slate-200">Email Address</h4>
                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">support@yourstore.com<br>sales@yourstore.com</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="relative z-10 mt-12 pt-6 border-t border-slate-800 flex items-center gap-4 text-slate-400 text-xs font-bold">
            <span>Follow Us:</span>
            <a href="#" class="hover:text-indigo-400 transition">Facebook</a>
            <span>&bull;</span>
            <a href="#" class="hover:text-indigo-400 transition">Twitter</a>
            <span>&bull;</span>
            <a href="#" class="hover:text-indigo-400 transition">Instagram</a>
        </div>
    </div>

    <!-- Right Column: Contact Form -->
    <div class="lg:col-span-7 bg-white rounded-3xl p-8 md:p-10 shadow-xl border border-slate-100 flex flex-col justify-center">
        <div>
            <h3 class="text-2xl font-black text-slate-800 leading-tight">Send a Message</h3>
            <p class="text-xs text-slate-405 mt-1.5 font-semibold">We typically reply within a business day.</p>
        </div>

        <form id="contact-us-form" class="mt-8 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-450 tracking-wider mb-2">Your Name</label>
                    <input type="text" name="name" required placeholder="John Doe" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-450 tracking-wider mb-2">Email Address</label>
                    <input type="email" name="email" required placeholder="john@example.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase text-slate-455 tracking-wider mb-2">Subject</label>
                <input type="text" name="subject" placeholder="What is this about?" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase text-slate-455 tracking-wider mb-2">Message</label>
                <textarea name="message" rows="5" required placeholder="Write your message here..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150"></textarea>
            </div>
            <div class="pt-2">
                <button type="submit" class="btn-primary-custom w-full font-black py-4 px-6 rounded-xl hover:opacity-95 transition shadow-lg flex items-center justify-center gap-2 text-xs uppercase tracking-wider">
                    <span>Send Message</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const initForm = function() {
        const form = document.getElementById('contact-us-form');
        if (!form) return;
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = \`
                <svg class="animate-spin h-5 w-5 text-current inline mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg> Sending...
            \`;

            const data = {
                name: form.querySelector('[name="name"]').value,
                email: form.querySelector('[name="email"]').value,
                subject: form.querySelector('[name="subject"]') ? form.querySelector('[name="subject"]').value : 'Contact Page Query',
                message: form.querySelector('[name="message"]').value
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('/contact-submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                if (res.success) {
                    showSuccessToast(res.message);
                    form.reset();
                } else {
                    alert(res.message || 'An error occurred. Please try again.');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                alert('Something went wrong. Please check your connection and try again.');
            });
        });

        function showSuccessToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-5 right-5 bg-slate-900 border border-slate-800 text-white rounded-2xl shadow-2xl p-5 flex items-start gap-3.5 max-w-sm transform translate-y-10 opacity-0 transition-all duration-300 z-[9999]';
            toast.innerHTML = \`
                <div class="bg-green-500/10 text-green-400 p-2 rounded-xl border border-green-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1">
                    <h4 class="font-extrabold text-sm text-slate-100">Message Sent!</h4>
                    <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">\${message}</p>
                </div>
            \`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            }, 10);
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initForm);
    } else {
        initForm();
    }
})();
<\/script>`,
            'contact-card': `<div class="max-w-2xl mx-auto my-8">
    <div class="bg-white rounded-3xl border border-slate-100 shadow-2xl p-8 md:p-12 relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
        <div class="text-center">
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Contact Our Team</h2>
            <p class="text-xs text-slate-450 mt-2.5 font-medium max-w-md mx-auto leading-relaxed">We are here to answer your questions and help you. Just send us a line and we'll reply as fast as possible.</p>
        </div>

        <form id="contact-us-form" class="mt-10 space-y-5">
            <div>
                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">Your Name</label>
                <input type="text" name="name" required placeholder="John Doe" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">Email Address</label>
                    <input type="email" name="email" required placeholder="john@example.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">Subject</label>
                    <input type="text" name="subject" placeholder="Help with my order" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">Message</label>
                <textarea name="message" rows="5" required placeholder="How can we help you?" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150"></textarea>
            </div>
            <div class="pt-2">
                <button type="submit" class="btn-primary-custom w-full font-black py-4 px-6 rounded-xl hover:opacity-95 transition shadow-lg flex items-center justify-center gap-2 text-xs uppercase tracking-wider">
                    <span>Send Message</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>

        <div class="grid grid-cols-3 gap-2 mt-10 pt-8 border-t border-slate-100 text-center">
            <div>
                <span class="block text-[9px] uppercase font-black text-slate-400">Call Us</span>
                <span class="block text-xs font-bold text-slate-700 mt-1">+1 (555) 123-45</span>
            </div>
            <div>
                <span class="block text-[9px] uppercase font-black text-slate-400">Email Us</span>
                <span class="block text-xs font-bold text-slate-700 mt-1">care@store.com</span>
            </div>
            <div>
                <span class="block text-[9px] uppercase font-black text-slate-400">Visit Us</span>
                <span class="block text-xs font-bold text-slate-700 mt-1">London, UK</span>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const initForm = function() {
        const form = document.getElementById('contact-us-form');
        if (!form) return;
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = \`
                <svg class="animate-spin h-5 w-5 text-current inline mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg> Sending...
            \`;

            const data = {
                name: form.querySelector('[name="name"]').value,
                email: form.querySelector('[name="email"]').value,
                subject: form.querySelector('[name="subject"]') ? form.querySelector('[name="subject"]').value : 'Contact Page Query',
                message: form.querySelector('[name="message"]').value
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('/contact-submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                if (res.success) {
                    showSuccessToast(res.message);
                    form.reset();
                } else {
                    alert(res.message || 'An error occurred. Please try again.');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                alert('Something went wrong. Please check your connection and try again.');
            });
        });

        function showSuccessToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-5 right-5 bg-slate-900 border border-slate-800 text-white rounded-2xl shadow-2xl p-5 flex items-start gap-3.5 max-w-sm transform translate-y-10 opacity-0 transition-all duration-300 z-[9999]';
            toast.innerHTML = \`
                <div class="bg-green-500/10 text-green-400 p-2 rounded-xl border border-green-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1">
                    <h4 class="font-extrabold text-sm text-slate-100">Message Sent!</h4>
                    <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">\${message}</p>
                </div>
            \`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            }, 10);
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initForm);
    } else {
        initForm();
    }
})();
<\/script>`,
            'contact-grid': `<div class="space-y-10 my-8">
    <!-- Top 3 Grid Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card 1: Address -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-lg text-center flex flex-col items-center">
            <div class="bg-indigo-50 p-3 rounded-full text-indigo-600 mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <h3 class="font-extrabold text-sm text-slate-800">Visit Store</h3>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed font-semibold">123 Wellness Way<br>London, UK</p>
        </div>
        <!-- Card 2: Phone -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-lg text-center flex flex-col items-center">
            <div class="bg-indigo-50 p-3 rounded-full text-indigo-600 mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
            </div>
            <h3 class="font-extrabold text-sm text-slate-800">Call Support</h3>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed font-semibold">+1 (555) 123-4567<br>Mon-Fri, 9am-6pm</p>
        </div>
        <!-- Card 3: Email -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-lg text-center flex flex-col items-center">
            <div class="bg-indigo-50 p-3 rounded-full text-indigo-600 mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
            <h3 class="font-extrabold text-sm text-slate-800">Email Us</h3>
            <p class="text-xs text-slate-400 mt-2 leading-relaxed font-semibold">care@store.com<br>info@store.com</p>
        </div>
    </div>

    <!-- Message Form Card below -->
    <div class="bg-white border border-slate-100 rounded-3xl p-8 md:p-10 shadow-xl">
        <h3 class="text-2xl font-black text-slate-800 tracking-tight">Need help? Drop us a line!</h3>
        <p class="text-xs text-slate-400 mt-1 font-semibold">We answer all messages within 24 hours.</p>

        <form id="contact-us-form" class="mt-8 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">Your Name</label>
                    <input type="text" name="name" required placeholder="John Doe" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">Email Address</label>
                    <input type="email" name="email" required placeholder="john@example.com" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">Subject</label>
                <input type="text" name="subject" placeholder="Feedback/Questions" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150">
            </div>
            <div>
                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-wider mb-2">Message</label>
                <textarea name="message" rows="5" required placeholder="Write your feedback/questions here..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition duration-150"></textarea>
            </div>
            <div class="pt-2">
                <button type="submit" class="btn-primary-custom w-full font-black py-4 px-6 rounded-xl hover:opacity-95 transition shadow-lg flex items-center justify-center gap-2 text-xs uppercase tracking-wider">
                    <span>Send Message</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    const initForm = function() {
        const form = document.getElementById('contact-us-form');
        if (!form) return;
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = \`
                <svg class="animate-spin h-5 w-5 text-current inline mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg> Sending...
            \`;

            const data = {
                name: form.querySelector('[name="name"]').value,
                email: form.querySelector('[name="email"]').value,
                subject: form.querySelector('[name="subject"]') ? form.querySelector('[name="subject"]').value : 'Contact Page Query',
                message: form.querySelector('[name="message"]').value
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch('/contact-submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(res => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                if (res.success) {
                    showSuccessToast(res.message);
                    form.reset();
                } else {
                    alert(res.message || 'An error occurred. Please try again.');
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                alert('Something went wrong. Please check your connection and try again.');
            });
        });

        function showSuccessToast(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-5 right-5 bg-slate-900 border border-slate-800 text-white rounded-2xl shadow-2xl p-5 flex items-start gap-3.5 max-w-sm transform translate-y-10 opacity-0 transition-all duration-300 z-[9999]';
            toast.innerHTML = \`
                <div class="bg-green-500/10 text-green-400 p-2 rounded-xl border border-green-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div class="flex-1">
                    <h4 class="font-extrabold text-sm text-slate-100">Message Sent!</h4>
                    <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">\text{\${message}}</p>
                </div>
            \`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            }, 10);
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }
    };
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initForm);
    } else {
        initForm();
    }
})();
<\/script>`
        };

        function applyPageTemplate(type) {
            // Update active buttons styling
            document.querySelectorAll('[id^="tpl-"]').forEach(btn => {
                btn.classList.remove('border-indigo-500', 'bg-indigo-50/25', 'active-template');
                btn.classList.add('border-slate-200');
            });
            const activeBtn = document.getElementById('tpl-' + type);
            if (activeBtn) {
                activeBtn.classList.remove('border-slate-200');
                activeBtn.classList.add('border-indigo-500', 'bg-indigo-50/25', 'active-template');
            }

            if (type === 'blank') {
                document.getElementById('page_title').value = '';
                document.getElementById('page_slug').value = '';
                document.getElementById('page_content').value = '';
                if (typeof pageQuill !== 'undefined') pageQuill.root.innerHTML = '';
                document.getElementById('page_add_to_header').checked = false;
            } else {
                document.getElementById('page_title').value = 'Contact Us';
                document.getElementById('page_slug').value = 'contact-us';
                const tplContent = pageTemplates[type];
                document.getElementById('page_content').value = tplContent;
                if (typeof pageQuill !== 'undefined') pageQuill.root.innerHTML = tplContent;
                document.getElementById('page_add_to_header').checked = true;
            }
        }

        function showAddPage() {
            document.getElementById('pages-list-container').classList.add('hidden');
            document.getElementById('dynamic_page_form').classList.remove('hidden');
            
            document.getElementById('page_editor_title').innerText = 'Create New Page';
            document.getElementById('page_id_hidden').value = '';
            
            // Apply blank template by default
            applyPageTemplate('blank');
            document.getElementById('page_is_active').checked = true;
            document.getElementById('page_is_policy').checked = false;

            localStorage.setItem('editingPageId', 'new');
        }

        function editPage(id, title, slug, contentBase64, isActive, isPolicy) {
            document.getElementById('pages-list-container').classList.add('hidden');
            document.getElementById('dynamic_page_form').classList.remove('hidden');
            
            document.getElementById('page_editor_title').innerText = 'Edit Page: ' + title;
            document.getElementById('page_id_hidden').value = id;
            document.getElementById('page_title').value = title;
            document.getElementById('page_slug').value = slug;

            // Remove active highlights since they are editing custom page
            document.querySelectorAll('[id^="tpl-"]').forEach(btn => {
                btn.classList.remove('border-indigo-500', 'bg-indigo-50/25', 'active-template');
                btn.classList.add('border-slate-200');
            });
            document.getElementById('page_add_to_header').checked = false;

            let plainContent = '';
            try {
                plainContent = atob(contentBase64);
            } catch(e) {
                plainContent = contentBase64;
            }
            document.getElementById('page_content').value = plainContent;
            if (typeof pageQuill !== 'undefined' && pageQuill) {
                pageQuill.root.innerHTML = plainContent;
            }
            document.getElementById('page_is_active').checked = isActive;
            document.getElementById('page_is_policy').checked = isPolicy;

            localStorage.setItem('editingPageId', id);
        }

        function cancelPageEdit() {
            document.getElementById('pages-list-container').classList.remove('hidden');
            document.getElementById('dynamic_page_form').classList.add('hidden');
            
            localStorage.removeItem('editingPageId');
        }

        function autoGenerateSlug(val) {
            const pageId = document.getElementById('page_id_hidden').value;
            if (!pageId) {
                document.getElementById('page_slug').value = val
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
            }
        }

        // ================= NAVIGATION BUILDER JS CONTROLLER =================
        let headerMenuArray = {!! json_encode(is_null($settings->header_menu) ? [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Shop', 'url' => '/collection']
        ] : $settings->header_menu) !!};
        let footerQuickArray = {!! json_encode(is_null($settings->footer_quick_links) ? [
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Shop', 'url' => '/collection']
        ] : $settings->footer_quick_links) !!};
        let footerPoliciesArray = {!! json_encode(is_null($settings->footer_policies_links) ? [
            ['label' => 'Privacy Policy', 'url' => '#'],
            ['label' => 'Refund Policy', 'url' => '#']
        ] : $settings->footer_policies_links) !!};

        function toggleLinkInput(menuKey) {
            const typeSelect = document.getElementById(`add-${menuKey}-type`);
            const pageWrapper = document.getElementById(`add-${menuKey}-page-wrapper`);
            const customWrapper = document.getElementById(`add-${menuKey}-custom-wrapper`);

            if (!typeSelect) return;
            const val = typeSelect.value;

            pageWrapper.classList.add('hidden');
            customWrapper.classList.add('hidden');

            if (val === 'page') {
                pageWrapper.classList.remove('hidden');
            } else if (val === 'custom') {
                customWrapper.classList.remove('hidden');
            }
        }

        function renderNavigation(menuKey) {
            let arr = [];
            if (menuKey === 'header') arr = headerMenuArray;
            else if (menuKey === 'quick') arr = footerQuickArray;
            else if (menuKey === 'policies') arr = footerPoliciesArray;

            const container = document.getElementById(`menu-items-${menuKey}`);
            if (!container) return;
            container.innerHTML = '';

            if (!arr || arr.length === 0) {
                container.innerHTML = `<p class="text-[10px] text-slate-400 text-center py-4 bg-slate-50 rounded-xl border border-dashed border-slate-200">No links added to this menu</p>`;
                return;
            }

            arr.forEach((item, index) => {
                const card = document.createElement('div');
                card.className = 'flex items-center justify-between bg-white border border-slate-150 p-3 rounded-xl shadow-sm hover:border-indigo-400 transition text-[11px] font-semibold text-slate-700';
                card.innerHTML = `
                    <div class="truncate max-w-[200px]">
                        <span class="font-extrabold text-slate-800">${item.label}</span>
                        <span class="block text-[8px] font-mono text-slate-400 truncate mt-0.5">${item.url}</span>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <button type="button" onclick="moveNavigationItem('${menuKey}', ${index}, -1)" ${index === 0 ? 'disabled' : ''} class="w-6 h-6 flex items-center justify-center bg-slate-50 border border-slate-200 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed rounded-lg transition text-slate-500 font-bold" title="Move Up">↑</button>
                        <button type="button" onclick="moveNavigationItem('${menuKey}', ${index}, 1)" ${index === arr.length - 1 ? 'disabled' : ''} class="w-6 h-6 flex items-center justify-center bg-slate-50 border border-slate-200 hover:bg-slate-100 disabled:opacity-30 disabled:cursor-not-allowed rounded-lg transition text-slate-500 font-bold" title="Move Down">↓</button>
                        <button type="button" onclick="deleteNavigationItem('${menuKey}', ${index})" class="w-6 h-6 flex items-center justify-center bg-rose-50 border border-rose-100 hover:bg-rose-100 text-rose-500 rounded-lg transition font-extrabold" title="Remove Link">×</button>
                    </div>
                `;
                container.appendChild(card);
            });
        }

        function addNavigationItem(menuKey) {
            const labelInput = document.getElementById(`add-${menuKey}-label`);
            const typeSelect = document.getElementById(`add-${menuKey}-type`);
            
            if (!labelInput || !typeSelect) return;
            const label = labelInput.value.trim();
            const type = typeSelect.value;

            if (label === '') {
                alert('Please enter a link label!');
                return;
            }

            let url = '/';
            if (type === 'home') url = '/';
            else if (type === 'shop') url = '/#products';
            else if (type === 'page') {
                const pageSelect = document.getElementById(`add-${menuKey}-page`);
                url = pageSelect.value;
            } else if (type === 'custom') {
                const customInput = document.getElementById(`add-${menuKey}-custom`);
                url = customInput.value.trim();
                if (url === '') {
                    alert('Please enter a custom URL!');
                    return;
                }
            }

            const item = { label: label, url: url };

            if (menuKey === 'header') headerMenuArray.push(item);
            else if (menuKey === 'quick') footerQuickArray.push(item);
            else if (menuKey === 'policies') footerPoliciesArray.push(item);

            labelInput.value = '';
            typeSelect.value = 'home';
            toggleLinkInput(menuKey);

            renderNavigation(menuKey);
        }

        function deleteNavigationItem(menuKey, index) {
            if (menuKey === 'header') headerMenuArray.splice(index, 1);
            else if (menuKey === 'quick') footerQuickArray.splice(index, 1);
            else if (menuKey === 'policies') footerPoliciesArray.splice(index, 1);

            renderNavigation(menuKey);
        }

        function moveNavigationItem(menuKey, index, direction) {
            let arr = [];
            if (menuKey === 'header') arr = headerMenuArray;
            else if (menuKey === 'quick') arr = footerQuickArray;
            else if (menuKey === 'policies') arr = footerPoliciesArray;

            const targetIndex = index + direction;
            if (targetIndex < 0 || targetIndex >= arr.length) return;

            const temp = arr[index];
            arr[index] = arr[targetIndex];
            arr[targetIndex] = temp;

            renderNavigation(menuKey);
        }

        function prepareNavigationSubmit() {
            document.getElementById('header_menu_json').value = JSON.stringify(headerMenuArray);
            document.getElementById('footer_quick_links_json').value = JSON.stringify(footerQuickArray);
            document.getElementById('footer_policies_links_json').value = JSON.stringify(footerPoliciesArray);

            localStorage.setItem('activeTab', 'navigation');
        }


    </script>
</body>
</html>