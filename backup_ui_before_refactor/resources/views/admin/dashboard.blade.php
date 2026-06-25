<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">
        @if(isset($error))
            <div class="bg-amber-50 border border-amber-200 text-amber-800 p-5 rounded-2xl">
                <h4 class="font-bold text-base mb-1">Pemberitahuan</h4>
                <p class="text-sm">{{ $error }}</p>
            </div>
        @else
            <!-- Store Profile info banner -->
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs uppercase tracking-wider text-indigo-200 font-bold">Toko Aktif Anda</span>
                    <h3 class="text-2xl font-extrabold">{{ $store->name }}</h3>
                    <p class="text-xs text-indigo-100">{{ $store->address ?? 'Alamat belum diisi' }} • Telp: {{ $store->phone ?? '-' }}</p>
                </div>
                <div class="px-4 py-2 bg-white/10 backdrop-blur-md rounded-xl text-xs font-semibold">
                    Role: Administrator
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <!-- Stat 1 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Kategori</span>
                        <span class="block text-3xl font-extrabold text-slate-800">{{ $totalCategories }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold">
                        📁
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Produk</span>
                        <span class="block text-3xl font-extrabold text-slate-800">{{ $totalProducts }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl font-bold">
                        🍔
                    </div>
                </div>

                <!-- Stat 3 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pelanggan</span>
                        <span class="block text-3xl font-extrabold text-slate-800">{{ $totalCustomers }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center text-xl font-bold">
                        👥
                    </div>
                </div>

                <!-- Stat 4 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Transaksi</span>
                        <span class="block text-3xl font-extrabold text-slate-800">{{ $totalTransactions }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        🧾
                    </div>
                </div>

                <!-- Stat 5 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Omset Toko</span>
                        <span class="block text-xl font-extrabold text-indigo-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl font-bold">
                        💰
                    </div>
                </div>
            </div>

            <!-- Detail Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Transaksi Terakhir (Kiri: 2 Kolom) -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-800">Transaksi Toko Terbaru</h3>
                        <a href="{{ route('kasir') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow shadow-indigo-600/10 transition-all">
                            Buka Transaksi Kasir
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                    <th class="pb-3">Invoice</th>
                                    <th class="pb-3">Kasir</th>
                                    <th class="pb-3">Pelanggan</th>
                                    <th class="pb-3">Total Belanja</th>
                                    <th class="pb-3 text-right">Tanggal/Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                                @forelse($recentTransactions as $tx)
                                    <tr>
                                        <td class="py-3.5 font-bold text-slate-800">{{ $tx->invoice_number }}</td>
                                        <td class="py-3.5">{{ $tx->user->name ?? '-' }}</td>
                                        <td class="py-3.5">{{ $tx->customer->name ?? '-' }}</td>
                                        <td class="py-3.5 font-semibold text-indigo-600">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                        <td class="py-3.5 text-right text-xs text-slate-400">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-6 text-center text-slate-400">Belum ada transaksi di toko ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Quick Links & Actions (Kanan: 1 Kolom) -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                    <h3 class="text-lg font-bold text-slate-800">Tautan Cepat</h3>
                    <div class="flex flex-col gap-3">
                        <a href="{{ route('products.index') }}" class="flex items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                            <span class="text-sm font-semibold text-slate-700">Kelola Menu &amp; Stok</span>
                            <span class="text-indigo-600 font-bold">🍔 →</span>
                        </a>
                        <a href="{{ route('categories.index') }}" class="flex items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                            <span class="text-sm font-semibold text-slate-700">Kelola Kategori Menu</span>
                            <span class="text-indigo-600 font-bold">📁 →</span>
                        </a>
                        <a href="{{ route('customers.index') }}" class="flex items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-colors">
                            <span class="text-sm font-semibold text-slate-700">Kelola Data Pelanggan</span>
                            <span class="text-indigo-600 font-bold">👥 →</span>
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
