<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'owner') {
            $stores = Store::where('owner_id', $user->id)->get();
            $storeId = $request->input('store_id', $stores->first()->id ?? null);
            
            $products = $storeId 
                ? Product::where('store_id', $storeId)->with('category')->orderBy('name')->paginate(10)
                : collect();
        } else {
            $stores = collect();
            $storeId = $user->store_id;
            
            $products = $storeId 
                ? Product::where('store_id', $storeId)->with('category')->orderBy('name')->paginate(10)
                : collect();
        }

        return view('products.index', compact('products', 'stores', 'storeId'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        
        if ($user->role === 'owner') {
            $stores = Store::where('owner_id', $user->id)->get();
            $storeId = $request->input('store_id', $stores->first()->id ?? null);
        } else {
            $stores = collect();
            $storeId = $user->store_id;
        }

        $categories = $storeId 
            ? Category::where('store_id', $storeId)->orderBy('name')->get()
            : collect();

        return view('products.create', compact('stores', 'categories', 'storeId'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $storeId = $user->role === 'owner' ? $request->store_id : $user->store_id;

        if (!$storeId) {
            return redirect()->back()->withInput()->with('error', 'Anda belum ditugaskan ke toko manapun.');
        }

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->where(function ($query) use ($storeId) {
                    return $query->where('store_id', $storeId);
                })
            ],
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) use ($storeId) {
                    $query->where('store_id', $storeId);
                })
            ],
            'store_id' => $user->role === 'owner' 
                ? [
                    'required',
                    Rule::exists('stores', 'id')->where(function ($query) {
                        $query->where('owner_id', auth()->id());
                    })
                  ]
                : 'nullable',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'name.unique' => 'Nama menu/produk sudah terdaftar di toko ini.',
            'name.required' => 'Nama produk wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'stock.required' => 'Stok wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, gif, atau webp.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'store_id' => $storeId,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('products.index', ['store_id' => $storeId])->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $user = Auth::user();

        if ($user->role === 'owner') {
            if ($product->store->owner_id !== $user->id) {
                abort(403);
            }
            $stores = Store::where('owner_id', $user->id)->get();
        } else {
            if ($product->store_id !== $user->store_id) {
                abort(403);
            }
            $stores = collect();
        }

        $categories = Category::where('store_id', $product->store_id)->orderBy('name')->get();

        return view('products.edit', compact('product', 'stores', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $user = Auth::user();

        if ($user->role === 'owner') {
            if ($product->store->owner_id !== $user->id) {
                abort(403);
            }
        } else {
            if ($product->store_id !== $user->store_id) {
                abort(403);
            }
        }

        $storeId = $user->role === 'owner' ? $request->store_id : $product->store_id;

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'name')->where(function ($query) use ($storeId) {
                    return $query->where('store_id', $storeId);
                })->ignore($product->id)
            ],
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) use ($storeId) {
                    $query->where('store_id', $storeId);
                })
            ],
            'store_id' => $user->role === 'owner' 
                ? [
                    'required',
                    Rule::exists('stores', 'id')->where(function ($query) {
                        $query->where('owner_id', auth()->id());
                    })
                  ]
                : 'nullable',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'name.unique' => 'Nama menu/produk sudah terdaftar di toko ini.',
            'name.required' => 'Nama produk wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'stock.required' => 'Stok wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'image.image' => 'File harus berupa gambar.',
            'image.mimes' => 'Format gambar harus jpeg, png, jpg, gif, atau webp.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        $data = [
            'store_id' => $storeId,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'is_active' => $request->has('is_active'),
        ];

        // 1. Kosongkan gambar jika diminta
        if ($request->input('clear_image') == '1') {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = null;
        } 
        // 2. Atau unggah gambar baru jika dikirimkan
        elseif ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index', ['store_id' => $storeId])->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        $user = Auth::user();

        if ($user->role === 'owner') {
            if ($product->store->owner_id !== $user->id) {
                abort(403);
            }
        } else {
            if ($product->store_id !== $user->store_id) {
                abort(403);
            }
        }

        $storeId = $product->store_id;
        
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return redirect()->route('products.index', ['store_id' => $storeId])->with('success', 'Produk berhasil dihapus.');
    }
}
