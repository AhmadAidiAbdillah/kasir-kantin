<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Owner') }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
            <!-- Stat 1 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Toko</span>
                    <span class="block text-3xl font-extrabold text-slate-800">{{ $totalStores }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                    🏪
                </div>
            </div>

            <!-- Stat 2 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Staff</span>
                    <span class="block text-3xl font-extrabold text-slate-800">{{ $totalUsers }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl font-bold">
                    👥
                </div>
            </div>

            <!-- Stat 3 -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex items-center justify-between">
                <div class="space-y-1">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Produk</span>
                    <span class="block text-3xl font-extrabold text-slate-800">{{ $totalProducts }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-xl font-bold">
                    🍔
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
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pendapatan</span>
                    <span class="block text-xl font-extrabold text-indigo-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-xl font-bold">
                    💰
                </div>
            </div>
        </div>

        <!-- Detail Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Toko List (Kiri: 2 Kolom) -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-800">Daftar Toko &amp; Cabang</h3>
                    <a href="{{ route('owner.stores.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 shadow shadow-indigo-600/10 transition-all">
                        + Tambah Toko
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="pb-3">Nama Toko</th>
                                <th class="pb-3">Telepon</th>
                                <th class="pb-3">Alamat</th>
                                <th class="pb-3 text-center">Produk</th>
                                <th class="pb-3 text-center">Staff</th>
                                <th class="pb-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                            @forelse($stores as $st)
                                <tr>
                                    <td class="py-3.5 font-semibold text-slate-800">{{ $st->name }}</td>
                                    <td class="py-3.5">{{ $st->phone ?? '-' }}</td>
                                    <td class="py-3.5">{{ $st->address ?? '-' }}</td>
                                    <td class="py-3.5 text-center"><span class="px-2 py-1 bg-sky-50 text-sky-700 rounded-md font-semibold text-xs">{{ $st->products_count }}</span></td>
                                    <td class="py-3.5 text-center"><span class="px-2 py-1 bg-purple-50 text-purple-700 rounded-md font-semibold text-xs">{{ $st->users_count }}</span></td>
                                    <td class="py-3.5 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            <a href="{{ route('owner.stores.edit', $st->id) }}" class="text-xs text-indigo-600 hover:underline">Edit</a>
                                            <form action="{{ route('owner.stores.destroy', $st->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus toko ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-500 hover:underline">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-6 text-center text-slate-400">Belum ada toko yang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Transaksi Terakhir (Kanan: 1 Kolom) -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
                <h3 class="text-lg font-bold text-slate-800">Transaksi Terbaru</h3>
                <div class="space-y-4">
                    @forelse($recentTransactions as $tx)
                        <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100/70 transition-colors">
                            <div class="space-y-1">
                                <span class="font-bold text-slate-800 text-sm block">{{ $tx->invoice_number }}</span>
                                <span class="text-xs text-slate-400 block">{{ $tx->store->name ?? '-' }} • {{ $tx->user->name ?? '-' }}</span>
                            </div>
                            <div class="text-right space-y-1">
                                <span class="font-extrabold text-indigo-600 text-sm block">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $tx->created_at->format('d/m H:i') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-400 text-sm text-center py-6">Belum ada transaksi terjadi.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
