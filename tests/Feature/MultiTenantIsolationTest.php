<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Role;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected Role $ownerRole;
    protected Role $adminRole;
    protected Role $kasirRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ownerRole = Role::firstOrCreate(['name' => 'Owner']);
        $this->adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $this->kasirRole = Role::firstOrCreate(['name' => 'Kasir']);
    }

    public function test_owner_cannot_modify_or_delete_other_owners_stores()
    {
        $ownerA = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $ownerB = User::factory()->create(['role_id' => $this->ownerRole->id]);

        $storeB = Store::create(['name' => 'Store B', 'owner_id' => $ownerB->id]);

        // Owner A tries to edit Store B
        $this->actingAs($ownerA)->get(route('owner.stores.edit', $storeB))
            ->assertStatus(403);

        // Owner A tries to update Store B
        $this->actingAs($ownerA)->put(route('owner.stores.update', $storeB), [
            'name' => 'Hacked Store B',
        ])->assertStatus(403);

        $this->assertEquals('Store B', $storeB->fresh()->name);

        // Owner A tries to delete Store B
        $this->actingAs($ownerA)->delete(route('owner.stores.destroy', $storeB))
            ->assertStatus(403);

        $this->assertDatabaseHas('stores', ['id' => $storeB->id]);
    }

    public function test_owner_cannot_manage_users_belonging_to_other_owners_stores()
    {
        $ownerA = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $ownerB = User::factory()->create(['role_id' => $this->ownerRole->id]);

        $storeB = Store::create(['name' => 'Store B', 'owner_id' => $ownerB->id]);
        $adminB = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'store_id' => $storeB->id,
        ]);

        // Owner A tries to edit Admin B
        $this->actingAs($ownerA)->get(route('owner.users.edit', $adminB))
            ->assertStatus(403);

        // Owner A tries to update Admin B
        $this->actingAs($ownerA)->put(route('owner.users.update', $adminB), [
            'name' => 'Hacked Name',
            'email' => $adminB->email,
            'role_id' => $this->adminRole->id,
            'store_id' => $storeB->id,
        ])->assertStatus(403);

        // Owner A tries to delete Admin B
        $this->actingAs($ownerA)->delete(route('owner.users.destroy', $adminB))
            ->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $adminB->id]);

        // Owner A tries to reset password of Admin B
        $this->actingAs($ownerA)->patch(route('owner.users.reset', $adminB))
            ->assertStatus(403);
    }

    public function test_admin_cannot_modify_or_delete_other_stores_categories()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $storeA = Store::create(['name' => 'Store A', 'owner_id' => $owner->id]);
        $storeB = Store::create(['name' => 'Store B', 'owner_id' => $owner->id]);

        $adminA = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'store_id' => $storeA->id,
        ]);

        $categoryB = Category::create(['name' => 'Minuman B', 'store_id' => $storeB->id]);

        // Admin A tries to edit Category B
        $this->actingAs($adminA)->get(route('categories.edit', $categoryB))
            ->assertStatus(403);

        // Admin A tries to update Category B
        $this->actingAs($adminA)->put(route('categories.update', $categoryB), [
            'name' => 'Hacked Minuman',
        ])->assertStatus(403);

        $this->assertEquals('Minuman B', $categoryB->fresh()->name);

        // Admin A tries to delete Category B
        $this->actingAs($adminA)->delete(route('categories.destroy', $categoryB))
            ->assertStatus(403);

        $this->assertDatabaseHas('categories', ['id' => $categoryB->id]);
    }

    public function test_admin_cannot_modify_or_delete_other_stores_products()
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
            'name' => 'Mie Goreng B',
            'price' => 12000,
            'stock' => 10,
            'category_id' => $categoryB->id,
            'store_id' => $storeB->id,
        ]);

        // Admin A tries to edit Product B
        $this->actingAs($adminA)->get(route('products.edit', $productB))
            ->assertStatus(403);

        // Admin A tries to update Product B
        $this->actingAs($adminA)->put(route('products.update', $productB), [
            'name' => 'Hacked Mie',
            'price' => 5000,
            'stock' => 5,
            'category_id' => $categoryB->id,
        ])->assertStatus(403);

        $this->assertEquals('Mie Goreng B', $productB->fresh()->name);

        // Admin A tries to delete Product B
        $this->actingAs($adminA)->delete(route('products.destroy', $productB))
            ->assertStatus(403);

        $this->assertDatabaseHas('products', ['id' => $productB->id]);
    }

    public function test_admin_cannot_modify_or_delete_other_stores_customers()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $storeA = Store::create(['name' => 'Store A', 'owner_id' => $owner->id]);
        $storeB = Store::create(['name' => 'Store B', 'owner_id' => $owner->id]);

        $adminA = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'store_id' => $storeA->id,
        ]);

        $customerB = Customer::create(['name' => 'Pelanggan Toko B', 'store_id' => $storeB->id]);

        // Admin A tries to edit Customer B
        $this->actingAs($adminA)->get(route('customers.edit', $customerB))
            ->assertStatus(403);

        // Admin A tries to update Customer B
        $this->actingAs($adminA)->put(route('customers.update', $customerB), [
            'name' => 'Hacked Customer',
        ])->assertStatus(403);

        $this->assertEquals('Pelanggan Toko B', $customerB->fresh()->name);

        // Admin A tries to delete Customer B
        $this->actingAs($adminA)->delete(route('customers.destroy', $customerB))
            ->assertStatus(403);

        $this->assertDatabaseHas('customers', ['id' => $customerB->id]);
    }

    public function test_user_cannot_view_other_stores_transaction_invoice()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $storeA = Store::create(['name' => 'Store A', 'owner_id' => $owner->id]);
        $storeB = Store::create(['name' => 'Store B', 'owner_id' => $owner->id]);

        $kasirA = User::factory()->create([
            'role_id' => $this->kasirRole->id,
            'store_id' => $storeA->id,
        ]);

        $kasirB = User::factory()->create([
            'role_id' => $this->kasirRole->id,
            'store_id' => $storeB->id,
        ]);

        $txB = Transaction::create([
            'store_id' => $storeB->id,
            'user_id' => $kasirB->id,
            'invoice_number' => 'TRX-TEST-B',
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax_percent' => 0,
            'tax_amount' => 0,
            'total_price' => 15000,
            'paid_amount' => 20000,
            'change_amount' => 5000,
            'payment_method' => 'Cash',
        ]);

        // Kasir A from Store A tries to view Invoice from Store B
        $this->actingAs($kasirA)->get(route('transactions.show', $txB))
            ->assertStatus(403);
    }
}
