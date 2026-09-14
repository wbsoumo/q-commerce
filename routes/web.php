<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\ApiController;

// Admin Panel Routes
Route::get('/', [AdminController::class, 'dashboard']);
Route::get('/admin', [AdminController::class, 'dashboard']);

// Stores & Managers Routes
Route::get('/admin/stores', [AdminController::class, 'stores']);
Route::get('/admin/stores/create', [AdminController::class, 'createStore']);
Route::post('/admin/stores/store', [AdminController::class, 'storeStore']);

// Products Routes with Global vs Store Specific Scope
Route::get('/admin/products', [AdminController::class, 'products']);
Route::get('/admin/products/create', [AdminController::class, 'createProduct']);
Route::post('/admin/products/store', [AdminController::class, 'storeProduct']);
Route::get('/admin/products/{id}/edit', [AdminController::class, 'editProduct']);
Route::post('/admin/products/update', [AdminController::class, 'updateProduct']);

// Store Manager Portal Routes
Route::get('/admin/store-manager', [AdminController::class, 'storeManagerPortal']);
Route::post('/admin/store-manager/update-inventory', [AdminController::class, 'updateStoreInventory']);

// Auto Database Table Import Route
Route::get('/import-database', [AdminController::class, 'importDatabase']);

// High-Performance REST API Routes for Flutter App
Route::prefix('api/v1')->group(function () {
    Route::get('/store/select', [ApiController::class, 'selectStore']);
    Route::get('/categories', [ApiController::class, 'getCategories']);
    Route::get('/products', [ApiController::class, 'getProducts']);
    Route::post('/orders', [ApiController::class, 'createOrder']);
});
