<x-guest-layout>
    @php
        $currentPlan = $selectedPlan ?? 'starter';
        $planData = $plans[$currentPlan] ?? $plans['starter'];
    @endphp

    <div class="mb-6 p-4 bg-brand-50 border border-brand-200 rounded-xl">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900">{{ $planData['name'] }} Plan</h3>
                <p class="text-sm text-gray-600">${{ $planData['price'] }}/month &bull; 14-day free trial</p>
            </div>
            <a href="{{ route('register') }}" class="text-sm text-brand-700 hover:text-brand-800 font-semibold">Change</a>
        </div>
        <ul class="mt-3 space-y-1.5">
            @foreach($planData['features'] as $feature)
                <li class="flex items-center gap-2 text-sm text-gray-700">
                    <svg class="w-4 h-4 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    {{ $feature }}
                </li>
            @endforeach
        </ul>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input type="hidden" name="plan" value="{{ $currentPlan }}">

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="store_domain" value="Store URL (e.g. my-shop)" />
            <div class="flex items-center">
                <x-text-input id="store_domain" class="block mt-1 w-full" type="text" name="store_domain" :value="old('store_domain')" required autocomplete="store_domain" />
                <span class="ml-2 text-gray-500">.{{ config('tenancy.central_domains', ['localhost'])[0] }}</span>
            </div>
            <x-input-error :messages="$errors->get('store_domain')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
