<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\KasirController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (Auth::check()) {
        return redirect(Auth::user()->getDashboardUrl());
    }
    return redirect()->route('login');
});

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Logout (Authenticated)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Central Dashboard Redirection Route
Route::get('/dashboard', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }
    return redirect(Auth::user()->getDashboardUrl());
})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| UKasir (Kasir) Routes - Desktop-First POS
|--------------------------------------------------------------------------
*/
Route::prefix('kasir')->name('kasir.')->middleware(['auth', 'role:UKasir'])->group(function () {
    Route::get('/pos', [KasirController::class, 'pos'])->name('pos');
    Route::get('/products/search', [KasirController::class, 'searchProducts'])->name('products.search');
    Route::post('/checkout', [KasirController::class, 'checkout'])->name('checkout');
    Route::get('/receipt/{invoice}', [KasirController::class, 'receipt'])->name('receipt');
    Route::get('/history', [KasirController::class, 'history'])->name('history');
});

/*
|--------------------------------------------------------------------------
| UGudang (Gudang) Routes - Master Barang & Inventaris
|--------------------------------------------------------------------------
*/
Route::prefix('gudang')->name('gudang.')->middleware(['auth', 'role:UGudang'])->group(function () {
    Route::get('/', [GudangController::class, 'index'])->name('index');
    Route::post('/products', [GudangController::class, 'storeProduct'])->name('products.store');
    Route::put('/products/{product}', [GudangController::class, 'updateProduct'])->name('products.update');
    Route::delete('/products/{product}', [GudangController::class, 'destroyProduct'])->name('products.destroy');
    Route::post('/stock/incoming', [GudangController::class, 'stockIncoming'])->name('stock.incoming');
    Route::post('/stock/adjustment', [GudangController::class, 'stockAdjustment'])->name('stock.adjustment');
    Route::get('/mutations', [GudangController::class, 'mutations'])->name('mutations');
});

/*
|--------------------------------------------------------------------------
| UAdmin (Admin/Pemilik) Routes - Read-Only Monitoring & Reporting
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:UAdmin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/sales', [AdminController::class, 'salesReport'])->name('sales');
    Route::get('/profits', [AdminController::class, 'profitReport'])->name('profits');
    Route::get('/activities', [AdminController::class, 'activityLogs'])->name('activities');
});
