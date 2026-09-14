<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Api\ApiController;

// Admin Panel Routes
Route::get('/', [AdminController::class, 'dashboard']);
Route::get('/admin', [AdminController::class, 'dashboard']);
Route::get('/admin/products', [AdminController::class, 'products']);

// Auto Database Table Import Route
Route::get('/import-database', [AdminController::class, 'importDatabase']);

// High-Performance REST API Routes for Flutter App
Route::prefix('api/v1')->group(function () {
    Route::get('/store/select', [ApiController::class, 'selectStore']);
    Route::get('/categories', [ApiController::class, 'getCategories']);
    Route::get('/products', [ApiController::class, 'getProducts']);
    Route::post('/orders', [ApiController::class, 'createOrder']);
});
