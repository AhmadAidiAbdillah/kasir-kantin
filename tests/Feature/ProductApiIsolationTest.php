<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Role $ownerRole;
    protected Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ownerRole = Role::firstOrCreate(['name' => 'Owner']);
        $this->adminRole = Role::firstOrCreate(['name' => 'Admin']);
    }

    public function test_admin_cannot_create_product_for_another_store_via_api()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $storeA = Store::create(['name' => 'Store A', 'owner_id' => $owner->id]);
        $storeB = Store::create(['name' => 'Store B', 'owner_id' => $owner->id]);

        $adminA = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'store_id' => $storeA->id,
        ]);

        $categoryB = Category::create(['name' => 'Makanan B', 'store_id' => $storeB->id]);

        // Admin A tries to create product in Store B via API
        $response = $this->actingAs($adminA)->postJson('/api/products', [
            'store_id' => $storeB->id,
            'category_id' => $categoryB->id,
            'name' => 'Cross-tenant Product',
            'price' => 10000,
            'stock' => 5,
        ]);

        // Should be forbidden (403)
        $response->assertStatus(403);
    }

    public function test_admin_cannot_update_product_of_another_store_via_api()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $storeA = Store::create(['name' => 'Store A', 'owner_id' => $owner->id]);
        $storeB = Store::create(['name' => 'Store B', 'owner_id' => $owner->id]);

        $adminA = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'store_id' => $storeA->id,
        ]);

        $categoryB = Category::create(['name' => 'Makanan B', 'store_id' => $storeB->id]);
        $productB = Product::create([
            'name' => 'Es Teh B',
            'price' => 5000,
            'stock' => 20,
            'category_id' => $categoryB->id,
            'store_id' => $storeB->id,
        ]);

        // Admin A tries to update Product B via API
        $response = $this->actingAs($adminA)->putJson("/api/products/{$productB->id}", [
            'name' => 'Hacked Es Teh',
            'price' => 100,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_cannot_delete_product_of_another_store_via_api()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $storeA = Store::create(['name' => 'Store A', 'owner_id' => $owner->id]);
        $storeB = Store::create(['name' => 'Store B', 'owner_id' => $owner->id]);

        $adminA = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'store_id' => $storeA->id,
        ]);

        $categoryB = Category::create(['name' => 'Makanan B', 'store_id' => $storeB->id]);
        $productB = Product::create([
            'name' => 'Es Jeruk B',
            'price' => 6000,
            'stock' => 20,
            'category_id' => $categoryB->id,
            'store_id' => $storeB->id,
        ]);

        // Admin A tries to delete Product B via API
        $response = $this->actingAs($adminA)->deleteJson("/api/products/{$productB->id}");

        $response->assertStatus(403);
    }
}
