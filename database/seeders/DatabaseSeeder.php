<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles
        $ownerRole = Role::firstOrCreate(['name' => 'Owner']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $kasirRole = Role::firstOrCreate(['name' => 'Kasir']);

        // 2. Seed Default Store
        $store = Store::firstOrCreate(
            ['name' => 'Kantin Utama'],
            [
                'address' => 'Jl. Kantin Raya No. 1, Sumenep',
                'phone' => '081234567890',
            ]
        );

        // 3. Seed Owner Demo Account
        $ownerUser = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name' => 'Owner Demo',
                'password' => Hash::make('password'),
                'role_id' => $ownerRole->id,
                'store_id' => null, // Owner doesn't strictly belong to one store, manages all
            ]
        );

        // Update store owner link
        $store->update(['owner_id' => $ownerUser->id]);

        // 4. Seed Admin Demo Account
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Demo',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'store_id' => $store->id,
            ]
        );

        // 5. Seed Kasir Demo Account
        User::firstOrCreate(
            ['email' => 'kasir@example.com'],
            [
                'name' => 'Kasir Demo',
                'password' => Hash::make('password'),
                'role_id' => $kasirRole->id,
                'store_id' => $store->id,
            ]
        );

        // 6. Seed Default Categories
        $makananCategory = Category::firstOrCreate(
            ['name' => 'Makanan', 'store_id' => $store->id],
            ['description' => 'Menu Makanan Utama dan Cemilan']
        );

        $minumanCategory = Category::firstOrCreate(
            ['name' => 'Minuman', 'store_id' => $store->id],
            ['description' => 'Menu Minuman Dingin dan Hangat']
        );

        // 7. Seed Default Products
        Product::firstOrCreate(
            ['name' => 'Nasi Goreng Spesial', 'store_id' => $store->id],
            [
                'price' => 15000,
                'stock' => 50,
                'category_id' => $makananCategory->id,
                'is_active' => true,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Ayam Penyet', 'store_id' => $store->id],
            [
                'price' => 18000,
                'stock' => 30,
                'category_id' => $makananCategory->id,
                'is_active' => true,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Es Teh Manis', 'store_id' => $store->id],
            [
                'price' => 5000,
                'stock' => 100,
                'category_id' => $minumanCategory->id,
                'is_active' => true,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Es Jeruk', 'store_id' => $store->id],
            [
                'price' => 7000,
                'stock' => 80,
                'category_id' => $minumanCategory->id,
                'is_active' => true,
            ]
        );

        // 8. Seed Default Customers
        Customer::firstOrCreate(
            ['name' => 'Umum', 'store_id' => $store->id],
            [
                'email' => 'umum@example.com',
                'phone' => '-',
                'address' => 'Pelanggan Walk-in',
            ]
        );

        Customer::firstOrCreate(
            ['name' => 'Budi Utomo', 'store_id' => $store->id],
            [
                'email' => 'budi@example.com',
                'phone' => '081222333444',
                'address' => 'Ruang 302',
            ]
        );

        Customer::firstOrCreate(
            ['name' => 'Siti Aminah', 'store_id' => $store->id],
            [
                'email' => 'siti@example.com',
                'phone' => '081333444555',
                'address' => 'Ruang 104',
            ]
        );

        // 9. Seed Default Transactions (over the last 7 days for rich chart data)
        $kasirUser = User::where('email', 'kasir@example.com')->first();
        $products = Product::where('store_id', $store->id)->get();
        $customers = Customer::where('store_id', $store->id)->get();

        if ($kasirUser && $products->isNotEmpty() && Transaction::where('store_id', $store->id)->count() === 0) {
            $paymentMethods = ['Cash', 'QRIS', 'Transfer'];
            
            for ($i = 6; $i >= 0; $i--) {
                // Determine date for this specific day
                $date = now()->subDays($i)->setTime(rand(9, 21), rand(0, 59), rand(0, 59));
                
                // Create 1-2 transactions per day
                $txCount = rand(1, 2);
                for ($txIndex = 1; $txIndex <= $txCount; $txIndex++) {
                    $customer = $customers->random();
                    $discountPercent = rand(0, 4) === 0 ? 10 : 0; // 20% chance of 10% discount
                    $taxPercent = 11; // 11% PPN is standard

                    // Generate unique invoice number
                    $invoiceNumber = 'INV-' . $date->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

                    $transaction = Transaction::create([
                        'store_id' => $store->id,
                        'user_id' => $kasirUser->id,
                        'customer_id' => $customer->id,
                        'invoice_number' => $invoiceNumber,
                        'discount_percent' => $discountPercent,
                        'discount_amount' => 0, // calculated below
                        'tax_percent' => $taxPercent,
                        'tax_amount' => 0, // calculated below
                        'total_price' => 0, // calculated below
                        'paid_amount' => 0, // calculated below
                        'change_amount' => 0, // calculated below
                        'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    // Add 1-3 random products to the transaction
                    $selectedProducts = $products->random(rand(1, min(3, $products->count())));
                    $subtotal = 0;

                    foreach ($selectedProducts as $product) {
                        $qty = rand(1, 3);
                        $itemSubtotal = $product->price * $qty;
                        $subtotal += $itemSubtotal;

                        TransactionDetail::create([
                            'transaction_id' => $transaction->id,
                            'product_id' => $product->id,
                            'quantity' => $qty,
                            'price' => $product->price,
                            'subtotal' => $itemSubtotal,
                            'created_at' => $date,
                            'updated_at' => $date,
                        ]);
                    }

                    // Calculate amounts
                    $discountAmount = ($subtotal * $discountPercent) / 100;
                    $taxableAmount = $subtotal - $discountAmount;
                    $taxAmount = ($taxableAmount * $taxPercent) / 100;
                    $totalPrice = $taxableAmount + $taxAmount;

                    // Round numbers
                    $discountAmount = round($discountAmount);
                    $taxAmount = round($taxAmount);
                    $totalPrice = round($totalPrice);

                    // Paid amount has to cover total price
                    $paidAmount = ceil($totalPrice / 5000) * 5000; // round up to nearest 5000
                    if ($transaction->payment_method !== 'Cash') {
                        $paidAmount = $totalPrice; // Exact amount for non-cash
                    }
                    $changeAmount = $paidAmount - $totalPrice;

                    $transaction->update([
                        'discount_amount' => $discountAmount,
                        'tax_amount' => $taxAmount,
                        'total_price' => $totalPrice,
                        'paid_amount' => $paidAmount,
                        'change_amount' => $changeAmount,
                    ]);
                }
            }
        }
    }
}
