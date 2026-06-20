<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\Product;
use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class StorefrontCartTest extends TestCase
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
        // Clean up tenant database and records
        if (isset($this->tenant)) {
            $this->tenant->delete();
        }
        parent::tearDown();
    }

    /**
     * Test storefront product page rendering.
     */
    public function test_product_detail_page_renders_successfully(): void
    {
        // Initialize tenancy to create seed product
        tenancy()->initialize($this->tenant);

        $product = Product::create([
            'name' => 'Premium Bundle Offer',
            'price' => 2500,
            'compare_price' => 4500,
            'description' => 'A wonderful organic bundle',
            'stock' => 10,
            'is_bundle' => true,
            'bundle_title' => 'Special Combo Deal',
            'bundle_price' => 2500,
            'bundle_details' => 'Buy 2 get 1 free',
            'bundle_header_title' => 'بڑی عید کی بڑی آفر',
            'bundle_header_badge' => 'Hot Sale',
            'bundle_color_primary' => '#16a34a',
            'bundle_color_text' => '#ffffff',
            'bundle_options' => [
                [
                    'title' => '2 items + 1 Free',
                    'price' => 2500,
                    'compare_price' => 4500,
                    'badge' => 'Save Rs. 2000',
                    'label' => 'Hot Sale',
                ]
            ],
        ]);

        tenancy()->end();

        // Send GET request to the product detail page on the tenant domain
        $response = $this->get("http://{$this->tenantId}.localhost/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertSee('Premium Bundle Offer');
        $response->assertSee('2 items + 1 Free');
        $response->assertSee('بڑی عید کی بڑی آفر');
        $response->assertSee('Order Now - Cash on Delivery');
    }

    /**
     * Test storefront homepage rendering.
     */
    public function test_homepage_renders_successfully(): void
    {
        $response = $this->get("http://{$this->tenantId}.localhost/");

        $response->assertStatus(200);
    }

    /**
     * Test storefront collection page rendering.
     */
    public function test_collection_page_renders_successfully(): void
    {
        $response = $this->get("http://{$this->tenantId}.localhost/collection");

        $response->assertStatus(200);
        $response->assertSee('All Products');
    }

    /**
     * Test checkout page rendering.
     */
    public function test_checkout_page_renders_successfully(): void
    {
        $response = $this->get("http://{$this->tenantId}.localhost/checkout");

        $response->assertStatus(200);
        $response->assertSee('Order Summary');
    }

    /**
     * Test order placement through checkout.
     */
    public function test_order_can_be_placed_successfully(): void
    {
        // Initialize tenancy to check database state later
        tenancy()->initialize($this->tenant);
        $product = Product::create([
            'name' => 'Single Product',
            'price' => 1500,
            'compare_price' => 2000,
            'stock' => 10,
        ]);
        tenancy()->end();

        // Cart items payload mimicking LocalStorage format
        $cartItems = [
            [
                'id' => $product->id,
                'name' => 'Single Product',
                'price' => 1500,
                'originalPrice' => 2000,
                'qty' => 2,
                'image' => '',
                'selectedVariants' => ['Size' => 'Large']
            ]
        ];

        $payload = [
            'customer_name' => 'Muhammad Ahmed',
            'customer_phone' => '03001234567',
            'customer_city' => 'Lahore',
            'customer_address' => 'House 123, Street 5, DHA Phase 5',
            'cart_items_json' => json_encode($cartItems),
            'client_ip' => '39.40.120.50',
            'latitude' => '31.5497',
            'longitude' => '74.3436',
            'ip_city' => 'Lahore',
            'ip_country' => 'Pakistan',
            'ip_isp' => 'PTCL',
        ];

        // Send post request to the checkout route on the tenant domain
        $response = $this->post("http://{$this->tenantId}.localhost/checkout", $payload);

        // Should redirect to order success page
        $response->assertStatus(302);
        $response->assertRedirectContains('/order-success/');

        // Assert order was written into the tenant database
        tenancy()->initialize($this->tenant);
        $this->assertEquals(1, Order::count());

        $order = Order::first();
        $this->assertEquals('Muhammad Ahmed', $order->customer_name);
        $this->assertEquals('03001234567', $order->customer_phone);
        $this->assertEquals(3000, $order->subtotal); // 1500 * 2
        $this->assertEquals(0, $order->shipping_fee);
        $this->assertEquals(3000, $order->total); // 3000 + 0
        $this->assertEquals('Lahore', $order->customer_city);
        $this->assertEquals('Pakistan', $order->ip_country);
        $this->assertEquals('Lahore', $order->ip_city);

        // Check if the order success page renders correctly with details
        tenancy()->end();

        $successResponse = $this->get("http://{$this->tenantId}.localhost/order-success/{$order->id}");
        $successResponse->assertStatus(200);
        $successResponse->assertSee('Muhammad Ahmed');
        $successResponse->assertSee('Original Subtotal');
        $successResponse->assertSee('Special Discount');
        $successResponse->assertSee('Rs. 4,000'); // Original subtotal (2000 * 2)
        $successResponse->assertSee('- Rs. 1,000'); // Discount amount (4000 - 3000)
        $successResponse->assertSee('Rs. 3,000'); // Total (3000 + 0)
    }
}
