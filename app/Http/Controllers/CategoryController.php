<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'owner') {
            $stores = Store::where('owner_id', $user->id)->get();
            $storeId = $request->input('store_id', $stores->first()->id ?? null);
            
            $categories = $storeId 
                ? Category::where('store_id', $storeId)->orderBy('name')->get()
                : collect();
        } else {
            $stores = collect();
            $storeId = $user->store_id;
            
            $categories = $storeId 
                ? Category::where('store_id', $storeId)->orderBy('name')->get()
                : collect();
        }

        return view('categories.index', compact('categories', 'stores', 'storeId'));
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
        return view('categories.create', compact('stores', 'selectedStoreId'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
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

        Category::create([
            'store_id' => $storeId,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index', ['store_id' => $storeId])->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        $user = Auth::user();
        
        if ($user->role === 'owner') {
            if ($category->store->owner_id !== $user->id) {
                abort(403);
            }
            $stores = Store::where('owner_id', $user->id)->get();
        } else {
            if ($category->store_id !== $user->store_id) {
                abort(403);
            }
            $stores = collect();
        }

        return view('categories.edit', compact('category', 'stores'));
    }

    public function update(Request $request, Category $category)
    {
        $user = Auth::user();
        
        if ($user->role === 'owner') {
            if ($category->store->owner_id !== $user->id) {
                abort(403);
            }
        } else {
            if ($category->store_id !== $user->store_id) {
                abort(403);
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'store_id' => $user->role === 'owner' 
                ? [
                    'required',
                    Rule::exists('stores', 'id')->where(function ($query) {
                        $query->where('owner_id', auth()->id());
                    })
                  ]
                : 'nullable',
        ]);

        $storeId = $user->role === 'owner' ? $request->store_id : $category->store_id;

        $category->update([
            'store_id' => $storeId,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index', ['store_id' => $storeId])->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $user = Auth::user();
        
        if ($user->role === 'owner') {
            if ($category->store->owner_id !== $user->id) {
                abort(403);
            }
        } else {
            if ($category->store_id !== $user->store_id) {
                abort(403);
            }
        }

        $storeId = $category->store_id;
        $category->delete();

        return redirect()->route('categories.index', ['store_id' => $storeId])->with('success', 'Kategori berhasil dihapus.');
    }
}
