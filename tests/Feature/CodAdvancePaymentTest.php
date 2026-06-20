<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CodAdvancePaymentTest extends TestCase
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
            'name' => 'Advance Payment Store',
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

    public function test_cod_advance_payment_calculation_and_enforcement(): void
    {
        // 1. Configure COD Advance Payment Settings
        tenancy()->initialize($this->tenant);
        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        $settings->update([
            'payment_cod_active' => true,
            'cod_require_advance' => true,
            'cod_advance_type' => 'flat',
            'cod_advance_value' => 200,
            'cod_advance_instructions' => 'Please pay Rs. 200 to EasyPaisa 03001234567',
        ]);
        tenancy()->end();

        // 2. Submit a Checkout Order
        $cartMock = [
            [
                'id' => 1,
                'name' => 'Test Item',
                'price' => 1000,
                'qty' => 2,
                'image' => 'products/item.jpg'
            ]
        ];

        $checkoutData = [
            'customer_name' => 'Alice Doe',
            'customer_phone' => '03001112223',
            'customer_address' => 'House 12, Street A',
            'customer_city' => 'Lahore',
            'cart_items_json' => json_encode($cartMock),
            'payment_method' => 'cod',
        ];

        $response = $this->post("http://{$this->tenantId}.localhost/checkout", $checkoutData);

        // Assert order placed successfully
        tenancy()->initialize($this->tenant);
        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertEquals(2000, $order->subtotal);
        $this->assertEquals(200, $order->cod_advance_required);
        $this->assertFalse($order->cod_advance_paid);
        $this->assertEquals('pending', $order->status);
        tenancy()->end();

        $response->assertRedirect("http://{$this->tenantId}.localhost/order-success/{$order->id}");

        // 3. Attempt to mark order status as 'processing' or 'completed'
        // It should be blocked because advance is not paid
        $statusData = ['status' => 'processing'];
        $statusResponse = $this->post("http://{$this->tenantId}.localhost/shop/orders/{$order->id}/status", $statusData);
        $statusResponse->assertSessionHas('error', '⚠️ Cannot process order! COD Advance Payment (کیش آن ڈلیوری ایڈوانس پیمنٹ) has not been paid/verified yet.');

        tenancy()->initialize($this->tenant);
        $order = Order::find($order->id);
        $this->assertEquals('pending', $order->status); // Status remains pending
        tenancy()->end();

        // 4. Toggle the COD advance payment to verify it
        $toggleResponse = $this->post("http://{$this->tenantId}.localhost/shop/orders/{$order->id}/toggle-advance");
        $toggleResponse->assertSessionHas('success', 'COD Advance Payment verified! 💳');

        tenancy()->initialize($this->tenant);
        $order = Order::find($order->id);
        $this->assertTrue($order->cod_advance_paid); // Marked as paid now
        tenancy()->end();

        // 5. Attempt to update status again (should succeed now)
        $statusResponse2 = $this->post("http://{$this->tenantId}.localhost/shop/orders/{$order->id}/status", $statusData);
        $statusResponse2->assertSessionHas('success', 'Order status updated successfully! 📦');

        tenancy()->initialize($this->tenant);
        $order = Order::find($order->id);
        $this->assertEquals('processing', $order->status); // Successfully updated to processing
        tenancy()->end();
    }

    public function test_save_and_retrieve_structured_cod_advance_settings(): void
    {
        tenancy()->initialize($this->tenant);
        
        $settingsData = [
            'shipping_mode' => 'flat',
            'shipping_flat_fee' => 150,
            'shipping_threshold' => 1500,
            'payment_cod_active' => '1',
            'cod_require_advance' => '1',
            'cod_advance_type' => 'percentage',
            'cod_advance_value' => 10,
            'cod_advance_method' => 'easypaisa',
            'cod_advance_easypaisa_title' => 'Ali Raza',
            'cod_advance_easypaisa_number' => '03129876543',
            'cod_advance_jazzcash_title' => 'Jane Doe',
            'cod_advance_jazzcash_number' => '03112233445',
            'cod_advance_bank_name' => 'Meezan Bank',
            'cod_advance_account_title' => 'John Doe Corp',
            'cod_advance_account_number' => 'PK00MEZN00123456',
        ];

        $response = $this->post("http://{$this->tenantId}.localhost/shop/payments", $settingsData);
        $response->assertRedirect("http://{$this->tenantId}.localhost/shop/payments");
        $response->assertSessionHas('success', 'Payment & Delivery Settings updated successfully! 💳');

        $settings = StoreSetting::firstOrCreate(['id' => 1]);
        $this->assertTrue($settings->cod_require_advance);
        $this->assertEquals('percentage', $settings->cod_advance_type);
        $this->assertEquals(10, $settings->cod_advance_value);
        $this->assertEquals('easypaisa', $settings->cod_advance_method);
        $this->assertEquals('Ali Raza', $settings->cod_advance_easypaisa_title);
        $this->assertEquals('03129876543', $settings->cod_advance_easypaisa_number);
        $this->assertEquals('Jane Doe', $settings->cod_advance_jazzcash_title);
        $this->assertEquals('03112233445', $settings->cod_advance_jazzcash_number);
        $this->assertEquals('Meezan Bank', $settings->cod_advance_bank_name);
        $this->assertEquals('John Doe Corp', $settings->cod_advance_account_title);
        $this->assertEquals('PK00MEZN00123456', $settings->cod_advance_account_number);

        tenancy()->end();
    }
}
