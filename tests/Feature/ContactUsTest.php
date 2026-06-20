<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\StoreSetting;
use App\Models\Page;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ContactUsTest extends TestCase
{
    use DatabaseMigrations;

    private string $tenantId;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        // Configure central domain for the test run
        config(['tenancy.central_domains' => ['localhost']]);

        // Dynamic tenant ID to avoid DB file sharing/locking conflicts
        $this->tenantId = 'ts' . rand(100000, 999999);

        // Create a test tenant and associate a test domain
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
        
        // Cleanup storage contact files created in test
        $messagesFile = storage_path('app/contact_messages_' . $this->tenantId . '.json');
        if (file_exists($messagesFile)) {
            unlink($messagesFile);
        }

        parent::tearDown();
    }

    /**
     * Test the complete Contact Us flow: page creation, auto-menu adding, submission and dashboard deletion.
     */
    public function test_contact_us_complete_flow(): void
    {
        // 1. Create a page with add_to_header checked
        $response = $this->post("http://{$this->tenantId}.localhost/shop/settings/page", [
            'title' => 'Contact Us',
            'slug' => 'contact-us',
            'content' => '<h1>Contact Page Template</h1>',
            'is_active' => '1',
            'add_to_header' => '1'
        ]);

        $response->assertRedirect('/shop/settings');

        // Initialize tenancy to check database
        tenancy()->initialize($this->tenant);

        // Verify Page exists
        $page = Page::where('slug', 'contact-us')->first();
        $this->assertNotNull($page);
        $this->assertEquals('Contact Us', $page->title);

        // Verify Header Menu has the link
        $settings = StoreSetting::first();
        $this->assertNotNull($settings);
        $headerMenu = $settings->header_menu;
        $this->assertIsArray($headerMenu);
        
        $hasLink = false;
        foreach ($headerMenu as $item) {
            if (($item['url'] ?? '') === '/page/contact-us') {
                $hasLink = true;
                $this->assertEquals('Contact Us', $item['label']);
            }
        }
        $this->assertTrue($hasLink, 'Header navigation should contain contact-us link');

        tenancy()->end();

        // 2. Submit contact form
        $contactData = [
            'name' => 'Ali Raza',
            'email' => 'ali@example.com',
            'subject' => 'Product inquiry',
            'message' => 'Is this product organic?'
        ];

        // Send public POST request to contact-submit route
        $response = $this->postJson("http://{$this->tenantId}.localhost/contact-submit", $contactData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Your message has been sent successfully! We will get back to you soon.'
        ]);

        // Verify file was written in storage
        $messagesFile = storage_path('app/contact_messages_' . $this->tenantId . '.json');
        $this->assertFileExists($messagesFile);

        $savedMessages = json_decode(file_get_contents($messagesFile), true);
        $this->assertCount(1, $savedMessages);
        $this->assertEquals('Ali Raza', $savedMessages[0]['name']);
        $this->assertEquals('ali@example.com', $savedMessages[0]['email']);
        $this->assertEquals('Product inquiry', $savedMessages[0]['subject']);
        $this->assertEquals('Is this product organic?', $savedMessages[0]['message']);

        // 3. Get Messages page and verify contact messages are visible
        $response = $this->get("http://{$this->tenantId}.localhost/shop/messages");
        $response->assertStatus(200);
        $response->assertSee('Ali Raza');
        $response->assertSee('Product inquiry');

        // 4. Delete the message (original index is 0)
        $deleteResponse = $this->get("http://{$this->tenantId}.localhost/shop/messages/delete/0");
        $deleteResponse->assertRedirect('/shop/messages');

        // Verify it was deleted from file
        $this->assertFileExists($messagesFile);
        $remainingMessages = json_decode(file_get_contents($messagesFile), true);
        $this->assertCount(0, $remainingMessages);
    }
}
