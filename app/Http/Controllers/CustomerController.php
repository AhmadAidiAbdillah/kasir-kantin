<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'owner') {
            $stores = Store::where('owner_id', $user->id)->get();
            $storeId = $request->input('store_id', $stores->first()->id ?? null);
            
            $customers = $storeId 
                ? Customer::where('store_id', $storeId)->orderBy('name')->paginate(10)
                : collect();
        } else {
            $stores = collect();
            $storeId = $user->store_id;
            
            $customers = $storeId 
                ? Customer::where('store_id', $storeId)->orderBy('name')->paginate(10)
                : collect();
        }

        return view('customers.index', compact('customers', 'stores', 'storeId'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'owner') {
            $stores = Store::where('owner_id', $user->id)->get();
        } else {
            $stores = collect();
        }
        
        $selectedStoreId = $request->input('store_id');
        
        return view('customers.create', compact('stores', 'selectedStoreId'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'store_id' => $user->role === 'owner' 
                ? [
                    'required',
                    Rule::exists('stores', 'id')->where(function ($query) {
                        $query->where('owner_id', auth()->id());
                    })
                  ]
                : 'nullable',
        ]);

        $storeId = $user->role === 'owner' ? $request->store_id : $user->store_id;

        if (!$storeId) {
            return redirect()->back()->withInput()->with('error', 'Anda belum ditugaskan ke toko manapun.');
        }

        Customer::create([
            'store_id' => $storeId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('customers.index', ['store_id' => $storeId])->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($user->role === 'owner') {
            if ($customer->store->owner_id !== $user->id) {
                abort(403);
            }
            $stores = Store::where('owner_id', $user->id)->get();
        } else {
            if ($customer->store_id !== $user->store_id) {
                abort(403);
            }
            $stores = collect();
        }

        return view('customers.edit', compact('customer', 'stores'));
    }

    public function update(Request $request, Customer $customer)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($user->role === 'owner') {
            if ($customer->store->owner_id !== $user->id) {
                abort(403);
            }
        } else {
            if ($customer->store_id !== $user->store_id) {
                abort(403);
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'store_id' => $user->role === 'owner' 
                ? [
                    'required',
                    Rule::exists('stores', 'id')->where(function ($query) {
                        $query->where('owner_id', auth()->id());
                    })
                  ]
                : 'nullable',
        ]);

        $storeId = $user->role === 'owner' ? $request->store_id : $customer->store_id;

        $customer->update([
            'store_id' => $storeId,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('customers.index', ['store_id' => $storeId])->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer)
    {
        $user = Auth::user();
        
        // Cek otorisasi
        if ($user->role === 'owner') {
            if ($customer->store->owner_id !== $user->id) {
                abort(403);
            }
        } else {
            if ($customer->store_id !== $user->store_id) {
                abort(403);
            }
        }

        $storeId = $customer->store_id;
        $customer->delete();

        return redirect()->route('customers.index', ['store_id' => $storeId])->with('success', 'Pelanggan berhasil dihapus.');
    }
}
