<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminDashboardActionsTest extends TestCase
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
            'name' => 'Actions Test Store',
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

        parent::tearDown();
    }

    /**
     * Test email reply to a contact inquiry (with Mail mocking).
     */
    public function test_admin_can_send_reply_email(): void
    {
        Mail::fake();

        $replyData = [
            'email' => 'customer@example.com',
            'subject' => 'Re: Support Request',
            'message' => 'Hello customer, here is your support reply.',
        ];

        $response = $this->post("http://{$this->tenantId}.localhost/shop/messages/reply", $replyData);

        $response->assertRedirect('/shop/messages');
        $response->assertSessionHas('success', 'Reply email sent successfully! 📧');

        Mail::assertSent(function (\Illuminate\Mail\Mailable $mail) use ($replyData) {
            return $mail->hasTo($replyData['email']) &&
                   $mail->subject === $replyData['subject'];
        });
    }

    /**
     * Test promotional campaign broadcast to all newsletter subscribers (with Mail mocking).
     */
    public function test_admin_can_broadcast_promotional_campaign(): void
    {
        Mail::fake();

        // 1. Create subscribers JSON mock data
        tenancy()->initialize($this->tenant);
        $subscribersFile = storage_path('app/subscribers_' . $this->tenantId . '.json');
        if (!is_dir(dirname($subscribersFile))) {
            mkdir(dirname($subscribersFile), 0755, true);
        }
        $subscribersData = [
            ['email' => 'sub1@example.com', 'created_at' => now()->toDateTimeString(), 'ip' => '1.1.1.1'],
            ['email' => 'sub2@example.com', 'created_at' => now()->toDateTimeString(), 'ip' => '2.2.2.2'],
        ];
        file_put_contents($subscribersFile, json_encode($subscribersData, JSON_PRETTY_PRINT));
        tenancy()->end();

        $broadcastData = [
            'subject' => 'Huge Sale Alert!',
            'message' => 'Get 50% off everything in our store this weekend.',
        ];

        $response = $this->post("http://{$this->tenantId}.localhost/shop/subscribers/broadcast", $broadcastData);

        $response->assertRedirect('/shop/subscribers');
        $response->assertSessionHas('success', 'Campaign broadcast sent successfully to all 2 subscribers! 🚀');

        Mail::assertSent(function (\Illuminate\Mail\Mailable $mail) use ($broadcastData) {
            return ($mail->hasTo('sub1@example.com') || $mail->hasTo('sub2@example.com')) &&
                   $mail->subject === $broadcastData['subject'];
        });
    }

    /**
     * Test Cloudflare proxy IP resolution.
     */
    public function test_ip_resolution_cloudflare_proxy(): void
    {
        $contactData = [
            'name' => 'CF Tester',
            'email' => 'cf@example.com',
            'subject' => 'CF IP Test',
            'message' => 'Testing Cloudflare IP resolution.'
        ];

        $response = $this->withHeaders([
            'CF-Connecting-IP' => '172.68.10.25'
        ])->postJson("http://{$this->tenantId}.localhost/contact-submit", $contactData);

        $response->assertStatus(200);

        tenancy()->initialize($this->tenant);
        $messagesFile = storage_path('app/contact_messages_' . $this->tenantId . '.json');
        $this->assertFileExists($messagesFile);
        $savedMessages = json_decode(file_get_contents($messagesFile), true);
        $this->assertEquals('172.68.10.25', $savedMessages[0]['ip']);
        tenancy()->end();
    }

    /**
     * Test X-Forwarded-For proxy chain IP resolution.
     */
    public function test_ip_resolution_x_forwarded_for_proxy(): void
    {
        $responseSub = $this->withHeaders([
            'X-Forwarded-For' => '198.51.100.42, 172.217.16.14'
        ])->postJson("http://{$this->tenantId}.localhost/newsletter-subscribe", [
            'email' => 'newsletter_proxy@example.com'
        ]);

        $responseSub->assertStatus(200);

        tenancy()->initialize($this->tenant);
        $subscribersFile = storage_path('app/subscribers_' . $this->tenantId . '.json');
        $this->assertFileExists($subscribersFile);
        $savedSubscribers = json_decode(file_get_contents($subscribersFile), true);
        $this->assertEquals('198.51.100.42', $savedSubscribers[0]['ip']);
        tenancy()->end();
    }

    /**
     * Test checkout IP resolution.
     */
    public function test_ip_resolution_checkout_real_ip(): void
    {
        // Initialize tenancy to seed product
        tenancy()->initialize($this->tenant);
        $settings = \App\Models\StoreSetting::firstOrCreate(['id' => 1]);
        $product = \App\Models\Product::create([
            'name' => 'IP Tester Product',
            'price' => 1000,
            'stock' => 10,
        ]);
        tenancy()->end();

        $cartJson = json_encode([
            [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'qty' => 1,
            ]
        ]);

        $checkoutData = [
            'customer_name' => 'Checkout Tester',
            'customer_phone' => '03001234567',
            'customer_address' => 'Test House 1',
            'customer_city' => 'Lahore',
            'cart_items_json' => $cartJson,
            'client_ip' => '172.68.10.30',
            'ip_country' => 'Pakistan',
            'ip_city' => 'Lahore',
            'latitude' => '31.5204',
            'longitude' => '74.3587',
            'ip_isp' => 'Nayatel',
        ];

        $responseCheckout = $this->withHeaders([
            'X-Real-IP' => '172.68.10.30'
        ])->post("http://{$this->tenantId}.localhost/checkout", $checkoutData);

        $responseCheckout->assertRedirect();

        tenancy()->initialize($this->tenant);
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('172.68.10.30', $order->ip_address);
        tenancy()->end();
    }

    /**
     * Test admin can access payments settings page and update shipping and gateway settings.
     */
    public function test_admin_can_update_payment_settings(): void
    {
        $this->get("http://{$this->tenantId}.localhost/shop/payments")
             ->assertStatus(200)
             ->assertViewIs('tenant.payment.index');

        $paymentData = [
            'shipping_mode' => 'flat',
            'shipping_flat_fee' => 150,
            'shipping_threshold' => 1500,
            'payment_cod_active' => '1',
            'payment_bank_active' => '1',
            'payment_bank_name' => 'Test Bank',
            'payment_bank_title' => 'Test Title',
            'payment_bank_number' => '123456789',
        ];

        $response = $this->post("http://{$this->tenantId}.localhost/shop/payments", $paymentData);

        $response->assertRedirect('/shop/payments');
        $response->assertSessionHas('success', 'Payment & Delivery Settings updated successfully! 💳');

        tenancy()->initialize($this->tenant);
        $settings = \App\Models\StoreSetting::first();
        $this->assertEquals('flat', $settings->shipping_mode);
        $this->assertEquals(150, $settings->shipping_flat_fee);
        $this->assertEquals(1500, $settings->shipping_threshold);
        $this->assertTrue((bool)$settings->payment_cod_active);
        $this->assertTrue((bool)$settings->payment_bank_active);
        $this->assertEquals('Test Bank', $settings->payment_bank_name);
        $this->assertEquals('Test Title', $settings->payment_bank_title);
        $this->assertEquals('123456789', $settings->payment_bank_number);
        tenancy()->end();
    }
}
