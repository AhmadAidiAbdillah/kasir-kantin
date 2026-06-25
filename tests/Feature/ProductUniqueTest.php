<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductUniqueTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Owner']);
        Role::firstOrCreate(['name' => 'Admin']);
        Role::firstOrCreate(['name' => 'Kasir']);
    }

    public function test_product_name_must_be_unique_per_store()
    {
        $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
        $store = Store::create(['name' => 'Store A', 'owner_id' => $owner->id]);

        $category = Category::create(['name' => 'Makanan', 'store_id' => $store->id]);

        // Create initial product
        $product1 = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Nasi Goreng',
            'price' => 15000,
            'stock' => 10,
        ]);

        // Attempt to create duplicate product in the same store
        $response = $this->actingAs($owner)
            ->post(route('products.store'), [
                'store_id' => $store->id,
                'category_id' => $category->id,
                'name' => 'Nasi Goreng',
                'price' => 12000,
                'stock' => 5,
            ]);

        $response->assertSessionHasErrors('name');
        
        // Assert error message contains the custom message
        $errors = session('errors');
        $this->assertEquals(
            'Nama menu/produk sudah terdaftar di toko ini.', 
            $errors->first('name')
        );
    }

    public function test_product_name_can_be_same_across_different_stores()
    {
        $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
        $store1 = Store::create(['name' => 'Store 1', 'owner_id' => $owner->id]);
        $store2 = Store::create(['name' => 'Store 2', 'owner_id' => $owner->id]);

        $category1 = Category::create(['name' => 'Makanan 1', 'store_id' => $store1->id]);
        $category2 = Category::create(['name' => 'Makanan 2', 'store_id' => $store2->id]);

        // Create product in store 1
        $product1 = Product::create([
            'store_id' => $store1->id,
            'category_id' => $category1->id,
            'name' => 'Nasi Goreng',
            'price' => 15000,
            'stock' => 10,
        ]);

        // Create same product name in store 2 (should pass validation)
        $response = $this->actingAs($owner)
            ->post(route('products.store'), [
                'store_id' => $store2->id,
                'category_id' => $category2->id,
                'name' => 'Nasi Goreng',
                'price' => 16000,
                'stock' => 15,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'store_id' => $store2->id,
            'name' => 'Nasi Goreng',
            'price' => 16000,
        ]);
    }

    public function test_product_update_validates_uniqueness_excluding_self()
    {
        $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
        $store = Store::create(['name' => 'Store A', 'owner_id' => $owner->id]);

        $category = Category::create(['name' => 'Makanan', 'store_id' => $store->id]);

        $product1 = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Nasi Goreng',
            'price' => 15000,
            'stock' => 10,
        ]);

        $product2 = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Mie Goreng',
            'price' => 12000,
            'stock' => 10,
        ]);

        // 1. Updating with own name should succeed
        $response = $this->actingAs($owner)
            ->put(route('products.update', $product1->id), [
                'store_id' => $store->id,
                'category_id' => $category->id,
                'name' => 'Nasi Goreng',
                'price' => 17000,
                'stock' => 8,
            ]);

        $response->assertRedirect();
        $this->assertEquals('Nasi Goreng', $product1->fresh()->name);
        $this->assertEquals(17000, $product1->fresh()->price);

        // 2. Updating product 1 to product 2's name should fail
        $response2 = $this->actingAs($owner)
            ->put(route('products.update', $product1->id), [
                'store_id' => $store->id,
                'category_id' => $category->id,
                'name' => 'Mie Goreng',
                'price' => 17000,
                'stock' => 8,
            ]);

        $response2->assertSessionHasErrors('name');
    }

    public function test_api_product_name_must_be_unique_per_store()
    {
        $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
        $store = Store::create(['name' => 'Store A', 'owner_id' => $owner->id]);
        $category = Category::create(['name' => 'Makanan', 'store_id' => $store->id]);

        // Create product
        Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Nasi Goreng',
            'price' => 15000,
            'stock' => 10,
        ]);

        // Call API store
        $response = $this->actingAs($owner)
            ->postJson('/api/products', [
                'store_id' => $store->id,
                'category_id' => $category->id,
                'name' => 'Nasi Goreng',
                'price' => 12000,
                'stock' => 5,
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
        $response->assertJsonPath('errors.name.0', 'Nama menu/produk sudah terdaftar di toko ini.');
    }

    public function test_api_product_mutations_restricted_to_owner_and_admin()
    {
        $kasir = User::factory()->create(['role_id' => Role::where('name', 'Kasir')->first()->id]);
        $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
        $store = Store::create(['name' => 'Store A', 'owner_id' => $owner->id]);
        $category = Category::create(['name' => 'Makanan', 'store_id' => $store->id]);

        $product = Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'name' => 'Nasi Goreng',
            'price' => 15000,
            'stock' => 10,
        ]);

        // 1. Kasir POST request should be forbidden (403)
        $responsePost = $this->actingAs($kasir)
            ->postJson('/api/products', [
                'store_id' => $store->id,
                'category_id' => $category->id,
                'name' => 'Mie Goreng',
                'price' => 12000,
                'stock' => 5,
            ]);
        $responsePost->assertStatus(403);

        // 2. Kasir PUT request should be forbidden (403)
        $responsePut = $this->actingAs($kasir)
            ->putJson("/api/products/{$product->id}", [
                'name' => 'Nasi Goreng Spesial',
            ]);
        $responsePut->assertStatus(403);

        // 3. Kasir DELETE request should be forbidden (403)
        $responseDelete = $this->actingAs($kasir)
            ->deleteJson("/api/products/{$product->id}");
        $responseDelete->assertStatus(403);

        // 4. Kasir GET requests (index & show) should succeed (200)
        $responseIndex = $this->actingAs($kasir)
            ->getJson("/api/products?store_id={$store->id}");
        $responseIndex->assertStatus(200);

        $responseShow = $this->actingAs($kasir)
            ->getJson("/api/products/{$product->id}");
        $responseShow->assertStatus(200);
    }
}
