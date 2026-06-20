<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class NewsletterSubscriptionTest extends TestCase
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
    }

    protected function tearDown(): void
    {
        if (tenancy()->initialized) {
            tenancy()->end();
        }

        if (isset($this->tenant)) {
            $this->tenant->delete();
        }
        
        $subscribersFile = storage_path('app/subscribers_' . $this->tenantId . '.json');
        if (file_exists($subscribersFile)) {
            unlink($subscribersFile);
        }

        parent::tearDown();
    }

    /**
     * Test the complete newsletter subscription flow.
     */
    public function test_newsletter_subscription_complete_flow(): void
    {
        // 1. Submit email to subscribe
        $response = $this->postJson("http://{$this->tenantId}.localhost/newsletter-subscribe", [
            'email' => 'subscriber@example.com'
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Thank you for subscribing to our newsletter! 🎉'
        ]);

        // Verify JSON file is created and contains the email
        $subscribersFile = storage_path('app/subscribers_' . $this->tenantId . '.json');
        $this->assertFileExists($subscribersFile);

        $savedSubscribers = json_decode(file_get_contents($subscribersFile), true);
        $this->assertCount(1, $savedSubscribers);
        $this->assertEquals('subscriber@example.com', $savedSubscribers[0]['email']);

        // 2. Submit duplicate email to check validation
        $responseDuplicate = $this->postJson("http://{$this->tenantId}.localhost/newsletter-subscribe", [
            'email' => 'subscriber@example.com'
        ]);

        $responseDuplicate->assertStatus(200);
        $responseDuplicate->assertJson([
            'success' => false,
            'message' => 'You are already subscribed to our newsletter! ✉️'
        ]);

        // Count should still be 1
        $savedSubscribers = json_decode(file_get_contents($subscribersFile), true);
        $this->assertCount(1, $savedSubscribers);

        // 3. View Subscribers dashboard and confirm email is visible
        $responseDashboard = $this->get("http://{$this->tenantId}.localhost/shop/subscribers");
        $responseDashboard->assertStatus(200);
        $responseDashboard->assertSee('subscriber@example.com');

        // 4. Delete subscriber by index (0)
        $deleteResponse = $this->get("http://{$this->tenantId}.localhost/shop/subscribers/delete/0");
        $deleteResponse->assertRedirect('/shop/subscribers');

        // Verify it was deleted from file
        $this->assertFileExists($subscribersFile);
        $remainingSubscribers = json_decode(file_get_contents($subscribersFile), true);
        $this->assertCount(0, $remainingSubscribers);
    }
}
