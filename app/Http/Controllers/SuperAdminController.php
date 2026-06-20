<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $totalStores = Tenant::count();
        $activeStores = Tenant::where('subscription_status', 'active')->count();
        $expiredStores = Tenant::where('subscription_status', 'expired')->count();
        $suspendedStores = Tenant::where('subscription_status', 'suspended')->count();
        $totalMerchants = User::whereNotNull('tenant_id')->count();

        // Aggregated database stats
        $totalProducts = 0;
        $totalOrders = 0;
        foreach (Tenant::all() as $tenant) {
            try {
                tenancy()->initialize($tenant);
                $totalProducts += \App\Models\Product::count();
                $totalOrders += \App\Models\Order::count();
                tenancy()->end();
            } catch (\Exception $e) {
                // Ignore fallback
            }
        }

        $recentStores = Tenant::latest()->take(5)->get();

        return view('super-admin.dashboard', compact(
            'totalStores',
            'activeStores',
            'expiredStores',
            'suspendedStores',
            'totalMerchants',
            'totalProducts',
            'totalOrders',
            'recentStores'
        ));
    }

    public function tenants(Request $request)
    {
        $query = Tenant::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereRaw('LOWER(id) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        }

        if ($request->filled('plan')) {
            $query->where('subscription_plan', $request->plan);
        }

        if ($request->filled('status')) {
            $query->where('subscription_status', $request->status);
        }

        $tenants = $query->latest()->paginate(15)->withQueryString();

        return view('super-admin.tenants.index', compact('tenants'));
    }

    public function createTenantForm()
    {
        return view('super-admin.tenants.create');
    }

    public function createTenant(Request $request)
    {
        $request->validate([
            'store_id' => 'required|string|alpha_dash|unique:tenants,id|max:50',
            'store_name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:domains,domain',
            'merchant_name' => 'required|string|max:255',
            'merchant_email' => 'required|email|max:255',
            'merchant_password' => 'required|string|min:8',
            'subscription_plan' => 'required|string|in:free,basic,pro,enterprise',
            'subscription_status' => 'required|string|in:active,trial,expired,suspended',
            'subscription_ends_at' => 'nullable|date',
        ]);

        $tenantId = strtolower($request->store_id);

        // Create Tenant
        $tenant = Tenant::create([
            'id' => $tenantId,
            'name' => $request->store_name,
            'subscription_plan' => $request->subscription_plan,
            'subscription_status' => $request->subscription_status,
            'subscription_ends_at' => $request->subscription_ends_at ? Carbon::parse($request->subscription_ends_at) : null,
        ]);

        // Create Domain
        $tenant->domains()->create(['domain' => $request->domain]);

        // Associate user
        $user = User::where('email', $request->merchant_email)->first();
        if ($user) {
            $user->update(['tenant_id' => $tenantId]);
        } else {
            User::create([
                'name' => $request->merchant_name,
                'email' => $request->merchant_email,
                'password' => Hash::make($request->merchant_password),
                'tenant_id' => $tenantId,
            ]);
        }

        return redirect('/admin/tenants')->with('success', "Store '{$request->store_name}' created manually successfully! 🚀");
    }

    public function showTenant($id)
    {
        $tenant = Tenant::findOrFail($id);

        // Get tenant metrics
        $productCount = 0;
        $orderCount = 0;
        $recentOrders = [];

        try {
            tenancy()->initialize($tenant);
            $productCount = \App\Models\Product::count();
            $orderCount = \App\Models\Order::count();
            $recentOrders = \App\Models\Order::latest()->take(5)->get();
            tenancy()->end();
        } catch (\Exception $e) {
            // Ignore if database connection fails
        }

        $merchantUsers = User::where('tenant_id', $id)->get();

        return view('super-admin.tenants.show', compact(
            'tenant',
            'productCount',
            'orderCount',
            'recentOrders',
            'merchantUsers'
        ));
    }

    public function updateSubscription(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);

        $request->validate([
            'subscription_plan' => 'required|string|in:free,basic,pro,enterprise',
            'subscription_status' => 'required|string|in:active,trial,expired,suspended',
            'subscription_ends_at' => 'nullable|date',
        ]);

        $tenant->update([
            'subscription_plan' => $request->subscription_plan,
            'subscription_status' => $request->subscription_status,
            'subscription_ends_at' => $request->subscription_ends_at ? Carbon::parse($request->subscription_ends_at) : null,
        ]);

        return redirect()->back()->with('success', "Subscription parameters updated successfully for store '{$tenant->name}'! 💳");
    }

    public function toggleStatus(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);
        $newStatus = $tenant->subscription_status === 'suspended' ? 'active' : 'suspended';
        
        $tenant->update(['subscription_status' => $newStatus]);

        $msg = $newStatus === 'suspended' ? "Store '{$tenant->name}' has been suspended! 🛑" : "Store '{$tenant->name}' has been activated! 🟢";

        return redirect()->back()->with('success', $msg);
    }

    public function deleteTenant($id)
    {
        $tenant = Tenant::findOrFail($id);
        $name = $tenant->name;
        
        // Remove linked users' tenant_id link first
        User::where('tenant_id', $id)->update(['tenant_id' => null]);
        
        // Delete tenant (drops database and domains automatically via Stancl Tenancy)
        $tenant->delete();

        return redirect('/admin/tenants')->with('success', "Store '{$name}' has been completely deleted from the platform! 🗑️");
    }
}
