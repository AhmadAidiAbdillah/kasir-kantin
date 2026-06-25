<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kelola Menu &amp; Stok Produk
            </h2>
            <div class="inline-flex items-center gap-4">
                @if(auth()->user()->role === 'owner' && $stores->count() > 0)
                    <form action="{{ route('products.index') }}" method="GET" id="store-filter-form">
                        <select name="store_id" onchange="document.getElementById('store-filter-form').submit()" class="rounded-xl border-slate-200 text-xs font-semibold text-slate-700">
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}" {{ $st->id == $storeId ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif
                @if($storeId)
                    <a href="{{ route('products.create', ['store_id' => $storeId]) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow shadow-indigo-600/10 transition-all">
                        + Tambah Produk
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
        @if(!$storeId)
            <p class="text-slate-400 text-sm text-center py-12">
                @if(auth()->user()->role === 'owner')
                    @if($stores->isEmpty())
                        Belum ada toko yang dibuat. Silakan buat toko terlebih dahulu di menu Kelola Toko.
                    @else
                        Silakan pilih toko.
                    @endif
                @else
                    Anda belum ditugaskan ke toko manapun. Silakan hubungi Owner untuk menugaskan toko aktif Anda.
                @endif
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                            <th class="pb-3">Nama Produk</th>
                            <th class="pb-3">Kategori</th>
                            <th class="pb-3">Harga Jual</th>
                            <th class="pb-3 text-center">Stok</th>
                            <th class="pb-3 text-center">Status</th>
                            <th class="pb-3 text-center" style="text-align: center !important;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                        @forelse($products as $prod)
                            <tr>
                                <td class="py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 font-bold overflow-hidden shrink-0">
                                            @if($prod->image)
                                                <img src="{{ asset('storage/' . $prod->image) }}" class="w-full h-full object-cover">
                                            @else
                                                🍔
                                            @endif
                                        </div>
                                        <span class="font-semibold text-slate-800">{{ $prod->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5"><span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-xs font-semibold">{{ $prod->category->name ?? '-' }}</span></td>
                                <td class="py-3.5 font-bold text-slate-900">Rp {{ number_format($prod->price, 0, ',', '.') }}</td>
                                <td class="py-3.5 text-center font-semibold {{ $prod->stock < 5 ? 'text-red-500 font-bold' : '' }}">{{ $prod->stock }}</td>
                                <td class="py-3.5 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $prod->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }}">
                                        {{ $prod->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="py-3.5 text-center" style="text-align: center !important;">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('products.edit', $prod->id) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition-all">Edit</a>
                                        <form action="{{ route('products.destroy', $prod->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')" class="inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-bold transition-all" style="color: #dc2626 !important;">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400">Belum ada produk terdaftar di toko ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="pt-4 border-t border-slate-50">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
