<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Toko & Cabang') }}
            </h2>
            <a href="{{ route('owner.stores.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-indigo-600/10 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                + Tambah Toko
            </a>
        </div>
    </x-slot>

    @section('page-title', 'Kelola Toko')

    <div class="py-6">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-800">Daftar Toko Aktif</h3>
                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">{{ $stores->count() }} Cabang</div>
            </div>

            @if($stores->isEmpty())
                <div class="py-12 text-center text-slate-450 space-y-3">
                    <span class="text-4xl block">🏪</span>
                    <p class="text-sm">Belum ada toko yang terdaftar. Mulai dengan membuat toko baru.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                                <th class="pb-3">Nama Toko</th>
                                <th class="pb-3">WhatsApp / Telepon</th>
                                <th class="pb-3">Alamat</th>
                                <th class="pb-3 text-center">Jumlah Produk</th>
                                <th class="pb-3 text-center">Jumlah Staff</th>
                                <th class="pb-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-600">
                            @foreach($stores as $st)
                                <tr class="hover:bg-slate-50/40 transition-colors">
                                    <td class="py-4 font-semibold text-slate-850">
                                        {{ $st->name }}
                                    </td>
                                    <td class="py-4 font-mono text-xs">
                                        {{ $st->phone ?? '-' }}
                                    </td>
                                    <td class="py-4 text-xs text-slate-500 max-w-xs truncate">
                                        {{ $st->address ?? '-' }}
                                    </td>
                                    <td class="py-4 text-center">
                                        <span class="px-2 py-0.5 bg-sky-50 text-sky-700 rounded-md text-xs font-bold">
                                            {{ $st->products_count }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-center">
                                        <span class="px-2 py-0.5 bg-purple-50 text-purple-700 rounded-md text-xs font-bold">
                                            {{ $st->users_count }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Edit -->
                                            <a href="{{ route('owner.stores.edit', $st->id) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition-all">
                                                Edit
                                            </a>

                                            <!-- Delete -->
                                            <form action="{{ route('owner.stores.destroy', $st->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus toko ini? Semua data produk, kategori, pelanggan, dan transaksi terkait toko ini akan terhapus secara permanen.');" class="inline m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-bold transition-all" style="color: #dc2626 !important;">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
