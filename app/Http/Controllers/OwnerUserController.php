<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Store;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OwnerUserController extends Controller
{
    /**
     * Tampilkan daftar admin & kasir + form tambah user
     */
    public function index()
    {
        $stores = Store::where('owner_id', auth()->id())->get();
        $storeIds = $stores->pluck('id');

        $users = User::whereHas('roleRelation', function ($query) {
                $query->whereIn('name', ['Admin', 'Kasir']);
            })
            ->whereIn('store_id', $storeIds)
            ->with(['store', 'roleRelation'])
            ->orderBy('name')
            ->paginate(10);

        $roles = Role::whereIn('name', ['Admin', 'Kasir'])->get();

        return view('owner.users.index', compact('users', 'stores', 'roles'));
    }

    /**
     * Tampilkan form create
     */
    public function create()
    {
        $stores = Store::where('owner_id', auth()->id())->get();
        $roles = Role::whereIn('name', ['Admin', 'Kasir'])->get();
        return view('owner.users.create', compact('stores', 'roles'));
    }

    /**
     * Simpan user baru (admin / kasir)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role_id'  => ['required', 'exists:roles,id'],
            'store_id' => [
                'required',
                Rule::exists('stores', 'id')->where(function ($query) {
                    $query->where('owner_id', auth()->id());
                })
            ],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $password = $request->filled('password') ? $request->password : 'kantin123';

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role_id'  => $request->role_id,
            'store_id' => $request->store_id,
            'password' => Hash::make($password),
        ]);

        return redirect()->route('owner.users.index')->with('success', 'User baru berhasil dibuat. Password: ' . $password);
    }

    /**
     * Tampilkan halaman edit user
     */
    public function edit(User $user)
    {
        // mencegah edit akun owner lewat sini
        if ($user->role === 'owner') {
            abort(403);
        }

        $stores = Store::where('owner_id', auth()->id())->get();

        // pastikan user yang akan diedit bertugas di salah satu toko milik owner ini
        if (!$user->store_id || !$stores->contains('id', $user->store_id)) {
            abort(403);
        }

        $roles = Role::whereIn('name', ['Admin', 'Kasir'])->get();

        return view('owner.users.edit', compact('user', 'stores', 'roles'));
    }

    /**
     * Update user (nama, email, role, toko, password opsional)
     */
    public function update(Request $request, User $user)
    {
        if ($user->role === 'owner') {
            abort(403);
        }

        // pastikan user yang akan diupdate bertugas di salah satu toko milik owner ini
        $stores = Store::where('owner_id', auth()->id())->get();
        if (!$user->store_id || !$stores->contains('id', $user->store_id)) {
            abort(403);
        }

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role_id'  => ['required', 'exists:roles,id'],
            'store_id' => [
                'required',
                Rule::exists('stores', 'id')->where(function ($query) {
                    $query->where('owner_id', auth()->id());
                })
            ],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'role_id'  => $request->role_id,
            'store_id' => $request->store_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('owner.users.index')->with('success', 'Data user berhasil diperbarui.');
    }

    /**
     * Hapus user
     */
    public function destroy(User $user)
    {
        if ($user->role === 'owner') {
            abort(403);
        }

        // pastikan user yang akan dihapus bertugas di salah satu toko milik owner ini
        $stores = Store::where('owner_id', auth()->id())->get();
        if (!$user->store_id || !$stores->contains('id', $user->store_id)) {
            abort(403);
        }

        $user->delete();

        return redirect()->route('owner.users.index')->with('success', 'User berhasil dihapus.');
    }

    /**
     * Reset password ke default
     */
    public function resetPassword(User $user)
    {
        if ($user->role === 'owner') {
            abort(403);
        }

        // pastikan user yang akan direset bertugas di salah satu toko milik owner ini
        $stores = Store::where('owner_id', auth()->id())->get();
        if (!$user->store_id || !$stores->contains('id', $user->store_id)) {
            abort(403);
        }

        $newPassword = 'kantin123';

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return redirect()->route('owner.users.index')->with('success', 'Password user di-reset ke: ' . $newPassword);
    }
}
