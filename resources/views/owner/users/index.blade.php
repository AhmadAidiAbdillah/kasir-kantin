<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Akun Pengguna</h2>
  </x-slot>

  @section('page-title','Kelola User')

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 py-6">
    <!-- Form tambah (kiri) -->
    <div class="lg:col-span-1">
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4">Tambah Admin / Kasir Baru</h3>

        <form method="POST" action="{{ route('owner.users.store') }}" class="space-y-4">
          @csrf

          <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl" value="{{ old('name') }}" required />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
          </div>

          <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl" value="{{ old('email') }}" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
          </div>

          <div>
            <x-input-label for="role_id" value="Role" />
            <select id="role_id" name="role_id" class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm" required>
              @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
              @endforeach
            </select>
            <x-input-error :messages="$errors->get('role_id')" class="mt-2" />
          </div>

          <div>
            <x-input-label for="store_id" value="Ditugaskan ke Toko" />
            <select id="store_id" name="store_id" class="mt-1 block w-full rounded-xl border-slate-200 text-sm shadow-sm" required>
              @foreach($stores as $store)
                <option value="{{ $store->id }}" {{ old('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
              @endforeach
            </select>
            <x-input-error :messages="$errors->get('store_id')" class="mt-2" />
          </div>

          <div>
            <x-input-label for="password" value="Password (opsional)" />
            <input type="password" id="password" name="password" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm mt-1 block w-full rounded-xl" />
            <p class="text-xs text-gray-500 mt-1">Kosongkan untuk default: <strong>kantin123</strong></p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
          </div>

          <div class="flex justify-end pt-2">
            <x-primary-button class="w-full justify-center py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 shadow-md">Simpan User</x-primary-button>
          </div>
        </form>
      </div>
    </div>

    <!-- Daftar user (kanan) -->
    <div class="lg:col-span-2">
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-bold text-slate-800">Daftar Admin &amp; Kasir</h3>
          <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">{{ $users->total() }} Pengguna</div>
        </div>

        @if ($users->isEmpty())
          <p class="text-slate-400 text-sm text-center py-6">Belum ada admin / kasir terdaftar.</p>
        @else
          <div class="overflow-x-auto">
            <table class="w-full text-sm text-left border-collapse">
              <thead>
                <tr class="border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                  <th class="pb-3">Nama</th>
                  <th class="pb-3">Email</th>
                  <th class="pb-3">Role</th>
                  <th class="pb-3">Toko</th>
                  <th class="pb-3 text-center" style="text-align: center !important;">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-50 text-sm text-slate-600">
                @foreach ($users as $u)
                  <tr>
                    <td class="py-3.5 font-semibold text-slate-800">{{ $u->name }}</td>
                    <td class="py-3.5">{{ $u->email }}</td>
                    <td class="py-3.5">
                      <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider {{ $u->role === 'admin' ? 'bg-purple-50 text-purple-700' : 'bg-emerald-50 text-emerald-700' }}">
                        {{ $u->roleRelation->name ?? $u->role }}
                      </span>
                    </td>
                    <td class="py-3.5">{{ $u->store->name ?? '-' }}</td>
                    <td class="py-3.5 text-center" style="text-align: center !important;">
                      <div class="flex items-center justify-center gap-2">
                        <!-- Edit -->
                        <a href="{{ route('owner.users.edit', $u) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition-all">Edit</a>

                        <!-- Reset Password -->
                        <form action="{{ route('owner.users.reset', $u) }}" method="POST" onsubmit="return confirm('Reset password user ini ke kantin123?');" class="inline m-0">
                          @csrf
                          @method('PATCH')
                          <button type="submit" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-600 rounded-lg text-xs font-bold transition-all" style="color: #d97706 !important;">Reset</button>
                        </form>

                        <!-- Delete -->
                        <form action="{{ route('owner.users.destroy', $u) }}" method="POST" onsubmit="return confirm('Yakin hapus user ini?');" class="inline m-0">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-xs font-bold transition-all" style="color: #dc2626 !important;">Hapus</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>

          {{-- pagination --}}
          <div class="pt-4 border-t border-slate-50">
            {{ $users->links() }}
          </div>
        @endif
      </div>
    </div>
  </div>
</x-app-layout>
