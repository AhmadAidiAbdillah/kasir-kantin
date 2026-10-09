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

class TransactionDeepTest extends TestCase
{
    use RefreshDatabase;

    protected Role $ownerRole;
    protected Role $adminRole;
    protected Role $kasirRole;
    protected User $owner;
    protected Store $store;
    protected User $kasir;
    protected Category $category;
    protected Product $productA;
    protected Product $productB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ownerRole = Role::firstOrCreate(['name' => 'Owner']);
        $this->adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $this->kasirRole = Role::firstOrCreate(['name' => 'Kasir']);

        $this->owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
        $this->store = Store::create(['name' => 'Kantin Sejahtera', 'owner_id' => $this->owner->id]);

        $this->kasir = User::factory()->create([
            'role_id' => $this->kasirRole->id,
            'store_id' => $this->store->id,
        ]);

        $this->category = Category::create(['name' => 'Makanan', 'store_id' => $this->store->id]);

        $this->productA = Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'name' => 'Bakso Sapi',
            'price' => 15000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->productB = Product::create([
            'store_id' => $this->store->id,
            'category_id' => $this->category->id,
            'name' => 'Es Teh Manis',
            'price' => 5000,
            'stock' => 20,
            'is_active' => true,
        ]);
    }

    public function test_checkout_exact_payment_succeeds()
    {
        $response = $this->actingAs($this->kasir)->post(route('kasir.store'), [
            'store_id' => $this->store->id,
            'paid' => 15000,
            'payment_method' => 'Cash',
            'items' => [
                ['product_id' => $this->productA->id, 'qty' => 1]
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'store_id' => $this->store->id,
            'total_price' => 15000,
            'paid_amount' => 15000,
            'change_amount' => 0,
        ]);
        $this->assertEquals(9, $this->productA->fresh()->stock);
    }

    public function test_checkout_overpayment_calculates_change_accurately()
    {
        $response = $this->actingAs($this->kasir)->post(route('kasir.store'), [
            'store_id' => $this->store->id,
            'paid' => 50000,
            'payment_method' => 'Cash',
            'items' => [
                ['product_id' => $this->productA->id, 'qty' => 2] // 30000
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'total_price' => 30000,
            'paid_amount' => 50000,
            'change_amount' => 20000,
        ]);
        $this->assertEquals(8, $this->productA->fresh()->stock);
    }

    public function test_checkout_underpayment_is_rejected()
    {
        $response = $this->actingAs($this->kasir)->post(route('kasir.store'), [
            'store_id' => $this->store->id,
            'paid' => 10000, // bill is 15000
            'payment_method' => 'Cash',
            'items' => [
                ['product_id' => $this->productA->id, 'qty' => 1]
            ]
        ]);

        $response->assertSessionHas('error', 'Jumlah uang pembayaran kurang dari total tagihan.');
        $this->assertEquals(0, Transaction::count());
        $this->assertEquals(10, $this->productA->fresh()->stock);
    }

    public function test_checkout_insufficient_stock_is_rejected()
    {
        $response = $this->actingAs($this->kasir)->post(route('kasir.store'), [
            'store_id' => $this->store->id,
            'paid' => 200000,
            'payment_method' => 'Cash',
            'items' => [
                ['product_id' => $this->productA->id, 'qty' => 15] // stock is only 10
            ]
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(0, Transaction::count());
        $this->assertEquals(10, $this->productA->fresh()->stock);
    }

    public function test_checkout_with_multiple_items_and_duplicate_aggregation()
    {
        // 2x Bakso (30,000) + 1x Bakso (15,000) = 3x Bakso (45,000)
        // 2x Es Teh (10,000)
        // Total = 55,000
        $response = $this->actingAs($this->kasir)->post(route('kasir.store'), [
            'store_id' => $this->store->id,
            'paid' => 60000,
            'payment_method' => 'QRIS',
            'items' => [
                ['product_id' => $this->productA->id, 'qty' => 2],
                ['product_id' => $this->productB->id, 'qty' => 2],
                ['product_id' => $this->productA->id, 'qty' => 1],
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'total_price' => 55000,
            'paid_amount' => 60000,
            'change_amount' => 5000,
            'payment_method' => 'QRIS',
        ]);

        $this->assertEquals(7, $this->productA->fresh()->stock);
        $this->assertEquals(18, $this->productB->fresh()->stock);

        // Details should have 2 unique rows (aggregated)
        $tx = Transaction::first();
        $this->assertCount(2, $tx->details);
    }

    public function test_checkout_invalid_payment_method_fails_validation()
    {
        $response = $this->actingAs($this->kasir)->post(route('kasir.store'), [
            'store_id' => $this->store->id,
            'paid' => 15000,
            'payment_method' => 'Cryptocurrency',
            'items' => [
                ['product_id' => $this->productA->id, 'qty' => 1]
            ]
        ]);

        $response->assertSessionHasErrors('payment_method');
        $this->assertEquals(0, Transaction::count());
    }

    public function test_checkout_with_discount_and_tax_calculation()
    {
        // Subtotal = 10,000 (2x Es Teh)
        // Discount 20% = 2,000
        // Tax 10% on (10,000 - 2,000 = 8,000) = 800
        // Final Total = 8,800
        // Paid = 10,000 -> Change = 1,200
        $response = $this->actingAs($this->kasir)->post(route('kasir.store'), [
            'store_id' => $this->store->id,
            'discount_percent' => 20,
            'tax_percent' => 10,
            'paid' => 10000,
            'payment_method' => 'Transfer',
            'items' => [
                ['product_id' => $this->productB->id, 'qty' => 2]
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'discount_percent' => 20,
            'discount_amount' => 2000,
            'tax_percent' => 10,
            'tax_amount' => 800,
            'total_price' => 8800,
            'paid_amount' => 10000,
            'change_amount' => 1200,
            'payment_method' => 'Transfer',
        ]);
    }

    public function test_checkout_with_zero_percent_tax_saves_zero_tax_amount_and_correct_total()
    {
        $response = $this->actingAs($this->kasir)->post(route('kasir.store'), [
            'store_id' => $this->store->id,
            'discount_percent' => 0,
            'tax_percent' => 0,
            'paid' => 15000,
            'payment_method' => 'Cash',
            'items' => [
                ['product_id' => $this->productA->id, 'qty' => 1] // 15,000
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax_percent' => 0,
            'tax_amount' => 0,
            'total_price' => 15000,
            'paid_amount' => 15000,
            'change_amount' => 0,
            'payment_method' => 'Cash',
        ]);

        $tx = Transaction::where('total_price', 15000)->latest('id')->first();
        $showResponse = $this->actingAs($this->kasir)->get(route('transactions.show', $tx));
        $showResponse->assertStatus(200);
        $showResponse->assertDontSee('Pajak (PPN');
    }

    public function test_transactions_export_csv_and_pdf()
    {
        // Create a transaction first
        $tx = Transaction::create([
            'store_id' => $this->store->id,
            'user_id' => $this->kasir->id,
            'invoice_number' => 'TRX-TEST-EXPORT',
            'discount_percent' => 0,
            'discount_amount' => 0,
            'tax_percent' => 0,
            'tax_amount' => 0,
            'total_price' => 15000,
            'paid_amount' => 20000,
            'change_amount' => 5000,
            'payment_method' => 'Cash',
        ]);

        // 1. Export CSV
        $responseCsv = $this->actingAs($this->owner)->get(route('transactions.export.csv', [
            'store_id' => $this->store->id
        ]));
        $responseCsv->assertStatus(200);
        $this->assertStringContainsString('text/csv', $responseCsv->headers->get('content-type'));

        // 2. Export PDF / Print view
        $responsePdf = $this->actingAs($this->owner)->get(route('transactions.export.pdf', [
            'store_id' => $this->store->id
        ]));
        $responsePdf->assertStatus(200);
        $responsePdf->assertSee('TRX-TEST-EXPORT');

        // 3. Show Invoice
        $responseShow = $this->actingAs($this->kasir)->get(route('transactions.show', $tx));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('TRX-TEST-EXPORT');
    }
}
