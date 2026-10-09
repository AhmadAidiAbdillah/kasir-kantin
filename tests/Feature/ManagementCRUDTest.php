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
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ManagementCRUDTest extends TestCase
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

    public function test_store_crud_lifecycle()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);

        // 1. Create Store
        $response = $this->actingAs($owner)->post(route('owner.stores.store'), [
            'name' => 'Kantin Pusat',
            'phone' => '081234567890',
            'address' => 'Gedung Rektorat',
        ]);
        $response->assertRedirect(route('owner.stores.index'));
        $this->assertDatabaseHas('stores', [
            'name' => 'Kantin Pusat',
            'owner_id' => $owner->id,
        ]);

        $store = Store::where('name', 'Kantin Pusat')->first();

        // 2. Edit Page
        $this->actingAs($owner)->get(route('owner.stores.edit', $store))
            ->assertStatus(200)
            ->assertSee('Kantin Pusat');

        // 3. Update Store
        $this->actingAs($owner)->put(route('owner.stores.update', $store), [
            'name' => 'Kantin Pusat Baru',
            'phone' => '089999999999',
            'address' => 'Gedung Baru',
        ])->assertRedirect(route('owner.stores.index'));

        $this->assertEquals('Kantin Pusat Baru', $store->fresh()->name);

        // 4. Delete Store
        $this->actingAs($owner)->delete(route('owner.stores.destroy', $store))
            ->assertRedirect(route('owner.stores.index'));

        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
    }

    public function test_owner_user_management_lifecycle()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $store = Store::create(['name' => 'Kantin 1', 'owner_id' => $owner->id]);

        // 1. Create Admin User
        $response = $this->actingAs($owner)->post(route('owner.users.store'), [
            'name' => 'Admin Kantin',
            'email' => 'admin.kantin@example.com',
            'role_id' => $this->adminRole->id,
            'store_id' => $store->id,
            'password' => 'secret123',
        ]);
        $response->assertRedirect(route('owner.users.index'));
        $this->assertDatabaseHas('users', ['email' => 'admin.kantin@example.com']);

        $admin = User::where('email', 'admin.kantin@example.com')->first();
        $this->assertTrue(Hash::check('secret123', $admin->password));

        // 2. Edit Admin User
        $this->actingAs($owner)->get(route('owner.users.edit', $admin))
            ->assertStatus(200)
            ->assertSee('Admin Kantin');

        // 3. Update Admin User
        $this->actingAs($owner)->put(route('owner.users.update', $admin), [
            'name' => 'Admin Updated',
            'email' => 'admin.kantin@example.com',
            'role_id' => $this->kasirRole->id, // change role to kasir
            'store_id' => $store->id,
        ])->assertRedirect(route('owner.users.index'));

        $this->assertEquals('Admin Updated', $admin->fresh()->name);
        $this->assertEquals('kasir', $admin->fresh()->role);

        // 4. Reset Password
        $this->actingAs($owner)->patch(route('owner.users.reset', $admin))
            ->assertRedirect(route('owner.users.index'));

        $this->assertTrue(Hash::check('kantin123', $admin->fresh()->password));

        // 5. Delete User
        $this->actingAs($owner)->delete(route('owner.users.destroy', $admin))
            ->assertRedirect(route('owner.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $admin->id]);
    }

    public function test_category_crud_lifecycle()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $store = Store::create(['name' => 'Kantin 1', 'owner_id' => $owner->id]);

        // 1. Create Category
        $response = $this->actingAs($owner)->post(route('categories.store'), [
            'name' => 'Cemilan',
            'description' => 'Snack ringan',
            'store_id' => $store->id,
        ]);
        $response->assertRedirect(route('categories.index', ['store_id' => $store->id]));
        $this->assertDatabaseHas('categories', ['name' => 'Cemilan', 'store_id' => $store->id]);

        $category = Category::where('name', 'Cemilan')->first();

        // 2. Edit Category
        $this->actingAs($owner)->get(route('categories.edit', $category))
            ->assertStatus(200)
            ->assertSee('Cemilan');

        // 3. Update Category
        $this->actingAs($owner)->put(route('categories.update', $category), [
            'name' => 'Cemilan & Snack',
            'description' => 'Snack enak',
            'store_id' => $store->id,
        ])->assertRedirect(route('categories.index', ['store_id' => $store->id]));

        $this->assertEquals('Cemilan & Snack', $category->fresh()->name);

        // 4. Delete Category
        $this->actingAs($owner)->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index', ['store_id' => $store->id]));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_customer_crud_lifecycle()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $store = Store::create(['name' => 'Kantin 1', 'owner_id' => $owner->id]);

        // 1. Create Customer
        $response = $this->actingAs($owner)->post(route('customers.store'), [
            'name' => 'Pak Dosen Joko',
            'email' => 'joko@kampus.ac.id',
            'phone' => '0812345678',
            'address' => 'Ruang Dosen 101',
            'store_id' => $store->id,
        ]);
        $response->assertRedirect(route('customers.index', ['store_id' => $store->id]));
        $this->assertDatabaseHas('customers', ['name' => 'Pak Dosen Joko', 'store_id' => $store->id]);

        $customer = Customer::where('name', 'Pak Dosen Joko')->first();

        // 2. Edit Customer
        $this->actingAs($owner)->get(route('customers.edit', $customer))
            ->assertStatus(200)
            ->assertSee('Pak Dosen Joko');

        // 3. Update Customer
        $this->actingAs($owner)->put(route('customers.update', $customer), [
            'name' => 'Prof. Joko, Ph.D.',
            'email' => 'joko@kampus.ac.id',
            'phone' => '0812345678',
            'address' => 'Gedung Pascasarjana',
            'store_id' => $store->id,
        ])->assertRedirect(route('customers.index', ['store_id' => $store->id]));

        $this->assertEquals('Prof. Joko, Ph.D.', $customer->fresh()->name);

        // 4. Delete Customer
        $this->actingAs($owner)->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers.index', ['store_id' => $store->id]));

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_product_crud_lifecycle()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $store = Store::create(['name' => 'Kantin 1', 'owner_id' => $owner->id]);
        $category = Category::create(['name' => 'Makanan', 'store_id' => $store->id]);

        // 1. Create Product
        $response = $this->actingAs($owner)->post(route('products.store'), [
            'name' => 'Ayam Geprek',
            'price' => 18000,
            'stock' => 25,
            'category_id' => $category->id,
            'store_id' => $store->id,
            'is_active' => '1',
        ]);
        $response->assertRedirect(route('products.index', ['store_id' => $store->id]));
        $this->assertDatabaseHas('products', ['name' => 'Ayam Geprek', 'store_id' => $store->id]);

        $product = Product::where('name', 'Ayam Geprek')->first();

        // 2. Edit Product
        $this->actingAs($owner)->get(route('products.edit', $product))
            ->assertStatus(200)
            ->assertSee('Ayam Geprek');

        // 3. Update Product
        $this->actingAs($owner)->put(route('products.update', $product), [
            'name' => 'Ayam Geprek Level 5',
            'price' => 20000,
            'stock' => 30,
            'category_id' => $category->id,
            'store_id' => $store->id,
            'is_active' => '1',
        ])->assertRedirect(route('products.index', ['store_id' => $store->id]));

        $this->assertEquals('Ayam Geprek Level 5', $product->fresh()->name);
        $this->assertEquals(20000, $product->fresh()->price);

        // 4. Delete Product
        $this->actingAs($owner)->delete(route('products.destroy', $product))
            ->assertRedirect(route('products.index', ['store_id' => $store->id]));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_dashboard_metrics_for_all_roles()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $store = Store::create(['name' => 'Kantin 1', 'owner_id' => $owner->id]);
        $admin = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'store_id' => $store->id,
        ]);
        $kasir = User::factory()->create([
            'role_id' => $this->kasirRole->id,
            'store_id' => $store->id,
        ]);

        Transaction::create([
            'store_id' => $store->id,
            'user_id' => $kasir->id,
            'invoice_number' => 'TRX-DASHBOARD-TEST',
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax_percent' => 0,
            'tax_amount' => 0,
            'total_price' => 25000,
            'paid_amount' => 30000,
            'change_amount' => 5000,
            'payment_method' => 'Cash',
        ]);

        // Owner Dashboard
        $responseOwner = $this->actingAs($owner)->get(route('owner.dashboard'));
        $responseOwner->assertStatus(200);
        $responseOwner->assertSee('25.000');

        // Admin Dashboard
        $responseAdmin = $this->actingAs($admin)->get(route('admin.dashboard'));
        $responseAdmin->assertStatus(200);
        $responseAdmin->assertSee('25.000');

        // Kasir Dashboard
        $responseKasir = $this->actingAs($kasir)->get(route('kasir.dashboard'));
        $responseKasir->assertStatus(200);
        $responseKasir->assertSee('25.000');
    }
}
