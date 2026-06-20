<x-app-layout>
    <!-- Custom styling & fonts for Shopify-like interface -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        .font-sans-profile {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .font-heading-profile {
            font-family: 'Outfit', sans-serif !important;
        }
    </style>

    <div class="py-12 bg-slate-50/50 min-h-[calc(100vh-65px)] font-sans-profile">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- Header section -->
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-heading-profile">
                    Account Settings
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Manage your merchant profile details, store configurations, and security settings.
                </p>
            </div>

            <!-- Profile Info Card -->
            <div class="p-6 sm:p-8 bg-white border border-slate-100 shadow-sm rounded-3xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Password Update Card -->
            <div class="p-6 sm:p-8 bg-white border border-slate-100 shadow-sm rounded-3xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Account Deletion Card -->
            <div class="p-6 sm:p-8 bg-white border border-rose-100 shadow-sm rounded-3xl bg-rose-50/10">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
