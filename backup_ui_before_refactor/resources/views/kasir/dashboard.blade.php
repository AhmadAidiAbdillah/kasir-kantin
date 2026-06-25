<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Kasir') }}
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
            <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-2xl p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <span class="text-xs uppercase tracking-wider text-emerald-200 font-bold">Toko Aktif Anda</span>
                    <h3 class="text-2xl font-extrabold">{{ $store->name }}</h3>
                    <p class="text-xs text-emerald-100">{{ $store->address ?? 'Alamat belum diisi' }}</p>
                </div>
                <a href="{{ route('kasir') }}" class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3.5 bg-white text-emerald-700 hover:bg-slate-50 rounded-xl font-bold text-sm shadow-md transition-all">
                    🛒 Buka Kasir / Transaksi Baru
                </a>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Stat 1 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Transaksi Saya Hari Ini</span>
                        <span class="block text-3xl font-extrabold text-slate-800">{{ $todayTransactionsCount }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        🧾
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Omset Penjualan Saya Hari Ini</span>
                        <span class="block text-3xl font-extrabold text-emerald-600">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl font-bold">
                        💰
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Table -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <h3 class="text-lg font-bold text-slate-800 font-sans">Riwayat Transaksi Saya</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="pb-3">Invoice</th>
                                <th class="pb-3">Pelanggan</th>
                                <th class="pb-3">Metode Pembayaran</th>
                                <th class="pb-3">Total Belanja</th>
                                <th class="pb-3 text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                            @forelse($recentTransactions as $tx)
                                <tr>
                                    <td class="py-3.5 font-bold text-slate-800">{{ $tx->invoice_number }}</td>
                                    <td class="py-3.5">{{ $tx->customer->name ?? '-' }}</td>
                                    <td class="py-3.5 text-xs font-medium"><span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md">{{ $tx->payment_method }}</span></td>
                                    <td class="py-3.5 font-bold text-emerald-600">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                    <td class="py-3.5 text-right text-xs text-slate-400">{{ $tx->created_at->format('H:i') }}</td>
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
        @endif
    </div>
</x-app-layout>
