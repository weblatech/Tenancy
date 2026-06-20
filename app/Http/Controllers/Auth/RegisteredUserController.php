<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. ڈیٹا چیک کریں
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'store_domain' => ['required', 'string', 'alpha_dash', 'unique:tenants,id', 'max:50'],
        ]);

        // 2. پہلے دکان (Tenant) بنائیں
        $tenantId = strtolower($request->store_domain);
        $tenant = Tenant::create([
            'id' => $tenantId,
            'name' => ucfirst($tenantId) . ' Store',
        ]);
        $tenant->domains()->create(['domain' => $tenantId . '.localhost']);

        // 3. اب یوزر بنائیں اور اس کے ساتھ دکان کو جوڑ دیں
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenantId, // 👈 یہ لائن دکان اور یوزر کو لنک کر رہی ہے
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}