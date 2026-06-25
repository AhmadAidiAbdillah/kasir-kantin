<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Owner Dashboard
     */
    public function owner()
    {
        $user = Auth::user();
        
        // Get store IDs owned by this owner
        $ownerStoreIds = Store::where('owner_id', $user->id)->pluck('id');
        
        $totalStores = $ownerStoreIds->count();
        
        if ($totalStores === 0) {
            $totalUsers = 0;
            $totalProducts = 0;
            $totalTransactions = 0;
            $totalRevenue = 0;
            $recentTransactions = collect();
            $stores = collect();
        } else {
            // Count users who are Admin or Kasir and belong to this owner's stores
            $totalUsers = User::whereIn('store_id', $ownerStoreIds)
                ->whereHas('roleRelation', function ($query) {
                    $query->whereIn('name', ['Admin', 'Kasir']);
                })->count();
            
            $totalProducts = Product::whereIn('store_id', $ownerStoreIds)->count();
            $totalTransactions = Transaction::whereIn('store_id', $ownerStoreIds)->count();
            $totalRevenue = Transaction::whereIn('store_id', $ownerStoreIds)->sum('total_price');
            
            $recentTransactions = Transaction::whereIn('store_id', $ownerStoreIds)
                ->with(['store', 'user', 'customer'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            $stores = Store::where('owner_id', $user->id)
                ->withCount(['products', 'users'])
                ->get();
        }

        return view('owner.dashboard', compact(
            'totalStores',
            'totalUsers',
            'totalProducts',
            'totalTransactions',
            'totalRevenue',
            'recentTransactions',
            'stores'
        ));
    }

    /**
     * Admin Dashboard
     */
    public function admin()
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if (!$storeId) {
            return view('admin.dashboard', [
                'error' => 'Anda belum ditugaskan ke toko manapun. Hubungi Owner.',
                'totalProducts' => 0,
                'totalCategories' => 0,
                'totalCustomers' => 0,
                'totalTransactions' => 0,
                'totalRevenue' => 0,
                'recentTransactions' => collect(),
            ]);
        }

        $store = Store::find($storeId);
        $totalProducts = Product::where('store_id', $storeId)->count();
        $totalCategories = Category::where('store_id', $storeId)->count();
        $totalCustomers = Customer::where('store_id', $storeId)->count();
        $totalTransactions = Transaction::where('store_id', $storeId)->count();
        $totalRevenue = Transaction::where('store_id', $storeId)->sum('total_price');

        $recentTransactions = Transaction::where('store_id', $storeId)
            ->with(['user', 'customer'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'store',
            'totalProducts',
            'totalCategories',
            'totalCustomers',
            'totalTransactions',
            'totalRevenue',
            'recentTransactions'
        ));
    }

    /**
     * Kasir Dashboard
     */
    public function kasir()
    {
        $user = Auth::user();
        $storeId = $user->store_id;

        if (!$storeId) {
            return view('kasir.dashboard', [
                'error' => 'Anda belum ditugaskan ke toko manapun. Hubungi Owner.',
                'todayTransactionsCount' => 0,
                'todayRevenue' => 0,
                'recentTransactions' => collect(),
            ]);
        }

        $store = Store::find($storeId);
        
        $todayTransactionsCount = Transaction::where('store_id', $storeId)
            ->where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();
            
        $todayRevenue = Transaction::where('store_id', $storeId)
            ->where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->sum('total_price');

        $recentTransactions = Transaction::where('store_id', $storeId)
            ->where('user_id', $user->id)
            ->with(['customer'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('kasir.dashboard', compact(
            'store',
            'todayTransactionsCount',
            'todayRevenue',
            'recentTransactions'
        ));
    }
}
