<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dasbor Operator Kasir') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">
        @if(isset($error))
            <div class="bg-amber-50 border border-amber-200 text-amber-800 p-5 rounded-2xl">
                <h4 class="font-bold text-base mb-1">Pemberitahuan</h4>
                <p class="text-sm">{{ $error }}</p>
            </div>
        @else
            @php
                $storeId = auth()->user()->store_id;

                // Widget: Produk Terlaris (Favorit) di Toko Ini
                $favProducts = \App\Models\TransactionDetail::selectRaw('product_id, SUM(quantity) as total_qty')
                    ->whereHas('transaction', function ($query) use ($storeId) {
                        $query->where('store_id', $storeId);
                    })
                    ->groupBy('product_id')
                    ->orderBy('total_qty', 'desc')
                    ->with('product.category')
                    ->limit(5)
                    ->get();
            @endphp

            <!-- Store Banner -->
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <span class="text-[10px] uppercase tracking-wider text-emerald-200 font-extrabold">Unit Kantin Aktif</span>
                    <h3 class="text-xl font-extrabold">{{ $store->name }}</h3>
                    <p class="text-xs text-emerald-100/80">{{ $store->address ?? 'Alamat toko' }}</p>
                </div>
                <a href="{{ route('kasir') }}" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3.5 bg-white text-emerald-700 hover:bg-slate-50 rounded-xl font-bold text-sm shadow-md transition-all hover:-translate-y-0.5">
                    🛒 Transaksi Baru (POS)
                </a>
            </div>

            <!-- Stats Grid (2 Cards) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Stat 1: Transaksi Hari Ini -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center justify-between transition-all hover:shadow hover:translate-y-[-2px]">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Transaksi Saya Hari Ini</span>
                        <span class="block text-3xl font-extrabold text-slate-800">{{ $todayTransactionsCount }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">🧾</div>
                </div>

                <!-- Stat 2: Omset Hari Ini -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center justify-between transition-all hover:shadow hover:translate-y-[-2px]">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Omset Penjualan Hari Ini</span>
                        <span class="block text-3xl font-extrabold text-emerald-600">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center font-bold text-lg">💰</div>
                </div>
            </div>

            <!-- Detail Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Left: Riwayat Transaksi Saya (2 Columns) -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <span>🧾</span> Riwayat Transaksi Saya Hari Ini
                    </h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                    <th class="pb-3">Invoice</th>
                                    <th class="pb-3">Pelanggan</th>
                                    <th class="pb-3">Metode</th>
                                    <th class="pb-3">Total Belanja</th>
                                    <th class="pb-3 text-right">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                                @forelse($recentTransactions as $tx)
                                    <tr>
                                        <td class="py-3 font-bold text-slate-800">
                                            <a href="{{ route('transactions.show', $tx->id) }}" class="hover:text-indigo-600 hover:underline">{{ $tx->invoice_number }}</a>
                                        </td>
                                        <td class="py-3">{{ $tx->customer->name ?? 'Umum' }}</td>
                                        <td class="py-3"><span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-xs font-semibold">{{ $tx->payment_method }}</span></td>
                                        <td class="py-3 font-bold text-emerald-650">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                        <td class="py-3 text-right text-xs text-slate-400">{{ $tx->created_at->format('H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-slate-400">Belum ada transaksi yang Anda proses hari ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right: Shortcut & Produk Favorit (1 Column) -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                    <!-- Shortcut Transaksi Baru -->
                    <div class="space-y-3">
                        <h3 class="text-base font-bold text-slate-800">Menu Kasir Cepat</h3>
                        <a href="{{ route('kasir') }}" class="flex items-center justify-between p-4 bg-emerald-50/50 hover:bg-emerald-50 rounded-xl border border-emerald-100/50 transition-colors">
                            <div class="flex items-center gap-2">
                                <span class="text-xl">🛒</span>
                                <span class="text-xs font-bold text-slate-700">Mulai Transaksi POS Baru</span>
                            </div>
                            <span class="text-emerald-600 font-bold">→</span>
                        </a>
                    </div>

                    <!-- Produk Favorit Toko -->
                    <div class="space-y-4 pt-4 border-t border-slate-100">
                        <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                            <span>🔥</span> Produk Favorit Toko
                        </h3>
                        <div class="space-y-3">
                            @forelse($favProducts as $fav)
                                <div class="flex items-center justify-between p-3 bg-slate-50/50 rounded-xl border border-slate-150/40">
                                    <div class="space-y-0.5">
                                        <span class="font-bold text-slate-800 text-xs block truncate max-w-[130px]">{{ $fav->product->name ?? 'Menu Dihapus' }}</span>
                                        <span class="text-[9px] text-slate-400 block font-semibold">{{ $fav->product->category->name ?? '-' }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 font-extrabold text-[10px] rounded">{{ $fav->total_qty }} Sold</span>
                                </div>
                            @empty
                                <p class="text-slate-400 text-xs text-center py-6">Belum ada produk favorit.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </div>
</x-app-layout>
