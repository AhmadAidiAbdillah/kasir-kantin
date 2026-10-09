<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan_Penjualan_{{ date('Ymd_His') }}</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (via CDN for standalone print layout) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fff;
            color: #1e293b;
        }
        @media print {
            body {
                background-color: #fff;
                color: #000;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: A4 landscape;
                margin: 1.5cm;
            }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 antialiased p-8">

    <!-- Top floating navigation bar (Hidden when printing) -->
    <div class="max-w-7xl mx-auto mb-8 no-print flex items-center justify-between bg-white border border-slate-100 shadow-sm px-6 py-4 rounded-2xl">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 animate-pulse"></span>
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">Pratinjau Cetak Laporan</span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/10 transition-all flex items-center gap-2">
                🖨️ Cetak / Simpan PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all">
                Tutup Halaman
            </button>
        </div>
    </div>

    <!-- Main Printable Document Container -->
    <div class="max-w-7xl mx-auto bg-white p-10 border border-slate-100 shadow-sm rounded-3xl print:border-none print:shadow-none print:p-0 space-y-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between border-b border-slate-150/80 pb-6 gap-4">
            <div class="space-y-1">
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">LAPORAN PENJUALAN KASIR KANTIN</h1>
                <p class="text-xs text-slate-450 font-semibold">Toko: <span class="text-slate-800 font-bold">{{ $activeStoreName }}</span></p>
                <p class="text-xs text-slate-450 font-semibold">
                    Periode: 
                    <span class="text-slate-800 font-bold">
                        @if($startDate && $endDate)
                            {{ date('d/m/Y', strtotime($startDate)) }} s.d {{ date('d/m/Y', strtotime($endDate)) }}
                        @elseif($startDate)
                            Mulai {{ date('d/m/Y', strtotime($startDate)) }}
                        @elseif($endDate)
                            s.d {{ date('d/m/Y', strtotime($endDate)) }}
                        @else
                            Semua Waktu
                        @endif
                    </span>
                </p>
            </div>
            <div class="text-right space-y-1">
                <div class="flex items-center gap-2 md:justify-end">
                    <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-extrabold text-base">K</div>
                    <span class="text-sm font-extrabold tracking-tight text-slate-900">Kasir<span class="text-indigo-600">Kantin</span></span>
                </div>
                <p class="text-[10px] text-slate-400 font-mono">Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
            </div>
        </div>

        <!-- Summary Metrics Dashboard -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Metric 1: Total Pendapatan -->
            <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-5 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pendapatan (Omzet)</span>
                <span class="block text-xl font-extrabold text-indigo-600 font-mono">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </div>
            <!-- Metric 2: Total Transaksi -->
            <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-5 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jumlah Transaksi</span>
                <span class="block text-xl font-extrabold text-slate-800 font-mono">{{ $totalTransactions }}</span>
            </div>
            <!-- Metric 3: Total Diskon -->
            <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-5 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Diskon Diberikan</span>
                <span class="block text-xl font-extrabold text-rose-500 font-mono">Rp {{ number_format($totalDiscount, 0, ',', '.') }}</span>
            </div>
            <!-- Metric 4: Total PPN -->
            <div class="bg-slate-50/50 border border-slate-100 rounded-2xl p-5 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pajak PPN</span>
                <span class="block text-xl font-extrabold text-slate-700 font-mono">Rp {{ number_format($totalTax, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Payment Methods breakdown -->
        <div class="space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rincian Metode Pembayaran</h3>
            <div class="flex flex-wrap gap-4">
                @foreach(['Cash' => '💵 Cash / Tunai', 'QRIS' => '📱 QRIS', 'Transfer' => '🏦 Transfer Bank'] as $key => $label)
                    @php
                        $stat = $paymentStats->get($key, ['count' => 0, 'total' => 0]);
                    @endphp
                    <div class="flex-1 min-w-[200px] bg-slate-50/30 border border-slate-100 rounded-xl p-4 flex items-center justify-between">
                        <div class="space-y-0.5">
                            <span class="text-xs font-bold text-slate-800">{{ $label }}</span>
                            <span class="block text-[10px] text-slate-400 font-semibold">{{ $stat['count'] }} Transaksi</span>
                        </div>
                        <span class="text-xs font-extrabold text-slate-900 font-mono">Rp {{ number_format($stat['total'], 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Detailed Transactions Table -->
        <div class="space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Daftar Transaksi Terperinci</h3>
            <div class="overflow-x-auto border border-slate-100 rounded-2xl">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50/65 border-b border-slate-100 text-slate-500 font-bold uppercase tracking-wider">
                            <th class="py-3 px-4">Invoice</th>
                            <th class="py-3 px-4">Toko</th>
                            <th class="py-3 px-4">Kasir</th>
                            <th class="py-3 px-4">Pelanggan</th>
                            <th class="py-3 px-4">Metode</th>
                            <th class="py-3 px-4">Item Terjual</th>
                            <th class="py-3 px-4">Total Belanja</th>
                            <th class="py-3 px-4 text-right">Tanggal/Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-600">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-slate-50/40">
                                <td class="py-3 px-4 font-bold text-slate-800">{{ $tx->invoice_number }}</td>
                                <td class="py-3 px-4 font-semibold">{{ $tx->store->name ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $tx->user->name ?? '-' }}</td>
                                <td class="py-3 px-4">{{ $tx->customer->name ?? 'Umum' }}</td>
                                <td class="py-3 px-4 font-semibold text-slate-700">{{ $tx->payment_method }}</td>
                                <td class="py-3 px-4 font-sans text-[11px] leading-relaxed">
                                    {{ $tx->details->map(fn($d) => $d->quantity . 'x ' . ($d->product->name ?? 'Menu'))->implode(', ') }}
                                </td>
                                <td class="py-3 px-4 font-bold text-slate-900 font-mono">Rp {{ number_format($tx->total_price, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-mono">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-8 text-center text-slate-400">Tidak ada transaksi yang cocok untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer for signature -->
        <div class="pt-8 border-t border-slate-100 flex justify-between items-center text-[10px] text-slate-450 font-semibold font-mono">
            <span>KasirKantin Laporan Sistem Penjualan Terkomputerisasi</span>
            <span>Halaman 1 dari 1</span>
        </div>

    </div>

    <!-- Automatically open browser print dialog after fully loaded -->
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Delay print briefly to ensure styles are applied
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
