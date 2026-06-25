<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Kelola Kategori Menu
            </h2>
            <div class="inline-flex items-center gap-4">
                @if(auth()->user()->role === 'owner' && $stores->count() > 0)
                    <form action="{{ route('categories.index') }}" method="GET" id="store-filter-form">
                        <select name="store_id" onchange="document.getElementById('store-filter-form').submit()" class="rounded-xl border-slate-200 text-xs font-semibold text-slate-700">
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}" {{ $st->id == $storeId ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif
                @if($storeId)
                    <a href="{{ route('categories.create', ['store_id' => $storeId]) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow shadow-indigo-600/10 transition-all">
                        + Tambah Kategori
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
                            <th class="pb-3">Nama Kategori</th>
                            <th class="pb-3">Keterangan</th>
                            <th class="pb-3 text-center" style="text-align: center !important;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                        @forelse($categories as $cat)
                            <tr>
                                <td class="py-3.5 font-semibold text-slate-800">{{ $cat->name }}</td>
                                <td class="py-3.5">{{ $cat->description ?? '-' }}</td>
                                <td class="py-3.5 text-center" style="text-align: center !important;">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('categories.edit', $cat->id) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition-all">Edit</a>
                                        <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Seluruh produk dalam kategori ini juga akan ikut terhapus.')" class="inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-bold transition-all" style="color: #dc2626 !important;">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-slate-400">Belum ada kategori terdaftar di toko ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-app-layout>
