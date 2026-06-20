<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class SuperAdminDashboardTest extends TestCase
{
    use DatabaseMigrations;

    private string $tenantId;
    private Tenant $tenant;
    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        config(['tenancy.central_domains' => ['localhost']]);

        $this->tenantId = 'sa' . rand(100000, 999999);

        $this->tenant = Tenant::create([
            'id' => $this->tenantId,
            'name' => 'Super Admin Test Store',
            'subscription_plan' => 'pro',
            'subscription_status' => 'active',
        ]);

        $this->tenant->domains()->create(['domain' => $this->tenantId . '.localhost']);

        $this->superAdmin = User::create([
            'name' => 'Platform Owner',
            'email' => 'admin@munaa.test',
            'password' => bcrypt('password'),
            'is_super_admin' => true,
        ]);
    }

    protected function tearDown(): void
    {
        if (tenancy()->initialized) {
            tenancy()->end();
        }

        if (isset($this->tenant)) {
            $this->tenant->delete();
        }

        parent::tearDown();
    }

    /**
     * Verify that unauthenticated users cannot access the Super Admin dashboard.
     */
    public function test_guest_cannot_access_super_admin_dashboard(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    /**
     * Verify that regular (non-super-admin) users cannot access the Super Admin dashboard.
     */
    public function test_regular_user_cannot_access_super_admin_dashboard(): void
    {
        $regularUser = User::create([
            'name' => 'Regular Merchant',
            'email' => 'merchant@example.com',
            'password' => bcrypt('password'),
            'tenant_id' => $this->tenantId,
            'is_super_admin' => false,
        ]);

        $response = $this->actingAs($regularUser)->get('/admin');
        $response->assertStatus(403);
    }

    /**
     * Verify that a Super Admin can access the dashboard and see analytics.
     */
    public function test_super_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin');

        $response->assertStatus(200);
        $response->assertViewIs('super-admin.dashboard');
        $response->assertViewHasAll([
            'totalStores',
            'activeStores',
            'expiredStores',
            'suspendedStores',
            'totalMerchants',
            'totalProducts',
            'totalOrders',
            'recentStores',
        ]);
    }

    /**
     * Verify that a Super Admin can view the tenant list page.
     */
    public function test_super_admin_can_view_tenants_list(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/tenants');

        $response->assertStatus(200);
        $response->assertViewIs('super-admin.tenants.index');
        $response->assertViewHas('tenants');
    }

    /**
     * Verify that a Super Admin can view the create tenant form.
     */
    public function test_super_admin_can_view_create_tenant_form(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/tenants/create');

        $response->assertStatus(200);
        $response->assertViewIs('super-admin.tenants.create');
    }

    /**
     * Verify that a Super Admin can view a specific tenant's details.
     */
    public function test_super_admin_can_view_tenant_details(): void
    {
        $response = $this->actingAs($this->superAdmin)->get("/admin/tenants/{$this->tenantId}");

        $response->assertStatus(200);
        $response->assertViewIs('super-admin.tenants.show');
        $response->assertViewHasAll([
            'tenant',
            'productCount',
            'orderCount',
            'recentOrders',
            'merchantUsers',
        ]);
    }

    /**
     * Verify that a Super Admin can update a tenant's subscription plan and status.
     */
    public function test_super_admin_can_update_subscription(): void
    {
        $response = $this->actingAs($this->superAdmin)->post("/admin/tenants/{$this->tenantId}/update-subscription", [
            'subscription_plan' => 'enterprise',
            'subscription_status' => 'trial',
            'subscription_ends_at' => '2027-12-31',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $updatedTenant = Tenant::find($this->tenantId);
        $this->assertEquals('enterprise', $updatedTenant->subscription_plan);
        $this->assertEquals('trial', $updatedTenant->subscription_status);
        $this->assertEquals('2027-12-31', \Carbon\Carbon::parse($updatedTenant->subscription_ends_at)->format('Y-m-d'));
    }

    /**
     * Verify that a Super Admin can toggle a tenant's status between active and suspended.
     */
    public function test_super_admin_can_toggle_store_status(): void
    {
        // First toggle: active → suspended
        $response = $this->actingAs($this->superAdmin)->post("/admin/tenants/{$this->tenantId}/toggle-status");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('suspended', Tenant::find($this->tenantId)->subscription_status);

        // Second toggle: suspended → active
        $response = $this->actingAs($this->superAdmin)->post("/admin/tenants/{$this->tenantId}/toggle-status");
        $response->assertRedirect();

        $this->assertEquals('active', Tenant::find($this->tenantId)->subscription_status);
    }

    /**
     * Verify that a suspended tenant's storefront is blocked with 403.
     */
    public function test_suspended_store_shows_error_page(): void
    {
        $this->tenant->update(['subscription_status' => 'suspended']);

        $response = $this->get("http://{$this->tenantId}.localhost/");
        $response->assertStatus(403);
    }

    /**
     * Verify that an expired tenant's storefront is blocked with 403.
     */
    public function test_expired_store_shows_error_page(): void
    {
        $this->tenant->update(['subscription_status' => 'expired']);

        $response = $this->get("http://{$this->tenantId}.localhost/");
        $response->assertStatus(403);
    }

    /**
     * Verify that a Super Admin can delete a tenant store completely.
     */
    public function test_super_admin_can_delete_tenant(): void
    {
        // Create a disposable tenant for deletion
        $disposableId = 'del' . rand(100000, 999999);
        $disposable = Tenant::create([
            'id' => $disposableId,
            'name' => 'Disposable Store',
            'subscription_plan' => 'free',
            'subscription_status' => 'active',
        ]);
        $disposable->domains()->create(['domain' => $disposableId . '.localhost']);

        $response = $this->actingAs($this->superAdmin)->post("/admin/tenants/{$disposableId}/delete");
        $response->assertRedirect('/admin/tenants');
        $response->assertSessionHas('success');

        $this->assertNull(Tenant::find($disposableId));
    }
}
