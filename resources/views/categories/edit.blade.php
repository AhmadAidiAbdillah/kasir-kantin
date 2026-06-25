<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('categories.index', ['store_id' => $category->store_id]) }}" class="text-slate-400 hover:text-slate-600 font-bold">&larr;</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Kategori</h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6">
            <h3 class="text-lg font-bold text-slate-800">Edit Kategori: {{ $category->name }}</h3>

            <form action="{{ route('categories.update', $category->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                @if(auth()->user()->role === 'owner')
                    <div>
                        <x-input-label for="store_id" value="Database Toko" />
                        <select id="store_id" name="store_id" class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm" required>
                            @foreach($stores as $st)
                                <option value="{{ $st->id }}" {{ $st->id == $category->store_id ? 'selected' : '' }}>{{ $st->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('store_id')" class="mt-2" />
                    </div>
                @else
                    <input type="hidden" name="store_id" value="{{ $category->store_id }}">
                @endif

                <div>
                    <x-input-label for="name" value="Nama Kategori" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl" value="{{ old('name', $category->name) }}" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="description" value="Keterangan (Opsional)" />
                    <textarea id="description" name="description" class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3">{{ old('description', $category->description) }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                    <a href="{{ route('categories.index', ['store_id' => $category->store_id]) }}" class="px-4 py-2 border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold shadow-sm transition-all">Batal</a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow shadow-indigo-600/10 transition-all">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
