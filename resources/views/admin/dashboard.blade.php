<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dasbor Admin') }}
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

                // Widget 1: Stok menipis (< 5)
                $lowStock = \App\Models\Product::where('store_id', $storeId)
                    ->where('stock', '<', 5)
                    ->orderBy('stock', 'asc')
                    ->limit(5)
                    ->get();

                // Widget 2: Produk terbaru
                $newProducts = \App\Models\Product::where('store_id', $storeId)
                    ->with('category')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            @endphp

            <!-- Store Profile Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-[10px] uppercase tracking-wider text-indigo-200 font-extrabold">Toko Ditugaskan</span>
                    <h3 class="text-xl font-extrabold">{{ $store->name }}</h3>
                    <p class="text-xs text-indigo-100/80">{{ $store->address ?? 'Alamat belum diatur' }} • Telp: {{ $store->phone ?? '-' }}</p>
                </div>
                <div class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-xl text-xs font-bold font-mono">
                    ADMINISTRATOR
                </div>
            </div>

            <!-- Stats Grid (4 Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Stat 1: Total Produk -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center justify-between transition-all hover:shadow hover:translate-y-[-2px]">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Produk</span>
                        <span class="block text-2xl font-extrabold text-slate-800">{{ $totalProducts }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">🍔</div>
                </div>

                <!-- Stat 2: Total Kategori -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center justify-between transition-all hover:shadow hover:translate-y-[-2px]">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Kategori</span>
                        <span class="block text-2xl font-extrabold text-slate-800">{{ $totalCategories }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg">📁</div>
                </div>

                <!-- Stat 3: Total Pelanggan -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center justify-between transition-all hover:shadow hover:translate-y-[-2px]">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pelanggan</span>
                        <span class="block text-2xl font-extrabold text-slate-800">{{ $totalCustomers }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center font-bold text-lg">👥</div>
                </div>

                <!-- Stat 4: Total Penjualan -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center justify-between transition-all hover:shadow hover:translate-y-[-2px]">
                    <div class="space-y-1">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Penjualan</span>
                        <span class="block text-lg font-extrabold text-indigo-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">💰</div>
                </div>
            </div>

            <!-- Widgets Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Widget 1: Stok Menipis (Left) -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <span>⚠️</span> Stok Menipis
                    </h3>
                    <div class="space-y-3">
                        @forelse($lowStock as $low)
                            <div class="flex items-center justify-between p-3 bg-red-50/40 rounded-xl border border-red-100/30">
                                <span class="font-bold text-slate-800 text-xs truncate max-w-[140px]">{{ $low->name }}</span>
                                <span class="px-2 py-0.5 bg-red-100 text-red-700 font-extrabold text-[10px] rounded">{{ $low->stock }} Porsi</span>
                            </div>
                        @empty
                            <p class="text-slate-400 text-xs text-center py-6">Stok aman terkendali.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Widget 2: Produk Terbaru (Middle) -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <span>🍔</span> Produk Terbaru
                    </h3>
                    <div class="space-y-3">
                        @forelse($newProducts as $new)
                            <div class="flex items-center justify-between p-3 bg-slate-50/50 rounded-xl border border-slate-100/60">
                                <div class="space-y-0.5">
                                    <span class="font-bold text-slate-800 text-xs block leading-tight truncate max-w-[120px]">{{ $new->name }}</span>
                                    <span class="text-[9px] text-slate-400 block font-semibold">{{ $new->category->name ?? '-' }}</span>
                                </div>
                                <span class="font-bold text-slate-800 text-xs">Rp {{ number_format($new->price, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-slate-400 text-xs text-center py-6">Belum ada produk terdaftar.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Widget 3: Transaksi Terbaru (Right) -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <span>🧾</span> Transaksi Terbaru
                    </h3>
                    <div class="space-y-3">
                        @forelse($recentTransactions as $tx)
                            <div class="flex items-center justify-between p-3 bg-slate-50/50 rounded-xl border border-slate-100/60">
                                <div class="space-y-0.5">
                                    <span class="font-bold text-slate-800 text-xs block leading-tight">{{ $tx->invoice_number }}</span>
                                    <span class="text-[9px] text-slate-450 block font-semibold">Oleh: {{ $tx->user->name ?? '-' }}</span>
                                </div>
                                <span class="font-extrabold text-indigo-600 text-xs">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <p class="text-slate-400 text-xs text-center py-6">Belum ada transaksi terjadi.</p>
                        @endforelse
                    </div>
                </div>

            </div>
        @endif
    </div>
</x-app-layout>
