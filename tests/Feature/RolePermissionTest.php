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

class RolePermissionTest extends TestCase
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

    public function test_guest_is_redirected_to_login_on_protected_routes()
    {
        $protectedRoutes = [
            '/dashboard',
            '/kasir',
            '/transactions',
            '/categories',
            '/products',
            '/customers',
            '/owner/stores',
            '/owner/users',
            '/admin/dashboard',
            '/kasir/dashboard',
            '/profile',
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    public function test_dashboard_redirects_to_role_specific_dashboard()
    {
        // 1. Owner redirect
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $this->actingAs($owner)->get('/dashboard')
            ->assertRedirect(route('owner.dashboard'));

        // 2. Admin redirect
        $store = Store::create(['name' => 'Store Test', 'owner_id' => $owner->id]);
        $admin = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'store_id' => $store->id,
        ]);
        $this->actingAs($admin)->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));

        // 3. Kasir redirect
        $kasir = User::factory()->create([
            'role_id' => $this->kasirRole->id,
            'store_id' => $store->id,
        ]);
        $this->actingAs($kasir)->get('/dashboard')
            ->assertRedirect(route('kasir.dashboard'));
    }

    public function test_owner_has_access_to_all_owner_and_management_routes()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $store = Store::create(['name' => 'Store Owner', 'owner_id' => $owner->id]);

        $routes = [
            route('owner.dashboard'),
            route('owner.stores.index'),
            route('owner.stores.create'),
            route('owner.users.index'),
            route('owner.users.create'),
            route('kasir'),
            route('transactions.index'),
            route('categories.index'),
            route('products.index'),
            route('customers.index'),
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($owner)->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_admin_forbidden_from_owner_routes()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $store = Store::create(['name' => 'Store Admin Test', 'owner_id' => $owner->id]);
        $admin = User::factory()->create([
            'role_id' => $this->adminRole->id,
            'store_id' => $store->id,
        ]);

        $ownerRoutes = [
            '/owner/dashboard',
            '/owner/stores',
            '/owner/stores/create',
            '/owner/users',
            '/owner/users/create',
        ];

        foreach ($ownerRoutes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $response->assertStatus(403);
        }
    }

    public function test_kasir_forbidden_from_owner_admin_and_master_data_routes()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $store = Store::create(['name' => 'Store Kasir Test', 'owner_id' => $owner->id]);
        $kasir = User::factory()->create([
            'role_id' => $this->kasirRole->id,
            'store_id' => $store->id,
        ]);

        $forbiddenRoutes = [
            '/owner/dashboard',
            '/owner/stores',
            '/owner/users',
            '/admin/dashboard',
            '/categories',
            '/categories/create',
            '/products',
            '/products/create',
        ];

        foreach ($forbiddenRoutes as $route) {
            $response = $this->actingAs($kasir)->get($route);
            $response->assertStatus(403);
        }
    }

    public function test_kasir_can_access_allowed_routes()
    {
        $owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $store = Store::create(['name' => 'Store Kasir Allowed', 'owner_id' => $owner->id]);
        $kasir = User::factory()->create([
            'role_id' => $this->kasirRole->id,
            'store_id' => $store->id,
        ]);

        $allowedRoutes = [
            route('kasir.dashboard'),
            route('kasir'),
            route('transactions.index'),
            route('customers.index'),
        ];

        foreach ($allowedRoutes as $route) {
            $response = $this->actingAs($kasir)->get($route);
            $response->assertStatus(200);
        }
    }
}
