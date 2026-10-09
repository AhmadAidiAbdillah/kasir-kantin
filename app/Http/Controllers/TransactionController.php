<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // Halaman Kasir / POS
    public function create(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'owner') {
            $stores = Store::where('owner_id', $user->id)->get();
            $storeId = $request->input('store_id', $stores->first()->id ?? null);
        } else {
            $stores = collect();
            $storeId = $user->store_id;
        }

        if (!$storeId) {
            if ($user->role === 'owner') {
                $errorMsg = $stores->isEmpty() 
                    ? 'Belum ada toko yang dibuat. Silakan buat toko terlebih dahulu di menu Kelola Toko.'
                    : 'Silakan pilih toko.';
            } else {
                $errorMsg = 'Anda belum ditugaskan ke toko manapun. Silakan hubungi Owner untuk menugaskan toko aktif Anda.';
            }

            return view('kasir.index', [
                'error' => $errorMsg,
                'categories' => collect(),
                'products' => collect(),
                'customers' => collect(),
                'storeId' => null,
                'stores' => $stores
            ]);
        }

        $store = Store::find($storeId);
        $categories = Category::where('store_id', $storeId)->orderBy('name')->get();
        $products = Product::where('store_id', $storeId)->where('is_active', true)->where('stock', '>', 0)->orderBy('name')->get();
        $customers = Customer::where('store_id', $storeId)->orderBy('name')->get();

        return view('kasir.index', compact('categories', 'products', 'customers', 'storeId', 'stores', 'store'));
    }

    // Simpan Transaksi Baru
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'customer_id' => 'nullable|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
            'paid' => 'required|integer|min:0',
            'payment_method' => 'required|string|in:Cash,QRIS,Transfer',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'tax_percent' => 'nullable|integer|min:0|max:100',
        ]);

        $storeId = $request->store_id;

        // Authorization check
        if ($user->role === 'owner') {
            $store = Store::find($storeId);
            if (!$store || $store->owner_id !== $user->id) {
                return redirect()->back()->withInput()->with('error', 'Anda tidak memiliki akses ke toko ini.');
            }
        } else {
            if ($storeId != $user->store_id) {
                return redirect()->back()->withInput()->with('error', 'Anda tidak memiliki akses ke toko ini.');
            }
        }

        // Customer validation per store
        if ($request->customer_id) {
            $customer = Customer::find($request->customer_id);
            if (!$customer || $customer->store_id != $storeId) {
                return redirect()->back()->withInput()->with('error', 'Pelanggan tidak valid untuk toko ini.');
            }
        }

        $itemsData = $request->items;

        // Aggregate product quantities to handle duplicate product entries safely
        $aggregatedItems = [];
        foreach ($itemsData as $item) {
            $prodId = $item['product_id'];
            $qty = $item['qty'];
            if (isset($aggregatedItems[$prodId])) {
                $aggregatedItems[$prodId] += $qty;
            } else {
                $aggregatedItems[$prodId] = $qty;
            }
        }

        $subtotalPrice = 0;
        $detailsToCreate = [];

        // Validate products and compute subtotal
        foreach ($aggregatedItems as $productId => $qty) {
            $product = Product::findOrFail($productId);
            
            // Ensure product belongs to the store
            if ($product->store_id != $storeId) {
                return redirect()->back()->withInput()->with('error', "Produk {$product->name} tidak valid untuk toko ini.");
            }

            // Ensure product is active
            if (!$product->is_active) {
                return redirect()->back()->withInput()->with('error', "Produk {$product->name} saat ini sedang dinonaktifkan.");
            }

            // Check stock
            if ($product->stock < $qty) {
                return redirect()->back()->withInput()->with('error', "Stok produk {$product->name} tidak mencukupi. Tersisa: {$product->stock}");
            }

            $subtotal = $product->price * $qty;
            $subtotalPrice += $subtotal;

            $detailsToCreate[] = [
                'product_id' => $product->id,
                'quantity' => $qty,
                'price' => $product->price,
                'subtotal' => $subtotal,
            ];
        }

        // Apply discount and tax (PPN)
        $discountPercent = intval($request->input('discount_percent', 0));
        $taxPercent = intval($request->input('tax_percent', 0));

        $discountAmount = intval($subtotalPrice * ($discountPercent / 100));
        $taxAmount = intval(($subtotalPrice - $discountAmount) * ($taxPercent / 100));
        $finalTotalPrice = $subtotalPrice - $discountAmount + $taxAmount;

        $paid = intval($request->paid);
        if ($paid < $finalTotalPrice) {
            return redirect()->back()->withInput()->with('error', 'Jumlah uang pembayaran kurang dari total tagihan.');
        }

        $changeAmount = $paid - $finalTotalPrice;

        $transaction = DB::transaction(function () use ($storeId, $user, $request, $finalTotalPrice, $paid, $changeAmount, $discountPercent, $discountAmount, $taxPercent, $taxAmount, $detailsToCreate) {
            // Generate Invoice Number: TRX-YYYYMMDD-RAND
            $invoiceNumber = 'TRX-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

            // Create Transaction Header
            $tx = Transaction::create([
                'store_id' => $storeId,
                'user_id' => $user->id,
                'customer_id' => $request->customer_id,
                'invoice_number' => $invoiceNumber,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'total_price' => $finalTotalPrice,
                'paid_amount' => $paid,
                'change_amount' => $changeAmount,
                'payment_method' => $request->payment_method,
            ]);

            // Create Transaction Details & Decrement Stock
            foreach ($detailsToCreate as $detail) {
                $detail['transaction_id'] = $tx->id;
                TransactionDetail::create($detail);

                // Decrement product stock
                $product = Product::find($detail['product_id']);
                $product->decrement('stock', $detail['quantity']);
            }

            return $tx;
        });

        return redirect()
            ->route('transactions.show', $transaction->id)
            ->with('success', 'Transaksi berhasil diproses.');
    }

    // Riwayat Transaksi
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'kasir') {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        } else {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
        }

        if ($user->role === 'owner') {
            $stores = Store::where('owner_id', $user->id)->get();
            $storeId = $request->input('store_id');
            
            if (is_null($storeId) && $stores->isNotEmpty()) {
                $storeId = $stores->first()->id;
            }
            
            $query = Transaction::query();
            if ($storeId === 'all') {
                $query->whereIn('store_id', $stores->pluck('id'));
            } elseif ($storeId) {
                $query->where('store_id', $storeId);
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            $stores = collect();
            $storeId = $user->store_id;
            $query = Transaction::where('store_id', $storeId);
        }

        if ($storeId || $user->role === 'owner') {
            if ($startDate) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate) {
                $query->whereDate('created_at', '<=', $endDate);
            }
            $transactions = $query->with(['user', 'customer'])->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $transactions = collect();
        }

        return view('transactions.index', compact('transactions', 'stores', 'storeId', 'startDate', 'endDate'));
    }

    // Ekspor ke CSV (Excel)
    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'kasir') {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        } else {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
        }

        if ($user->role === 'owner') {
            $stores = Store::where('owner_id', $user->id)->get();
            $storeId = $request->input('store_id');
            
            if (is_null($storeId) && $stores->isNotEmpty()) {
                $storeId = $stores->first()->id;
            }
            
            $query = Transaction::query();
            if ($storeId === 'all') {
                $query->whereIn('store_id', $stores->pluck('id'));
            } elseif ($storeId) {
                $query->where('store_id', $storeId);
            } else {
                $query->whereRaw('1 = 0');
            }
        } else {
            $storeId = $user->store_id;
            $query = Transaction::where('store_id', $storeId);
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $transactions = $query->with(['user', 'customer', 'store', 'details.product'])->orderBy('created_at', 'desc')->get();

        $filename = 'Laporan_Penjualan_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($file, [
                'No. Invoice',
                'Toko/Cabang',
                'Kasir',
                'Pelanggan',
                'Metode Pembayaran',
                'Subtotal (Rp)',
                'Potongan Diskon (Rp)',
                'Pajak PPN (Rp)',
                'Total Belanja (Rp)',
                'Uang Bayar (Rp)',
                'Uang Kembali (Rp)',
                'Tanggal Transaksi',
                'Item Terjual (Qty x Produk)'
            ], ';');

            foreach ($transactions as $tx) {
                $subtotal = $tx->details->sum('subtotal');
                $itemsStr = $tx->details->map(function ($det) {
                    return $det->quantity . 'x ' . ($det->product->name ?? 'Menu');
                })->implode(', ');

                fputcsv($file, [
                    $tx->invoice_number,
                    $tx->store->name ?? '-',
                    $tx->user->name ?? '-',
                    $tx->customer->name ?? 'Umum',
                    $tx->payment_method,
                    $subtotal,
                    $tx->discount_amount,
                    $tx->tax_amount,
                    $tx->total_price,
                    $tx->paid_amount,
                    $tx->change_amount,
                    $tx->created_at->format('Y-m-d H:i:s'),
                    $itemsStr
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Halaman Cetak Laporan (PDF)
    public function printReport(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'kasir') {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        } else {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
        }

        if ($user->role === 'owner') {
            $stores = Store::where('owner_id', $user->id)->get();
            $storeId = $request->input('store_id');
            
            if (is_null($storeId) && $stores->isNotEmpty()) {
                $storeId = $stores->first()->id;
            }
            
            $query = Transaction::query();
            if ($storeId === 'all') {
                $query->whereIn('store_id', $stores->pluck('id'));
                $activeStoreName = 'Semua Toko';
            } elseif ($storeId) {
                $query->where('store_id', $storeId);
                $activeStoreName = Store::find($storeId)->name ?? '-';
            } else {
                $query->whereRaw('1 = 0');
                $activeStoreName = '-';
            }
        } else {
            $storeId = $user->store_id;
            $query = Transaction::where('store_id', $storeId);
            $activeStoreName = Store::find($storeId)->name ?? '-';
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $transactions = $query->with(['user', 'customer', 'store', 'details.product'])->orderBy('created_at', 'desc')->get();

        $totalRevenue = $transactions->sum('total_price');
        $totalTransactions = $transactions->count();
        $totalDiscount = $transactions->sum('discount_amount');
        $totalTax = $transactions->sum('tax_amount');

        $paymentStats = $transactions->groupBy('payment_method')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('total_price')
            ];
        });

        return view('transactions.print', compact(
            'transactions',
            'startDate',
            'endDate',
            'activeStoreName',
            'totalRevenue',
            'totalTransactions',
            'totalDiscount',
            'totalTax',
            'paymentStats'
        ));
    }


    // Cetak / Lihat Detail Invoice
    public function show(Transaction $transaction)
    {
        $user = Auth::user();

        // Otorisasi
        if ($user->role === 'owner') {
            if ($transaction->store->owner_id !== $user->id) {
                abort(403);
            }
        } else {
            if ($transaction->store_id !== $user->store_id) {
                abort(403);
            }
        }

        $transaction->load(['store', 'user', 'customer', 'details.product']);

        return view('transactions.show', compact('transaction'));
    }
}
