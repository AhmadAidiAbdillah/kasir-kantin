<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\OwnerUserController;

// 🔹 Landing Page (Home Page) sebelum login
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 🔹 Dashboard Utama (Redirect dinamis ke dashboard spesifik role)
Route::get('/dashboard', function () {
    $user = auth()->user();
    if (!$user) {
        return redirect()->route('login');
    }
    
    $role = $user->role;
    if ($role === 'owner') {
        return redirect()->route('owner.dashboard');
    } elseif ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($role === 'kasir') {
        return redirect()->route('kasir.dashboard');
    }
    
    abort(403, 'Akses tidak sah.');
})->middleware(['auth'])->name('dashboard');

// 🔹 Profile Routes (bawaan Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 🔹 Route khusus OWNER
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'owner'])->name('dashboard');
    Route::resource('stores', StoreController::class);
    Route::resource('users', OwnerUserController::class);
    Route::patch('users/{user}/reset', [OwnerUserController::class, 'resetPassword'])->name('users.reset');
});

// 🔹 Route khusus ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
});

// 🔹 Route khusus KASIR
Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'kasir'])->name('dashboard');
});

// 🔹 Fitur POS / Kasir - Bisa diakses oleh Owner, Admin, dan Kasir
Route::middleware(['auth', 'role:owner,admin,kasir'])->group(function () {
    Route::get('/kasir', [TransactionController::class, 'create'])->name('kasir');
    Route::post('/kasir', [TransactionController::class, 'store'])->name('kasir.store');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/export/csv', [TransactionController::class, 'exportCsv'])->name('transactions.export.csv');
    Route::get('/transactions/export/pdf', [TransactionController::class, 'printReport'])->name('transactions.export.pdf');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
});

// 🔹 Master Data: Kategori & Produk - Bisa diakses oleh Owner dan Admin
Route::middleware(['auth', 'role:owner,admin'])->group(function () {
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
});

// 🔹 Master Data: Pelanggan - Bisa diakses oleh Owner, Admin, dan Kasir
Route::middleware(['auth', 'role:owner,admin,kasir'])->group(function () {
    Route::resource('customers', CustomerController::class);
});

// 🔹 Route bawaan Breeze (login, register, dll)
require __DIR__.'/auth.php';

// 🔹 API Resource
Route::prefix('api')->middleware('auth')->group(function () {
    // Read endpoints for all roles (Owner, Admin, Kasir)
    Route::get('products', [\App\Http\Controllers\Api\ProductApiController::class, 'index']);
    Route::get('products/{product}', [\App\Http\Controllers\Api\ProductApiController::class, 'show']);

    // Mutation endpoints restricted to Owner and Admin roles
    Route::middleware('role:owner,admin')->group(function () {
        Route::post('products', [\App\Http\Controllers\Api\ProductApiController::class, 'store']);
        Route::match(['put', 'patch'], 'products/{product}', [\App\Http\Controllers\Api\ProductApiController::class, 'update']);
        Route::delete('products/{product}', [\App\Http\Controllers\Api\ProductApiController::class, 'destroy']);
    });
});



