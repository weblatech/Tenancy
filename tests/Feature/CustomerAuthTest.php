<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\StoreSetting;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerAuthTest extends TestCase
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
        if (isset($this->tenant)) {
            $this->tenant->delete();
        }

        parent::tearDown();
    }

    public function test_customer_registration_and_login_flow(): void
    {
        // 1. Visit registration page
        $response = $this->get("http://{$this->tenantId}.localhost/customer/register");
        $response->assertStatus(200);

        // 2. Submit registration form
        $regData = [
            'name' => 'John Customer',
            'email' => 'john.cust@example.com',
            'phone' => '03009999999',
            'city' => 'Islamabad',
            'address' => 'House 1, Street 2',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ];

        $response = $this->post("http://{$this->tenantId}.localhost/customer/register", $regData);
        $response->assertRedirect('/customer/dashboard');

        // Initialize tenancy to check DB
        tenancy()->initialize($this->tenant);
        $customer = Customer::where('email', 'john.cust@example.com')->first();
        $this->assertNotNull($customer);
        $this->assertEquals('John Customer', $customer->name);
        $this->assertTrue(Hash::check('secret123', $customer->password));
        $this->assertEquals('03009999999', $customer->phone);
        $this->assertEquals('Islamabad', $customer->city);
        $this->assertEquals('House 1, Street 2', $customer->address);
        tenancy()->end();

        // 3. Log out
        $response = $this->post("http://{$this->tenantId}.localhost/customer/logout");
        $response->assertRedirect('/');

        // 4. Try to access dashboard (should redirect to login)
        $response = $this->get("http://{$this->tenantId}.localhost/customer/dashboard");
        $response->assertRedirect('/customer/login');

        // 5. Submit login form
        $loginData = [
            'email' => 'john.cust@example.com',
            'password' => 'secret123',
        ];
        $response = $this->post("http://{$this->tenantId}.localhost/customer/login", $loginData);
        $response->assertRedirect('/customer/dashboard');

        // 6. Access dashboard when logged in
        $response = $this->get("http://{$this->tenantId}.localhost/customer/dashboard");
        $response->assertStatus(200);
        $response->assertSee('John Customer');
        $response->assertSee('john.cust@example.com');
        $response->assertSee('Islamabad');

        // 7. Auto-fill checkout fields check
        $response = $this->get("http://{$this->tenantId}.localhost/checkout");
        $response->assertStatus(200);
        $response->assertSee('value="John Customer"', false);
        $response->assertSee('value="03009999999"', false);
        $response->assertSee('value="Islamabad"', false);
        $response->assertSee('House 1, Street 2');

        // 8. Place order and verify linkage
        tenancy()->initialize($this->tenant);
        $product = Product::create([
            'name' => 'Test Tea',
            'price' => 500,
            'description' => 'Test Tea Description',
        ]);
        tenancy()->end();

        $cartItems = [
            [
                'id' => $product->id,
                'name' => 'Test Tea',
                'price' => 500,
                'qty' => 2,
            ]
        ];

        $checkoutData = [
            'customer_name' => 'John Customer',
            'customer_phone' => '03009999999',
            'customer_city' => 'Islamabad',
            'customer_address' => 'House 1, Street 2',
            'cart_items_json' => json_encode($cartItems),
        ];

        $response = $this->post("http://{$this->tenantId}.localhost/checkout", $checkoutData);
        
        tenancy()->initialize($this->tenant);
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals($customer->id, $order->customer_id);
        tenancy()->end();
    }
}
