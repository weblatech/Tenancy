<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ProductVariantsPricingTest extends TestCase
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
            'name' => 'Variants Test Store',
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
     * Test creating a product with variant combinations.
     */
    public function test_can_create_product_with_variant_combinations(): void
    {
        $combinations = [
            [
                'name' => 'S / Red',
                'combination' => ['Size' => 'S', 'Color' => 'Red'],
                'price' => 1200,
                'compare_price' => 1600,
                'stock' => 5,
            ],
            [
                'name' => 'M / Blue',
                'combination' => ['Size' => 'M', 'Color' => 'Blue'],
                'price' => 1400,
                'compare_price' => 1800,
                'stock' => 0,
            ]
        ];

        $postData = [
            'name' => 'Test Variant Product',
            'price' => 1000,
            'compare_price' => 1500,
            'description' => 'Test description',
            'stock' => 10,
            'variants_text' => "Size: S, M\nColor: Red, Blue",
            'variant_combinations_json' => json_encode($combinations),
        ];

        $response = $this->post("http://{$this->tenantId}.localhost/shop/add-product", $postData);

        $response->assertRedirect('/shop/products');
        $response->assertSessionHas('success', 'Product added successfully! 🚀');

        // Verify product in database
        tenancy()->initialize($this->tenant);
        $product = Product::where('name', 'Test Variant Product')->first();
        
        $this->assertNotNull($product);
        $this->assertEquals(1000, $product->price);
        $this->assertEquals(1500, $product->compare_price);
        $this->assertEquals(['Size' => ['S', 'M'], 'Color' => ['Red', 'Blue']], $product->variants);
        $this->assertEquals($combinations, $product->variant_combinations);
        
        tenancy()->end();
    }

    /**
     * Test creating a product with fallback variant text format (no colons).
     */
    public function test_can_create_product_with_fallback_variant_text(): void
    {
        $combinations = [
            [
                'name' => '10 capsules',
                'combination' => ['Option' => '10 capsules'],
                'price' => 1200,
                'compare_price' => 1600,
                'stock' => 5,
            ],
            [
                'name' => '15 capsules',
                'combination' => ['Option' => '15 capsules'],
                'price' => 1400,
                'compare_price' => 1800,
                'stock' => 10,
            ]
        ];

        $postData = [
            'name' => 'Fallback Variant Product',
            'price' => 1000,
            'compare_price' => 1500,
            'description' => 'Test description fallback',
            'stock' => 10,
            'variants_text' => "10 capsules\n15 capsules",
            'variant_combinations_json' => json_encode($combinations),
        ];

        $response = $this->post("http://{$this->tenantId}.localhost/shop/add-product", $postData);

        $response->assertRedirect('/shop/products');
        $response->assertSessionHas('success', 'Product added successfully! 🚀');

        // Verify product in database
        tenancy()->initialize($this->tenant);
        $product = Product::where('name', 'Fallback Variant Product')->first();
        
        $this->assertNotNull($product);
        $this->assertEquals(['Option' => ['10 capsules', '15 capsules']], $product->variants);
        $this->assertEquals($combinations, $product->variant_combinations);
        
        tenancy()->end();
    }

    /**
     * Test editing a product and updating variant combinations.
     */
    public function test_can_edit_product_variant_combinations(): void
    {
        // Create initial product
        tenancy()->initialize($this->tenant);
        $product = Product::create([
            'name' => 'Original Product',
            'price' => 1000,
            'compare_price' => 1500,
            'description' => 'Original description',
            'stock' => 10,
            'variants' => ['Size' => ['S', 'M']],
            'variant_combinations' => [
                [
                    'name' => 'S',
                    'combination' => ['Size' => 'S'],
                    'price' => 1200,
                    'compare_price' => 1600,
                    'stock' => 5,
                ]
            ],
        ]);
        tenancy()->end();

        $updatedCombinations = [
            [
                'name' => 'S',
                'combination' => ['Size' => 'S'],
                'price' => 1300,
                'compare_price' => 1700,
                'stock' => 7,
            ],
            [
                'name' => 'M',
                'combination' => ['Size' => 'M'],
                'price' => 1500,
                'compare_price' => 1900,
                'stock' => 3,
            ]
        ];

        $editData = [
            'name' => 'Updated Product Name',
            'price' => 1100,
            'compare_price' => 1600,
            'description' => 'Updated description',
            'stock' => 15,
            'variants_text' => "Size: S, M",
            'variant_combinations_json' => json_encode($updatedCombinations),
        ];

        $response = $this->post("http://{$this->tenantId}.localhost/shop/products/{$product->id}/edit", $editData);

        $response->assertRedirect('/shop/products');
        $response->assertSessionHas('success', 'Product updated successfully! 🚀');

        // Verify changes in database
        tenancy()->initialize($this->tenant);
        $product->refresh();
        
        $this->assertEquals('Updated Product Name', $product->name);
        $this->assertEquals(1100, $product->price);
        $this->assertEquals(1600, $product->compare_price);
        $this->assertEquals(['Size' => ['S', 'M']], $product->variants);
        $this->assertEquals($updatedCombinations, $product->variant_combinations);
        
        tenancy()->end();
    }

    /**
     * Test storefront product page renders with variant script & dynamic selectors.
     */
    public function test_storefront_renders_variants_and_json(): void
    {
        $combinations = [
            [
                'name' => 'S / Red',
                'combination' => ['Size' => 'S', 'Color' => 'Red'],
                'price' => 1250,
                'compare_price' => 1650,
                'stock' => 4,
            ]
        ];

        tenancy()->initialize($this->tenant);
        $product = Product::create([
            'name' => 'Premium Shirt',
            'price' => 1000,
            'compare_price' => 1500,
            'description' => 'Cotton shirt',
            'stock' => 10,
            'variants' => ['Size' => ['S'], 'Color' => ['Red']],
            'variant_combinations' => $combinations,
        ]);
        tenancy()->end();

        $response = $this->get("http://{$this->tenantId}.localhost/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertSee('Premium Shirt');
        
        // Assert we output the variant combinations JSON inside the JS code block
        $response->assertSee('variantCombinations =');
        $response->assertSee('S / Red');
        $response->assertSee('1250');
        
        // Assert variant selectors are rendered
        $response->assertSee('data-option-name="Size"', false);
        $response->assertSee('data-option-name="Color"', false);
        
        // Assert out-of-stock and button element IDs
        $response->assertSee('id="variant-stock-alert"', false);
        $response->assertSee('id="btn-add-to-cart"', false);
        $response->assertSee('id="btn-buy-now"', false);
    }
}
