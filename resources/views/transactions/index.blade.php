<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Riwayat Transaksi Penjualan
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">
        <!-- Unified Filter Panel -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
            <form action="{{ route('transactions.index') }}" method="GET" id="report-filter-form" class="space-y-5">
                @if(auth()->user()->role !== 'kasir')
                    <!-- Filter Grid (Visible to Owner & Admin) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 {{ auth()->user()->role === 'owner' ? 'md:grid-cols-4' : 'md:grid-cols-3' }} gap-4">
                        @if(auth()->user()->role === 'owner' && $stores->count() > 0)
                            <div class="space-y-1.5">
                                <label for="store_id" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Toko / Cabang</label>
                                <select name="store_id" id="store_id" class="w-full rounded-xl border-slate-200 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                    <option value="all" {{ $storeId === 'all' ? 'selected' : '' }}>Semua Toko</option>
                                    @foreach($stores as $st)
                                        <option value="{{ $st->id }}" {{ $st->id == $storeId ? 'selected' : '' }}>{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="space-y-1.5">
                            <label for="start_date" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Mulai</label>
                            <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full rounded-xl border-slate-200 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        </div>

                        <div class="space-y-1.5">
                            <label for="end_date" class="text-xs font-bold text-slate-400 uppercase tracking-wider">Tanggal Selesai</label>
                            <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full rounded-xl border-slate-200 text-xs font-semibold text-slate-700 focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        </div>

                        <!-- Preset Buttons -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-400 uppercase tracking-wider">Pilihan Cepat</label>
                            <div class="flex rounded-xl border border-slate-200 p-0.5 bg-slate-50 w-full justify-between h-[42px] items-center" style="height: 42px;">
                                <button type="button" onclick="setPreset('today')" class="flex-1 h-full flex items-center justify-center rounded-lg text-[10px] font-bold transition-all text-slate-650 hover:bg-white hover:text-indigo-600 hover:shadow-sm" style="height: 100%;">Hari Ini</button>
                                <button type="button" onclick="setPreset('month')" class="flex-1 h-full flex items-center justify-center rounded-lg text-[10px] font-bold transition-all text-slate-650 hover:bg-white hover:text-indigo-600 hover:shadow-sm" style="height: 100%;">Bulan Ini</button>
                                <button type="button" onclick="setPreset('all')" class="flex-1 h-full flex items-center justify-center rounded-lg text-[10px] font-bold transition-all text-slate-650 hover:bg-white hover:text-indigo-600 hover:shadow-sm" style="height: 100%;">Semua</button>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Info Alert for Kasir role -->
                    <div class="bg-indigo-50/70 border border-indigo-100/50 rounded-2xl p-4 flex items-center justify-between gap-4">
                        <div class="space-y-1">
                            <span class="text-xs font-extrabold text-indigo-700 uppercase tracking-wider block">Laporan Harian Kasir Aktif</span>
                            <span class="text-xs text-indigo-600 block font-medium">Sesuai kebijakan toko, Anda hanya dapat mengakses, mengekspor, dan mencetak laporan transaksi untuk hari ini: <strong>{{ date('d/m/Y') }}</strong>.</span>
                        </div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-indigo-700 bg-white border border-indigo-100/60 px-3.5 py-2 rounded-xl shrink-0 shadow-sm">
                            Hari Ini
                        </div>
                    </div>
                @endif

                <div class="border-t border-slate-100/80 my-4"></div>

                <!-- Unified Bottom Action Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <!-- Left: Counter info -->
                    <div class="text-xs text-slate-400 font-semibold">
                        Ditemukan: <span class="text-slate-800 font-bold">{{ $transactions instanceof \Illuminate\Pagination\LengthAwarePaginator ? $transactions->total() : count($transactions) }}</span> transaksi
                    </div>
                    
                    <!-- Right: Grouped Buttons -->
                    <div class="flex flex-wrap items-center gap-3">
                        @if(auth()->user()->role !== 'kasir')
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/10 transition-all flex items-center gap-1.5 hover:-translate-y-0.5">
                                🔍 Filter
                            </button>
                            <a href="{{ route('transactions.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all hover:-translate-y-0.5">
                                🔄 Reset
                            </a>
                        @endif
                        <button type="button" onclick="exportData('csv')" class="px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-100 rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-2 hover:-translate-y-0.5">
                            📥 Ekspor Excel
                        </button>
                        <button type="button" onclick="exportData('pdf')" class="px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-100 rounded-xl text-xs font-bold shadow-sm transition-all flex items-center gap-2 hover:-translate-y-0.5">
                            🖨️ Cetak Laporan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Panel -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                            <th class="pb-3">Invoice</th>
                            @if($storeId === 'all')
                                <th class="pb-3">Toko</th>
                            @endif
                            <th class="pb-3">Kasir</th>
                            <th class="pb-3">Pelanggan</th>
                            <th class="pb-3">Metode</th>
                            <th class="pb-3">Total Belanja</th>
                            <th class="pb-3">Tanggal &amp; Waktu</th>
                            <th class="pb-3 text-center" style="text-align: center !important;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                        @forelse($transactions as $tx)
                            <tr>
                                <td class="py-3.5 font-bold text-slate-800">{{ $tx->invoice_number }}</td>
                                @if($storeId === 'all')
                                    <td class="py-3.5 font-semibold text-slate-700">{{ $tx->store->name ?? '-' }}</td>
                                @endif
                                <td class="py-3.5">{{ $tx->user->name ?? '-' }}</td>
                                <td class="py-3.5">{{ $tx->customer->name ?? 'Umum' }}</td>
                                <td class="py-3.5"><span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded-md text-xs font-semibold">{{ $tx->payment_method }}</span></td>
                                <td class="py-3.5 font-bold text-indigo-600">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                <td class="py-3.5 text-xs text-slate-400">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3.5 text-center" style="text-align: center !important;">
                                    <a href="{{ route('transactions.show', $tx->id) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition-all">Lihat Invoice</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $storeId === 'all' ? 8 : 7 }}" class="py-6 text-center text-slate-400">Belum ada riwayat transaksi tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($transactions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="pt-4">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Javascript Helper for Presets and Exports -->
    <script>
        function setPreset(type) {
            const startInput = document.getElementById('start_date');
            const endInput = document.getElementById('end_date');
            const form = document.getElementById('report-filter-form');
            
            const today = new Date();
            const todayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');

            if (type === 'today') {
                startInput.value = todayStr;
                endInput.value = todayStr;
            } else if (type === 'month') {
                const firstDayStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-01';
                startInput.value = firstDayStr;
                endInput.value = todayStr;
            } else if (type === 'all') {
                startInput.value = '';
                endInput.value = '';
            }
            form.submit();
        }

        function exportData(format) {
            const form = document.getElementById('report-filter-form');
            const queryParams = new URLSearchParams(new FormData(form)).toString();
            
            if (format === 'csv') {
                window.location.href = "{{ route('transactions.export.csv') }}?" + queryParams;
            } else if (format === 'pdf') {
                window.open("{{ route('transactions.export.pdf') }}?" + queryParams, '_blank');
            }
        }
    </script>
</x-app-layout>
