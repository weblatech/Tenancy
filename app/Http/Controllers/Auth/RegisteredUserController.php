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
    public function create(Request $request): View
    {
        $plan = $request->query('plan', 'starter');
        
        $plans = [
            'starter' => [
                'name' => 'Starter',
                'price' => 9,
                'features' => ['1 Staff Account', 'Up to 50 Products', 'Theme Customizer', 'Subdomain Hosting'],
            ],
            'growth' => [
                'name' => 'Growth',
                'price' => 29,
                'features' => ['5 Staff Accounts', 'Unlimited Products', 'Premium Customizer', 'Custom Domain', 'WhatsApp CRM'],
            ],
            'enterprise' => [
                'name' => 'Enterprise',
                'price' => 79,
                'features' => ['Unlimited Staff', 'Unlimited Products', 'Dedicated Theme Designer', 'Database Tuning', 'Priority 24/7 Support'],
            ],
        ];

        return view('auth.register', [
            'selectedPlan' => $plan,
            'plans' => $plans,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validPlans = ['starter', 'growth', 'enterprise'];
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'store_domain' => ['required', 'string', 'alpha_dash', 'unique:tenants,id', 'max:50'],
            'plan' => ['required', 'string', 'in:'.implode(',', $validPlans)],
        ]);

        $tenantId = strtolower($request->store_domain);
        $plan = $request->plan;
        
        $trialEndsAt = now()->addDays(14);
        
        $tenant = Tenant::create([
            'id' => $tenantId,
            'name' => ucfirst($tenantId) . ' Store',
            'subscription_plan' => $plan,
            'subscription_status' => 'trialing',
            'subscription_ends_at' => $trialEndsAt,
        ]);
        
        $centralDomains = explode(',', env('CENTRAL_DOMAINS', 'localhost'));
        $appHost = $centralDomains[0] ?? 'localhost';
        $tenant->domains()->create(['domain' => $tenantId . '.' . $appHost]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tenant_id' => $tenantId,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}