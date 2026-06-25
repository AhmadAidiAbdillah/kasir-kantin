<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class POSCheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Roles
        Role::firstOrCreate(['name' => 'Owner']);
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Kasir']);
    }

    /**
     * Test checkout calculates subtotal, discount, tax and final price correctly.
     */
    public function test_checkout_calculations_are_saved_correctly()
    {
        $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
        $store = Store::create(['name' => 'Store A', 'owner_id' => $owner->id]);
        
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'Admin')->first()->id,
            'store_id' => $store->id,
        ]);

        $category = Category::create(['name' => 'Makanan', 'store_id' => $store->id]);
        $product = Product::create([
            'name' => 'Nasgor',
            'price' => 10000,
            'stock' => 10,
            'category_id' => $category->id,
            'store_id' => $store->id,
        ]);

        $customer = Customer::create(['name' => 'Budi', 'store_id' => $store->id]);

        $response = $this->actingAs($admin)
            ->post(route('kasir.store'), [
                'store_id' => $store->id,
                'customer_id' => $customer->id,
                'paid' => 20000, // covers final price
                'payment_method' => 'Cash',
                'discount_percent' => 10, // 10% discount on 10,000 subtotal = 1000 discount
                'tax_percent' => 11, // 11% PPN on (10,000 - 1000) = 990 PPN
                'items' => [
                    [
                        'product_id' => $product->id,
                        'qty' => 1
                    ]
                ]
            ]);

        $response->assertRedirect();
        
        // Assert transaction was created with correct values
        $this->assertDatabaseHas('transactions', [
            'store_id' => $store->id,
            'user_id' => $admin->id,
            'customer_id' => $customer->id,
            'discount_percent' => 10,
            'discount_amount' => 1000,
            'tax_percent' => 11,
            'tax_amount' => 990,
            'total_price' => 9990,
            'paid_amount' => 20000,
            'change_amount' => 10010,
            'payment_method' => 'Cash',
        ]);

        // Assert product stock was decremented
        $this->assertEquals(9, $product->fresh()->stock);
    }

    /**
     * Test that user cannot checkout from a store they do not belong to.
     */
    public function test_checkout_cross_tenant_is_forbidden()
    {
        $ownerA = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
        $storeA = Store::create(['name' => 'Store A', 'owner_id' => $ownerA->id]);

        $ownerB = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
        $storeB = Store::create(['name' => 'Store B', 'owner_id' => $ownerB->id]);

        $adminA = User::factory()->create([
            'role_id' => Role::where('name', 'Admin')->first()->id,
            'store_id' => $storeA->id,
        ]);

        $categoryB = Category::create(['name' => 'Makanan B', 'store_id' => $storeB->id]);
        $productB = Product::create([
            'name' => 'Nasgor B',
            'price' => 10000,
            'stock' => 10,
            'category_id' => $categoryB->id,
            'store_id' => $storeB->id,
        ]);

        // Try checkout on Store B using Admin A credentials
        $response = $this->actingAs($adminA)
            ->post(route('kasir.store'), [
                'store_id' => $storeB->id,
                'paid' => 10000,
                'payment_method' => 'Cash',
                'items' => [
                    [
                        'product_id' => $productB->id,
                        'qty' => 1
                    ]
                ]
            ]);

        $response->assertSessionHas('error', 'Anda tidak memiliki akses ke toko ini.');
    }
}
