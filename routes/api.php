<?php

use App\Http\Controllers\Api\V1\AdminManagementController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\FinancialLedgerController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PosController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // 1. Auth routes (Public)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);

        Route::middleware(['auth:sanctum', 'check.banned'])->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/profile', [AuthController::class, 'profile']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
        });
    });

    // 2. Products Catalog (Public read)
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/thinnest-stock', [ProductController::class, 'thinnestStock']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    // 3. User Protected Routes
    Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart/items', [CartController::class, 'store']);
        Route::patch('/cart/items/{id}', [CartController::class, 'update']);
        Route::delete('/cart/items/{id}', [CartController::class, 'destroy']);

        Route::post('/orders/checkout', [OrderController::class, 'checkout']);
    });

    // 4. Common Order Routes (User, Seller, Admin)
    Route::middleware(['auth:sanctum', 'check.banned'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::get('/orders/{id}/receipt', [OrderController::class, 'downloadReceipt'])->name('orders.receipt');
    });

    // 5. Seller & Admin Products Management & POS
    Route::middleware(['auth:sanctum', 'role:seller,admin'])->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        Route::post('/pos/checkout', [PosController::class, 'checkout']);
    });

    // 6. Admin Only Financial Ledger & Store Management
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
        Route::get('/financial-ledgers', [FinancialLedgerController::class, 'index']);
        Route::get('/financial-ledgers/summary', [FinancialLedgerController::class, 'summary']);
        Route::post('/financial-ledgers', [FinancialLedgerController::class, 'store']);
        Route::put('/financial-ledgers/{id}', [FinancialLedgerController::class, 'update']);
        Route::delete('/financial-ledgers/{id}', [FinancialLedgerController::class, 'destroy']);

        Route::get('/users', [AdminManagementController::class, 'users']);
        Route::patch('/users/{id}/ban', [AdminManagementController::class, 'toggleBan']);
        Route::patch('/users/{id}/role', [AdminManagementController::class, 'changeRole']);
        Route::delete('/users/{id}', [AdminManagementController::class, 'deleteUser']);

        Route::get('/audit-logs', [AdminManagementController::class, 'storeActivityLogs']);
        Route::get('/error-logs', [AdminManagementController::class, 'systemErrorLogs']);
    });
});
