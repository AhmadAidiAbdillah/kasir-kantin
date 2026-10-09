<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

/**
 * Class ProductApiController
 * 
 * Controller untuk menyediakan endpoint CRUD data produk dalam format JSON.
 */
class ProductApiController extends Controller
{
    /**
     * GET /api/products
     * Mengambil daftar produk (mendukung filter pencarian & store_id)
     */
    public function index(Request $request)
    {
        $query = Product::query()->where('is_active', true)->with('category');

        // Filter pencarian AJAX
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan Store ID
        if ($request->has('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        $products = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar produk berhasil diambil.',
            'data' => $products
        ], 200);
    }

    /**
     * GET /api/products/{id}
     * Mengambil detail satu produk
     */
    public function show(string $id)
    {
        $product = Product::with('category')->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhasil diambil.',
            'data' => $product
        ], 200);
    }

    /**
     * POST /api/products
     * Menyimpan produk baru
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $storeId = $user->role === 'owner' ? $request->store_id : $user->store_id;

        // Validasi otorisasi multi-tenancy
        if ($user->role === 'owner') {
            $store = Store::find($storeId);
            if (!$store || $store->owner_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke toko ini.'
                ], 403);
            }
        } else {
            // Admin hanya boleh membuat produk untuk toko yang ditugaskan kepadanya
            if ($request->has('store_id') && $request->store_id != $user->store_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke toko ini.'
                ], 403);
            }
        }

        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) use ($storeId) {
                    return $query->where('store_id', $storeId);
                })
            ],
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
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'name.unique' => 'Nama menu/produk sudah terdaftar di toko ini.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'store_id' => $storeId,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'price' => $request->price,
            'stock' => $request->stock,
            'image' => $imagePath,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan.',
            'data' => $product
        ], 201); // HTTP 201 Created
    }

    /**
     * PUT /api/products/{id}
     * Memperbarui data produk
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        $user = auth()->user();
        if ($user->role === 'owner') {
            if ($product->store->owner_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke produk ini.'
                ], 403);
            }
        } else {
            // Admin hanya boleh mengedit produk di tokonya sendiri
            if ($product->store_id !== $user->store_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke produk ini.'
                ], 403);
            }
        }

        $storeId = $user->role === 'owner' 
            ? $request->input('store_id', $product->store_id) 
            : $product->store_id;

        $validator = Validator::make($request->all(), [
            'store_id' => 'nullable|exists:stores,id',
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(function ($query) use ($storeId) {
                    return $query->where('store_id', $storeId);
                })
            ],
            'name' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('products', 'name')->where(function ($query) use ($storeId) {
                    return $query->where('store_id', $storeId);
                })->ignore($id)
            ],
            'price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'name.unique' => 'Nama menu/produk sudah terdaftar di toko ini.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only(['category_id', 'name', 'price', 'stock', 'is_active']);
        if ($user->role === 'owner' && $request->has('store_id')) {
            $data['store_id'] = $request->store_id;
        }

        if ($request->input('clear_image') == '1') {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui.',
            'data' => $product
        ], 200);
    }

    /**
     * DELETE /api/products/{id}
     * Menghapus produk
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan.'
            ], 404);
        }

        $user = auth()->user();
        if ($user->role === 'owner') {
            if ($product->store->owner_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke produk ini.'
                ], 403);
            }
        } else {
            // Admin hanya boleh menghapus produk di tokonya sendiri
            if ($product->store_id !== $user->store_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke produk ini.'
                ], 403);
            }
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus.'
        ], 200);
    }
}
