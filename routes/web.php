<?php

use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\SellerController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', function () {
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    return match (Auth::user()->role) {
        'admin' => redirect()->route('admin.revenue'),
        'seller' => redirect()->route('seller.shop'),
        default => redirect()->route('user.shop'),
    };
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

    Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset.form');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated common routes
Route::middleware(['auth', 'check.banned'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Role: User routes
    Route::middleware('role:user')->prefix('user')->name('user.')->group(function () {
        Route::get('/shop', [UserController::class, 'shop'])->name('shop');
        Route::get('/cart', [UserController::class, 'cart'])->name('cart');
        Route::post('/cart/add', [UserController::class, 'addToCart'])->name('cart.add');
        Route::post('/cart/{id}/update', [UserController::class, 'updateCart'])->name('cart.update');
        Route::post('/cart/{id}/delete', [UserController::class, 'removeFromCart'])->name('cart.delete');
        Route::post('/checkout', [UserController::class, 'checkout'])->name('checkout');
        Route::get('/history', [UserController::class, 'history'])->name('history');
        Route::get('/orders/{id}/receipt', [UserController::class, 'downloadReceipt'])->name('receipt');
    });

    // Role: Seller routes
    Route::middleware('role:seller')->prefix('seller')->name('seller.')->group(function () {
        Route::get('/shop', [SellerController::class, 'shop'])->name('shop');
        Route::post('/pos/checkout', [SellerController::class, 'posCheckout'])->name('pos.checkout');

        Route::get('/products', [SellerController::class, 'products'])->name('products');
        Route::post('/products/store', [SellerController::class, 'storeProduct'])->name('products.store');
        Route::post('/products/{id}/update', [SellerController::class, 'updateProduct'])->name('products.update');
        Route::post('/products/{id}/delete', [SellerController::class, 'destroyProduct'])->name('products.delete');

        Route::get('/history', [SellerController::class, 'history'])->name('history');
        Route::get('/orders/{id}/receipt', [SellerController::class, 'downloadReceipt'])->name('receipt');
    });

    // Role: Admin routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/revenue', [AdminController::class, 'revenue'])->name('revenue');

        Route::get('/ledger', [AdminController::class, 'ledger'])->name('ledger');
        Route::post('/ledger/store', [AdminController::class, 'storeLedger'])->name('ledger.store');
        Route::post('/ledger/{id}/update', [AdminController::class, 'updateLedger'])->name('ledger.update');
        Route::post('/ledger/{id}/delete', [AdminController::class, 'destroyLedger'])->name('ledger.delete');

        Route::get('/management', [AdminController::class, 'management'])->name('management');
        Route::post('/users/{id}/ban', [AdminController::class, 'toggleBanUser'])->name('users.ban');
        Route::post('/users/{id}/role', [AdminController::class, 'changeUserRole'])->name('users.role');
        Route::post('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('users.delete');
    });
});
