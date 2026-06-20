<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MerchantCustomDomainTest extends TestCase
{
    use DatabaseMigrations;

    private string $tenantId;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        config(['tenancy.central_domains' => ['localhost']]);

        $this->tenantId = 'ts' . rand(100000, 999999);

        $this->tenant = Tenant::create([
            'id' => $this->tenantId,
            'name' => 'Test Store',
        ]);

        $this->tenant->domains()->create(['domain' => $this->tenantId . '.localhost']);

        // Force tenant database creation and migration by initializing and ending tenancy
        tenancy()->initialize($this->tenant);
        tenancy()->end();
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

    public function test_merchant_can_list_domains_and_see_default(): void
    {
        $response = $this->get("http://{$this->tenantId}.localhost/shop/domains");
        $response->assertStatus(200);
        $response->assertSee($this->tenantId . '.localhost');
        $response->assertSee('System');

        if (tenancy()->initialized) {
            tenancy()->end();
        }
    }

    public function test_merchant_can_add_custom_domain(): void
    {
        $response = $this->post("http://{$this->tenantId}.localhost/shop/domains", [
            'domain' => 'mybrand.com',
        ]);

        $response->assertRedirect('/shop/domains');
        $response->assertSessionHas('success');

        if (tenancy()->initialized) {
            tenancy()->end();
        }

        // Check in central database
        $this->assertDatabaseHas('domains', [
            'domain' => 'mybrand.com',
            'tenant_id' => $this->tenantId,
        ]);
    }

    public function test_domain_validation_blocks_duplicates_and_central_domains(): void
    {
        // 1. Block duplicate
        $response = $this->post("http://{$this->tenantId}.localhost/shop/domains", [
            'domain' => $this->tenantId . '.localhost',
        ]);
        $response->assertSessionHas('error');

        // 2. Block central domain
        $response = $this->post("http://{$this->tenantId}.localhost/shop/domains", [
            'domain' => 'localhost',
        ]);
        $response->assertSessionHas('error');

        if (tenancy()->initialized) {
            tenancy()->end();
        }
    }

    public function test_merchant_cannot_delete_default_subdomain(): void
    {
        tenancy()->initialize($this->tenant);
        $domainRecord = \Stancl\Tenancy\Database\Models\Domain::where('domain', $this->tenantId . '.localhost')->first();
        tenancy()->end();

        $response = $this->post("http://{$this->tenantId}.localhost/shop/domains/{$domainRecord->id}/delete");
        $response->assertSessionHas('error');
        
        if (tenancy()->initialized) {
            tenancy()->end();
        }

        // Assert domain is still there
        $this->assertDatabaseHas('domains', [
            'id' => $domainRecord->id,
        ]);
    }

    public function test_merchant_can_delete_custom_domain(): void
    {
        // Create custom domain first
        tenancy()->initialize($this->tenant);
        $customDomain = $this->tenant->domains()->create(['domain' => 'mybrand.com']);
        tenancy()->end();

        $response = $this->post("http://{$this->tenantId}.localhost/shop/domains/{$customDomain->id}/delete");
        $response->assertRedirect('/shop/domains');
        $response->assertSessionHas('success');

        if (tenancy()->initialized) {
            tenancy()->end();
        }

        // Assert domain is deleted
        $this->assertDatabaseMissing('domains', [
            'id' => $customDomain->id,
        ]);
    }

    public function test_custom_domain_resolves_storefront_context(): void
    {
        tenancy()->initialize($this->tenant);
        $this->tenant->domains()->create(['domain' => 'shop.mybrand.com']);
        tenancy()->end();

        // Disable central domain check to allow shop.mybrand.com
        config(['tenancy.central_domains' => ['localhost', '127.0.0.1']]);

        $response = $this->get("http://shop.mybrand.com/");
        $response->assertStatus(200);
        $response->assertSee('Test Store'); // Asserts it resolved to our tenant storefront!

        if (tenancy()->initialized) {
            tenancy()->end();
        }
    }
}
