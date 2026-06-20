<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AdminCustomerDirectoryTest extends TestCase
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

    public function test_customer_directory_listing_and_metrics(): void
    {
        // Initialize tenancy to seed data
        tenancy()->initialize($this->tenant);

        // 1. Create a registered customer
        $customer = Customer::create([
            'name' => 'Ali Raza',
            'email' => 'ali.raza@example.com',
            'phone' => '03001234567',
            'city' => 'Lahore',
            'address' => 'Model Town',
            'password' => bcrypt('password123'),
        ]);

        // 2. Create order for registered customer
        Order::create([
            'customer_name' => 'Ali Raza',
            'customer_phone' => '03001234567',
            'customer_city' => 'Lahore',
            'customer_address' => 'Model Town',
            'cart_items' => [['id' => 1, 'name' => 'Item A', 'price' => 1000, 'qty' => 1]],
            'subtotal' => 1000,
            'shipping_fee' => 200,
            'total' => 1200,
            'status' => 'completed',
            'customer_id' => $customer->id,
        ]);

        // 3. Create a guest customer (order with no customer_id)
        Order::create([
            'customer_name' => 'Sajid Khan',
            'customer_phone' => '03123456789',
            'customer_city' => 'Karachi',
            'customer_address' => 'Clifton',
            'cart_items' => [['id' => 2, 'name' => 'Item B', 'price' => 2000, 'qty' => 1]],
            'subtotal' => 2000,
            'shipping_fee' => 0,
            'total' => 2000,
            'status' => 'pending',
            'customer_id' => null,
        ]);

        tenancy()->end();

        // Visit customer directory
        $response = $this->get("http://{$this->tenantId}.localhost/shop/customers");
        $response->assertStatus(200);

        // Assert metrics are correct in view
        $response->assertSee('Ali Raza');
        $response->assertSee('Sajid Khan');
        $response->assertSee('Guest Buyer'); // Badge for Sajid Khan
        
        // Assert counts
        $response->assertSee('2'); // Total Customers
        $response->assertSee('Lahore');
        $response->assertSee('Karachi');
        $response->assertSee('Rs 1,200'); // Spent by Ali
        $response->assertSee('Rs 2,000'); // Spent by Sajid
    }

    public function test_customer_export_csv(): void
    {
        tenancy()->initialize($this->tenant);
        Customer::create([
            'name' => 'Zeeshan Ali',
            'email' => 'zeeshan@example.com',
            'phone' => '03211111111',
            'city' => 'Peshawar',
            'address' => 'Hayatabad',
            'password' => bcrypt('password123'),
        ]);
        tenancy()->end();

        $response = $this->get("http://{$this->tenantId}.localhost/shop/customers/export");
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=customers_export_' . date('Y-m-d') . '.csv');
        
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();
        $this->assertStringContainsString('Zeeshan Ali', $content);
        $this->assertStringContainsString('zeeshan@example.com', $content);
    }

    public function test_customer_details_registered(): void
    {
        tenancy()->initialize($this->tenant);
        $customer = Customer::create([
            'name' => 'Zahid Mahmood',
            'email' => 'zahid@example.com',
            'phone' => '03333333333',
            'city' => 'Faisalabad',
            'address' => 'Sargodha Road',
            'password' => bcrypt('password123'),
        ]);
        tenancy()->end();

        $response = $this->get("http://{$this->tenantId}.localhost/shop/customers/{$customer->id}");
        $response->assertStatus(200);
        $response->assertSee('Zahid Mahmood');
        $response->assertSee('zahid@example.com');
        $response->assertSee('Faisalabad');
        $response->assertSee('Registered Account');
    }

    public function test_customer_details_guest(): void
    {
        tenancy()->initialize($this->tenant);
        Order::create([
            'customer_name' => 'Waseem Akram',
            'customer_phone' => '03454444444',
            'customer_city' => 'Multan',
            'customer_address' => 'Cantt',
            'cart_items' => [['id' => 3, 'name' => 'Item C', 'price' => 3000, 'qty' => 1]],
            'subtotal' => 3000,
            'shipping_fee' => 0,
            'total' => 3000,
            'status' => 'pending',
            'customer_id' => null,
        ]);
        tenancy()->end();

        $guestKey = base64_encode('03454444444');
        $response = $this->get("http://{$this->tenantId}.localhost/shop/customers/guest/{$guestKey}");
        $response->assertStatus(200);
        $response->assertSee('Waseem Akram');
        $response->assertSee('Multan');
        $response->assertSee('Guest Customer');
        $response->assertSee('Rs 3,000');
    }

    public function test_customer_filtering_by_type_and_delivery(): void
    {
        tenancy()->initialize($this->tenant);

        // Customer A: Registered this month, has 1 completed order (orders_count = 1)
        $customerA = Customer::create([
            'name' => 'Customer Alpha',
            'email' => 'alpha@example.com',
            'phone' => '03001111111',
            'city' => 'Lahore',
            'address' => 'A Block',
            'password' => bcrypt('password123'),
            'created_at' => now(),
        ]);
        Order::create([
            'customer_name' => 'Customer Alpha',
            'customer_phone' => '03001111111',
            'customer_city' => 'Lahore',
            'customer_address' => 'A Block',
            'cart_items' => [['id' => 1, 'name' => 'Item A', 'price' => 500, 'qty' => 1]],
            'subtotal' => 500,
            'shipping_fee' => 0,
            'total' => 500,
            'status' => 'completed',
            'customer_id' => $customerA->id,
            'created_at' => now(),
        ]);

        // Customer B: Registered last year, has 2 completed orders (orders_count = 2)
        $customerB = Customer::create([
            'name' => 'Customer Beta',
            'email' => 'beta@example.com',
            'phone' => '03002222222',
            'city' => 'Karachi',
            'address' => 'B Block',
            'password' => bcrypt('password123'),
        ]);
        \DB::table('customers')->where('id', $customerB->id)->update(['created_at' => now()->subYear()]);

        $orderB1 = Order::create([
            'customer_name' => 'Customer Beta',
            'customer_phone' => '03002222222',
            'customer_city' => 'Karachi',
            'customer_address' => 'B Block',
            'cart_items' => [['id' => 1, 'name' => 'Item A', 'price' => 500, 'qty' => 1]],
            'subtotal' => 500,
            'shipping_fee' => 0,
            'total' => 500,
            'status' => 'completed',
            'customer_id' => $customerB->id,
        ]);
        \DB::table('orders')->where('id', $orderB1->id)->update(['created_at' => now()->subYear()]);

        Order::create([
            'customer_name' => 'Customer Beta',
            'customer_phone' => '03002222222',
            'customer_city' => 'Karachi',
            'customer_address' => 'B Block',
            'cart_items' => [['id' => 1, 'name' => 'Item A', 'price' => 500, 'qty' => 1]],
            'subtotal' => 500,
            'shipping_fee' => 0,
            'total' => 500,
            'status' => 'completed',
            'customer_id' => $customerB->id,
        ]);

        // Customer C: Registered this month, has 1 cancelled order (orders_count = 1)
        $customerC = Customer::create([
            'name' => 'Customer Gamma',
            'email' => 'gamma@example.com',
            'phone' => '03003333333',
            'city' => 'Islamabad',
            'address' => 'C Block',
            'password' => bcrypt('password123'),
        ]);
        Order::create([
            'customer_name' => 'Customer Gamma',
            'customer_phone' => '03003333333',
            'customer_city' => 'Islamabad',
            'customer_address' => 'C Block',
            'cart_items' => [['id' => 1, 'name' => 'Item A', 'price' => 500, 'qty' => 1]],
            'subtotal' => 500,
            'shipping_fee' => 0,
            'total' => 500,
            'status' => 'cancelled',
            'customer_id' => $customerC->id,
        ]);

        // Customer D: Registered last year, 0 orders
        $customerD = Customer::create([
            'name' => 'Customer Delta',
            'email' => 'delta@example.com',
            'phone' => '03004444444',
            'city' => 'Faisalabad',
            'address' => 'D Block',
            'password' => bcrypt('password123'),
        ]);
        \DB::table('customers')->where('id', $customerD->id)->update(['created_at' => now()->subYear()]);

        tenancy()->end();

        // 1. Verify ?type=new (Alpha, Gamma are new since they have exactly 1 order)
        $response = $this->get("http://{$this->tenantId}.localhost/shop/customers?type=new");
        $response->assertStatus(200);
        $response->assertSee('Customer Alpha');
        $response->assertSee('Customer Gamma');
        $response->assertDontSee('Customer Beta');

        // 2. Verify ?type=this_month (Alpha, Gamma registered this month; Beta has order this month; Delta should not be there)
        $response = $this->get("http://{$this->tenantId}.localhost/shop/customers?type=this_month");
        $response->assertStatus(200);
        $response->assertSee('Customer Alpha');
        $response->assertSee('Customer Gamma');
        $response->assertSee('Customer Beta'); // Beta has last order this month
        $response->assertDontSee('Customer Delta');

        // 3. Verify ?type=repeat (Beta has 2 orders)
        $response = $this->get("http://{$this->tenantId}.localhost/shop/customers?type=repeat");
        $response->assertStatus(200);
        $response->assertSee('Customer Beta');
        $response->assertDontSee('Customer Alpha');
        $response->assertDontSee('Customer Gamma');

        // 4. Verify ?delivery=delivered (Alpha, Beta have completed/delivered orders)
        $response = $this->get("http://{$this->tenantId}.localhost/shop/customers?delivery=delivered");
        $response->assertStatus(200);
        $response->assertSee('Customer Alpha');
        $response->assertSee('Customer Beta');
        $response->assertDontSee('Customer Gamma');

        // 5. Verify ?delivery=returned (Gamma has cancelled/returned order)
        $response = $this->get("http://{$this->tenantId}.localhost/shop/customers?delivery=returned");
        $response->assertStatus(200);
        $response->assertSee('Customer Gamma');
        $response->assertDontSee('Customer Alpha');
        $response->assertDontSee('Customer Beta');

        // 6. Verify ?delivery=no_orders (Delta has 0 orders)
        $response = $this->get("http://{$this->tenantId}.localhost/shop/customers?delivery=no_orders");
        $response->assertStatus(200);
        $response->assertSee('Customer Delta');
        $response->assertDontSee('Customer Alpha');
        $response->assertDontSee('Customer Beta');
        $response->assertDontSee('Customer Gamma');

        // 7. Verify CSV Export with ?delivery=returned
        $response = $this->get("http://{$this->tenantId}.localhost/shop/customers/export?delivery=returned");
        $response->assertStatus(200);
        
        ob_start();
        $response->sendContent();
        $csvContent = ob_get_clean();
        
        $this->assertStringContainsString('Customer Gamma', $csvContent);
        $this->assertStringNotContainsString('Customer Alpha', $csvContent);
        $this->assertStringNotContainsString('Customer Beta', $csvContent);
    }
}
