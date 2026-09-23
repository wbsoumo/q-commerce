<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ApiController;

// Authentication Routes
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'processAdminLogin']);
Route::get('/admin/register', [AuthController::class, 'showAdminRegister']);
Route::post('/admin/register', [AuthController::class, 'processAdminRegister']);
Route::get('/manager/login', [AuthController::class, 'showManagerLogin'])->name('manager.login');
Route::post('/manager/login', [AuthController::class, 'processManagerLogin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Separate Dedicated Store Manager Portal Routes
Route::middleware(['store.manager'])->group(function () {
    Route::get('/manager/dashboard', [ManagerController::class, 'dashboard'])->name('manager.dashboard');
    Route::get('/manager/inventory', [ManagerController::class, 'inventory'])->name('manager.inventory');
    Route::get('/manager/products/create', [ManagerController::class, 'createProduct'])->name('manager.products.create');
    Route::get('/manager/products/{id}/edit', [ManagerController::class, 'editProduct'])->name('manager.products.edit');
    Route::post('/manager/products/store', [ManagerController::class, 'storeProduct']);
    Route::post('/manager/products/update', [ManagerController::class, 'updateProduct']);

    Route::get('/manager/orders', [ManagerController::class, 'orders'])->name('manager.orders');
    Route::post('/manager/orders/{id}/status', [ManagerController::class, 'updateOrderStatus']);

    Route::get('/manager/deliveries', [ManagerController::class, 'deliveries'])->name('manager.deliveries');
    Route::post('/manager/deliveries/assign', [ManagerController::class, 'assignRider']);

    Route::get('/manager/settings', [ManagerController::class, 'settings'])->name('manager.settings');
    Route::post('/manager/settings/update', [ManagerController::class, 'updateSettings']);
});

// Separate Super Admin-Only Routes
Route::middleware(['admin.only'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::get('/admin', [AdminController::class, 'dashboard']);

    // Stores Management
    Route::get('/admin/stores', [AdminController::class, 'stores']);
    Route::get('/admin/stores/create', [AdminController::class, 'createStore']);
    Route::post('/admin/stores/store', [AdminController::class, 'storeStore']);
    Route::get('/admin/stores/{id}/settings', [AdminController::class, 'storeSettings']);
    Route::post('/admin/stores/{id}/settings', [AdminController::class, 'updateStoreSettings']);

    // Category Management
    Route::get('/admin/categories', [AdminController::class, 'categories']);
    Route::post('/admin/categories/store', [AdminController::class, 'storeCategory']);
    Route::post('/admin/categories/update', [AdminController::class, 'updateCategory']);

    // Master Product Catalog
    Route::get('/admin/products', [AdminController::class, 'products']);
    Route::get('/admin/products/create', [AdminController::class, 'createProduct']);
    Route::post('/admin/products/store', [AdminController::class, 'storeProduct']);
    Route::get('/admin/products/{id}/edit', [AdminController::class, 'editProduct']);
    Route::post('/admin/products/update', [AdminController::class, 'updateProduct']);
    Route::get('/admin/products/{id}/variants', [AdminController::class, 'productVariants']);
    Route::post('/admin/products/{id}/variants/store', [AdminController::class, 'storeProductVariant']);

    // Global Homepage Customizer
    Route::get('/admin/homepage-customizer', [AdminController::class, 'homepageCustomizer']);
    Route::post('/admin/homepage-customizer/save', [AdminController::class, 'saveHomepageCustomizer']);

    // Super Admin Manager View Overrides
    Route::get('/admin/store-manager', [AdminController::class, 'storeManagerPortal']);
    Route::post('/admin/store-manager/update-inventory', [AdminController::class, 'updateStoreInventory']);

    // Global Inventory & Alerts
    Route::get('/admin/inventory/transactions', [AdminController::class, 'inventoryTransactions']);
    Route::post('/admin/inventory/adjust', [AdminController::class, 'adjustInventory']);
    Route::get('/admin/inventory/alerts', [AdminController::class, 'inventoryAlerts']);

    // Orders & Customers
    Route::get('/admin/orders', [AdminController::class, 'orders']);
    Route::get('/admin/orders/{id}', [AdminController::class, 'showOrder']);
    Route::post('/admin/orders/{id}/update-status', [AdminController::class, 'updateOrderStatus']);
    Route::get('/admin/customers', [AdminController::class, 'customers']);
    Route::get('/admin/customers/{id}', [AdminController::class, 'showCustomer']);
    Route::post('/admin/customers/{id}/update-status', [AdminController::class, 'updateCustomerStatus']);

    // Logistics & Dispatch
    Route::get('/admin/deliveries', [AdminController::class, 'deliveries']);
    Route::post('/admin/deliveries/assign', [AdminController::class, 'assignDeliveryRider']);
    Route::get('/admin/delivery-zones', [AdminController::class, 'deliveryZones']);
    Route::post('/admin/delivery-zones/store', [AdminController::class, 'storeDeliveryZone']);

    // Staff Management
    Route::get('/admin/staff', [AdminController::class, 'staffMembers']);
    Route::post('/admin/staff/store', [AdminController::class, 'storeStaffMember']);

    // Coupon & Offers Management
    Route::get('/admin/coupons', [AdminController::class, 'coupons']);
    Route::get('/admin/coupons/create', [AdminController::class, 'createCoupon']);
    Route::post('/admin/coupons/store', [AdminController::class, 'storeCoupon']);
    Route::post('/admin/coupons/{id}/toggle', [AdminController::class, 'toggleCoupon']);
    Route::delete('/admin/coupons/{id}', [AdminController::class, 'deleteCoupon']);
});

// High-Performance REST API Routes for Flutter App
Route::prefix('api/v1')->group(function () {
    Route::post('/auth/register', [ApiController::class, 'register']);
    Route::post('/auth/login', [ApiController::class, 'login']);
    Route::get('/store/select', [ApiController::class, 'selectStore']);

    Route::get('/sync-check', [ApiController::class, 'checkSyncStatus']);
    Route::get('/categories', [ApiController::class, 'getCategories']);
    Route::get('/products', [ApiController::class, 'getProducts']);
    Route::post('/orders', [ApiController::class, 'createOrder']);
    Route::get('/user/addresses', [ApiController::class, 'getAddresses']);
    Route::post('/user/addresses/store', [ApiController::class, 'storeAddress']);
    Route::get('/user/orders', [ApiController::class, 'getUserOrders']);
    Route::get('/coupons', [ApiController::class, 'getCoupons']);
    Route::post('/coupons/validate', [ApiController::class, 'validateCoupon']);
    Route::get('/user/wallet', [ApiController::class, 'getUserWallet']);
});
