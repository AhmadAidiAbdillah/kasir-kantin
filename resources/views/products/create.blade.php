<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('products.index', ['store_id' => $storeId]) }}" class="text-slate-400 hover:text-slate-600 font-bold">&larr;</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Produk Baru</h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
            <h3 class="text-lg font-bold text-slate-800">Detail Menu/Produk</h3>

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                @if(auth()->user()->role === 'owner')
                    <div>
                        <x-input-label for="store_id" value="Simpan ke Database Toko" />
                        <select id="store_id" name="store_id" onchange="window.location.href = '{{ route('products.create') }}?store_id=' + this.value" class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm" required>
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}" {{ $st->id == $storeId ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('store_id')" class="mt-2" />
                    </div>
                @else
                    <input type="hidden" name="store_id" value="{{ auth()->user()->store_id }}">
                @endif

                <div>
                    <x-input-label for="category_id" value="Kategori Menu" />
                    <select id="category_id" name="category_id" class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="name" value="Nama Produk" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl" value="{{ old('name') }}" placeholder="Contoh: Ayam Bakar Madu" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="price" value="Harga Jual (Rp)" />
                        <x-text-input id="price" name="price" type="number" min="0" class="mt-1 block w-full rounded-xl" value="{{ old('price') }}" placeholder="Contoh: 15000" required />
                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="stock" value="Stok Awal" />
                        <x-text-input id="stock" name="stock" type="number" min="0" class="mt-1 block w-full rounded-xl" value="{{ old('stock', 0) }}" placeholder="Contoh: 50" required />
                        <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                    </div>
                </div>

                <!-- Image Upload Field -->
                <div class="space-y-2">
                    <x-input-label for="image" value="Foto Produk / Makanan (Opsional)" />
                    <div style="display: flex; align-items: center; gap: 16px;">
                        <div id="image-preview-container" style="width: 80px; height: 80px; border-radius: 12px; background-color: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; font-size: 24px;">
                            🍔
                        </div>
                        <div style="flex: 1; display: flex; flex-direction: column; gap: 8px;">
                            <input type="file" id="image" name="image" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 transition-all cursor-pointer">
                            <p class="text-[10px] text-slate-400" style="margin: 0;">Format: JPG, PNG, WEBP (Maksimal 2MB)</p>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('image')" class="mt-2" />
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" id="is_active" name="is_active" value="1" class="rounded border-slate-350 text-indigo-600 shadow-sm focus:ring-indigo-500" checked>
                    <x-input-label for="is_active" value="Menu ini aktif (dapat dicari oleh Kasir)" class="font-semibold" />
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                    <a href="{{ route('products.index', ['store_id' => $storeId]) }}" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-sm transition-all">Batal</a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow shadow-indigo-600/10 transition-all">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Image Helper -->
    <script>
        document.getElementById('image')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('image-preview-container');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    preview.innerHTML = `<img src="${event.target.result}" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(file);
            } else {
                preview.innerHTML = '🍔';
            }
        });
    </script>
</x-app-layout>
