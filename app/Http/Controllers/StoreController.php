<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::where('owner_id', auth()->id())->withCount(['products', 'users'])->get();
        return view('owner.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('owner.stores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        Store::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'owner_id' => auth()->id(),
        ]);

        return redirect()->route('owner.stores.index')->with('success', 'Toko baru berhasil ditambahkan.');
    }

    public function edit(Store $store)
    {
        // Pastikan hanya owner pemilik toko yang bisa edit
        if ($store->owner_id !== auth()->id()) {
            abort(403);
        }
        return view('owner.stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        if ($store->owner_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $store->update($request->only(['name', 'phone', 'address']));

        return redirect()->route('owner.stores.index')->with('success', 'Data toko berhasil diperbarui.');
    }

    public function destroy(Store $store)
    {
        if ($store->owner_id !== auth()->id()) {
            abort(403);
        }

        $store->delete();

        return redirect()->route('owner.stores.index')->with('success', 'Toko berhasil dihapus.');
    }
}
