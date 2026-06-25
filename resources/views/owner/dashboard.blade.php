<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dasbor Owner') }}
        </h2>
    </x-slot>

    @php
        $user = auth()->user();
        $ownerStoreIds = \App\Models\Store::where('owner_id', $user->id)->pluck('id');

        // Mengambil data grafik penjualan 7 hari terakhir secara dinamis
        $salesLast7Days = \App\Models\Transaction::selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->whereIn('store_id', $ownerStoreIds)
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = date('Y-m-d', strtotime("-$i days"));
            $dateLabel = date('d M', strtotime("-$i days"));
            $chartLabels[] = $dateLabel;
            
            $daySale = $salesLast7Days->firstWhere('date', $dateStr);
            $chartData[] = $daySale ? (int) $daySale->total : 0;
        }

        // Produk Terlaris (Top Selling Products)
        $topProducts = \App\Models\TransactionDetail::selectRaw('product_id, SUM(quantity) as total_qty')
            ->whereHas('product', function ($query) use ($ownerStoreIds) {
                $query->whereIn('store_id', $ownerStoreIds);
            })
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->with('product.store')
            ->limit(5)
            ->get();

        // Produk Stok Menipis (Stok < 5)
        $lowStockProducts = \App\Models\Product::whereIn('store_id', $ownerStoreIds)
            ->where('stock', '<', 5)
            ->with(['store', 'category'])
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();
    @endphp

    <div class="py-6 space-y-6">
        
        <!-- Stats Grid Horizontal (5 Cards) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
            <!-- Stat 1: Total Toko -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center justify-between transition-all hover:shadow hover:translate-y-[-2px]">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Toko</span>
                    <span class="block text-2xl font-extrabold text-slate-800">{{ $totalStores }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">🏪</div>
            </div>

            <!-- Stat 2: Total Staff -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center justify-between transition-all hover:shadow hover:translate-y-[-2px]">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Staff</span>
                    <span class="block text-2xl font-extrabold text-slate-800">{{ $totalUsers }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-lg">👥</div>
            </div>

            <!-- Stat 3: Total Produk -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center justify-between transition-all hover:shadow hover:translate-y-[-2px]">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Produk</span>
                    <span class="block text-2xl font-extrabold text-slate-800">{{ $totalProducts }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center font-bold text-lg">🍔</div>
            </div>

            <!-- Stat 4: Total Transaksi -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center justify-between transition-all hover:shadow hover:translate-y-[-2px]">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Transaksi</span>
                    <span class="block text-2xl font-extrabold text-slate-800">{{ $totalTransactions }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-lg">🧾</div>
            </div>

            <!-- Stat 5: Total Pendapatan -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex items-center justify-between transition-all hover:shadow hover:translate-y-[-2px]">
                <div class="space-y-1">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pendapatan</span>
                    <span class="block text-lg font-extrabold text-indigo-600 truncate">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center font-bold text-lg">💰</div>
            </div>
        </div>

        <!-- Sales Chart Section -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <span>📈</span> Grafik Penjualan 7 Hari Terakhir
            </h3>
            <div class="h-64 relative">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Panel: Toko & Cabang -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-800">Daftar Toko &amp; Cabang</h3>
                    <a href="{{ route('owner.stores.create') }}" class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow transition-colors">
                        + Tambah Toko
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="pb-3">Nama Toko</th>
                                <th class="pb-3">WhatsApp</th>
                                <th class="pb-3">Alamat</th>
                                <th class="pb-3 text-center">Produk</th>
                                <th class="pb-3 text-center">Staf</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                            @forelse($stores as $st)
                                <tr>
                                    <td class="py-3 font-semibold text-slate-800">
                                        <a href="{{ route('owner.stores.edit', $st->id) }}" class="hover:text-indigo-600 hover:underline">{{ $st->name }}</a>
                                    </td>
                                    <td class="py-3">{{ $st->phone ?? '-' }}</td>
                                    <td class="py-3 text-xs">{{ $st->address ?? '-' }}</td>
                                    <td class="py-3 text-center"><span class="px-2 py-0.5 bg-sky-50 text-sky-700 rounded text-xs font-semibold">{{ $st->products_count }}</span></td>
                                    <td class="py-3 text-center"><span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded text-xs font-semibold">{{ $st->users_count }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">Belum ada cabang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right Panel: Best Selling Products -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <span>🔥</span> Produk Terlaris
                </h3>
                <div class="space-y-3.5">
                    @forelse($topProducts as $top)
                        <div class="flex items-center justify-between p-3 bg-slate-50/55 rounded-xl border border-slate-100/50">
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-800 text-xs block leading-tight">{{ $top->product->name ?? 'Menu Dihapus' }}</span>
                                <span class="text-[9px] text-slate-400 block font-semibold">{{ $top->product->store->name ?? '-' }}</span>
                            </div>
                            <span class="px-2 py-1 bg-indigo-50 text-indigo-700 font-extrabold text-[10px] rounded-lg">{{ $top->total_qty }} Terjual</span>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs text-center py-6">Belum ada produk terjual.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Second Info Grid: Low Stock & Recent Transaksis -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Low Stock Widget (Kiri: 1 Kolom) -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <span>⚠️</span> Produk Stok Menipis
                </h3>
                <div class="space-y-3.5">
                    @forelse($lowStockProducts as $low)
                        <div class="flex items-center justify-between p-3 bg-red-50/40 rounded-xl border border-red-100/30">
                            <div class="space-y-0.5">
                                <span class="font-bold text-slate-800 text-xs block leading-tight">{{ $low->name }}</span>
                                <span class="text-[9px] text-slate-400 block font-semibold">{{ $low->store->name ?? '-' }}</span>
                            </div>
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 font-extrabold text-[10px] rounded">{{ $low->stock }} Porsi</span>
                        </div>
                    @empty
                        <p class="text-slate-400 text-xs text-center py-6">Stok produk aman.</p>
                    @endforelse
                </div>
            </div>

            <!-- Recent Transactions Widget (Kanan: 2 Kolom) -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <span>🧾</span> Transaksi Terbaru
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="pb-2">Invoice</th>
                                <th class="pb-2">Toko</th>
                                <th class="pb-2">Kasir</th>
                                <th class="pb-2">Total Belanja</th>
                                <th class="pb-2 text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-xs text-slate-600">
                            @forelse($recentTransactions as $tx)
                                <tr>
                                    <td class="py-2.5 font-bold text-slate-800">{{ $tx->invoice_number }}</td>
                                    <td class="py-2.5">{{ $tx->store->name ?? '-' }}</td>
                                    <td class="py-2.5">{{ $tx->user->name ?? '-' }}</td>
                                    <td class="py-2.5 font-bold text-indigo-600">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                    <td class="py-2.5 text-right text-slate-450">{{ $tx->created_at->format('d/m H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">Belum ada transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js Script CDN Loading & Setup -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: {!! json_encode($chartData) !!},
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.05)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.3,
                        pointRadius: 4,
                        pointBackgroundColor: '#4f46e5'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: 50000,
                            ticks: {
                                precision: 0,
                                callback: function(value) {
                                    return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                },
                                font: {
                                    size: 10,
                                    family: "'Plus Jakarta Sans', sans-serif"
                                }
                            },
                            grid: {
                                color: 'rgba(148, 163, 184, 0.08)'
                            }
                        },
                        x: {
                            ticks: {
                                font: {
                                    size: 10,
                                    family: "'Plus Jakarta Sans', sans-serif"
                                }
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
