<x-app-layout>
@php
    $waLink = null;
    $phone = $transaction->customer?->phone;
    
    if ($phone && $phone !== '-' && strlen($phone) >= 10) {
        // Bersihkan karakter non-numerik
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Ubah awalan 0 menjadi 62
        if (substr($phone, 0, 1) == '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        $storeName = $transaction->store->name ?? 'Kantin';
        $customerName = $transaction->customer?->name ?? 'Pelanggan';
        $invoice = $transaction->invoice_number;
        $date = $transaction->created_at->format('d/m/Y H:i');
        
        // Susun Pesan WA
        $msg = "*STRUK PEMBELIAN*\n";
        $msg .= "_{$storeName}_\n\n";
        $msg .= "Halo kak *{$customerName}*,\n";
        $msg .= "Terima kasih telah berbelanja! Berikut rincian pesanan Anda:\n\n";
        
        $msg .= "No. Invoice: {$invoice}\n";
        $msg .= "Tanggal: {$date}\n";
        $msg .= "------------------------\n";
        
        foreach($transaction->details as $det) {
            $prodName = $det->product->name ?? 'Menu';
            $qty = $det->quantity;
            $price = number_format($det->price, 0, ',', '.');
            $sub = number_format($det->subtotal, 0, ',', '.');
            $msg .= "{$prodName}\n";
            $msg .= "{$qty} x Rp {$price} = Rp {$sub}\n";
        }
        
        $msg .= "------------------------\n";
        $msg .= "*Total : Rp " . number_format($transaction->total_price, 0, ',', '.') . "*\n";
        $msg .= "Bayar : Rp " . number_format($transaction->paid_amount, 0, ',', '.') . "\n";
        $msg .= "Kembali: Rp " . number_format($transaction->change_amount, 0, ',', '.') . "\n\n";
        $msg .= "_Simpan struk ini sebagai bukti pembayaran yang sah._";
        
        $encodedMsg = rawurlencode($msg);
        $waLink = "https://wa.me/{$phone}?text={$encodedMsg}";
    }
@endphp
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Detail Invoice: {{ $transaction->invoice_number }}
            </h2>
            <div class="inline-flex items-center gap-2">
                @if($waLink)
                <a href="{{ $waLink }}" target="_blank" class="px-4 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 rounded-xl text-xs font-semibold shadow-sm transition-all print:hidden flex items-center gap-1 border border-emerald-200">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.347-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.876 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    Kirim WA
                </a>
                @endif
                <button onclick="window.print()" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl text-xs font-semibold shadow-sm transition-all print:hidden">
                    🖨️ Cetak Resi
                </button>
                <a href="{{ route('kasir') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-all print:hidden">
                    Kembali ke Kasir
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Invoice Mockup Card -->
    <div id="print-area" class="py-6 flex justify-center">
        <div class="w-full max-w-lg bg-white rounded-2xl border border-slate-200/60 shadow-lg p-8 space-y-6 relative overflow-hidden font-mono text-sm text-slate-800">
            <!-- Receipt Header -->
            <div class="text-center space-y-2">
                <h3 class="text-xl font-bold tracking-wider uppercase text-slate-900">{{ $transaction->store->name ?? '-' }}</h3>
                <p class="text-xs text-slate-500 font-sans">{{ $transaction->store->address ?? 'Alamat toko' }}</p>
                <p class="text-xs text-slate-500 font-sans">Telp: {{ $transaction->store->phone ?? '-' }}</p>
                <div class="border-b border-dashed border-slate-200 pt-4"></div>
            </div>

            <!-- Receipt Info metadata -->
            <div class="grid grid-cols-2 gap-3 text-xs font-sans">
                <div class="space-y-1">
                    <span class="text-slate-400 block">No. Invoice:</span>
                    <span class="font-bold text-slate-700 block">{{ $transaction->invoice_number }}</span>
                </div>
                <div class="space-y-1 text-right">
                    <span class="text-slate-400 block">Tanggal/Waktu:</span>
                    <span class="font-bold text-slate-700 block">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="space-y-1">
                    <span class="text-slate-400 block">Kasir:</span>
                    <span class="font-bold text-slate-700 block">{{ $transaction->user->name ?? '-' }}</span>
                </div>
                <div class="space-y-1 text-right">
                    <span class="text-slate-400 block">Pelanggan:</span>
                    <span class="font-bold text-slate-700 block">{{ $transaction->customer->name ?? 'Umum' }}</span>
                </div>
            </div>

            <div class="border-b border-dashed border-slate-200"></div>

            <!-- Items Purchased list -->
            <div class="space-y-4 font-mono">
                <div class="grid grid-cols-3 font-bold border-b border-slate-100 pb-2 text-slate-600 text-xs">
                    <span class="col-span-1">Item</span>
                    <span class="text-center">Qty x Harga</span>
                    <span class="text-right">Total</span>
                </div>

                @foreach($transaction->details as $det)
                    <div class="grid grid-cols-3 items-center text-xs">
                        <span class="col-span-1 font-semibold text-slate-800">{{ $det->product->name ?? 'Menu' }}</span>
                        <span class="text-center text-slate-500">{{ $det->quantity }} x {{ number_format($det->price, 0, ',', '.') }}</span>
                        <span class="text-right font-bold text-slate-800">Rp {{ number_format($det->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="border-b border-dashed border-slate-200"></div>

            <!-- Total Calculations -->
            @php
                $subtotal = $transaction->details->sum('subtotal');
            @endphp
            <div class="space-y-2 text-xs font-sans">
                <div class="flex justify-between font-medium text-slate-500">
                    <span>Subtotal</span>
                    <span class="font-mono">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                @if($transaction->discount_amount > 0)
                    <div class="flex justify-between font-medium text-red-500">
                        <span>Potongan Diskon ({{ $transaction->discount_percent }}%)</span>
                        <span class="font-mono">- Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($transaction->tax_amount > 0)
                    <div class="flex justify-between font-medium text-slate-500">
                        <span>Pajak (PPN {{ $transaction->tax_percent }}%)</span>
                        <span class="font-mono">+ Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-extrabold text-base text-slate-900 border-t border-slate-100 pt-2">
                    <span>TOTAL</span>
                    <span class="font-mono text-indigo-600 text-lg">Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-medium text-slate-500 pt-1">
                    <span>Bayar ({{ $transaction->payment_method }})</span>
                    <span class="font-mono text-slate-700">Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-slate-800">
                    <span>Kembalian</span>
                    <span class="font-mono text-emerald-600">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="border-b border-dashed border-slate-200"></div>

            <!-- Receipt Footer message -->
            <div class="text-center space-y-1 pt-2">
                <h5 class="font-bold text-slate-700 text-xs font-sans">Terima Kasih Atas Kunjungan Anda</h5>
                <p class="text-[10px] text-slate-400 font-sans">Silakan simpan resi pembayaran ini sebagai bukti transaksi sah.</p>
            </div>
        </div>
    </div>

    <!-- Styles for Printing Invoice -->
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #print-area, #print-area * {
                visibility: visible;
            }
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                display: flex !important;
                justify-content: center;
            }
            #print-area > div {
                box-shadow: none !important;
                border: none !important;
                background: transparent !important;
                padding: 0 !important;
                max-width: 100% !important;
            }
            .print\:hidden {
                display: none !important;
            }
        }
    </style>
</x-app-layout>
