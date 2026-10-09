<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ApiController;

// Public Web Legal & Information Pages
Route::get('/about-us', function () {
    return view('legal', ['page' => 'about-us', 'title' => 'About Us – SonarbanglaMart']);
});
Route::get('/terms-and-conditions', function () {
    return view('legal', ['page' => 'terms', 'title' => 'Terms & Conditions – SonarbanglaMart']);
});
Route::get('/privacy-policy', function () {
    return view('legal', ['page' => 'privacy', 'title' => 'Privacy Policy – SonarbanglaMart']);
});
Route::get('/shopping-policy', function () {
    return view('legal', ['page' => 'shopping', 'title' => 'Shopping Policy – SonarbanglaMart']);
});
Route::get('/refund-policy', function () {
    return view('legal', ['page' => 'refund', 'title' => 'Refund & Cancellation Policy – SonarbanglaMart']);
});

// Direct APK Download Routes
Route::get('/SBMartQuick-Universal.apk', function () {
    $path = public_path('SBMartQuick-Universal.apk');
    if (file_exists($path)) {
        return response()->download($path, 'SBMartQuick-Universal.apk', ['Content-Type' => 'application/vnd.android.package-archive']);
    }
    return abort(404);
});
Route::get('/sbmart.apk', function () {
    $path = public_path('sbmart.apk');
    if (!file_exists($path)) {
        $path = public_path('SBMartQuick-Universal.apk');
    }
    if (file_exists($path)) {
        return response()->download($path, 'SBMartQuick-Universal.apk', ['Content-Type' => 'application/vnd.android.package-archive']);
    }
    return abort(404);
});

// Authentication Routes
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'processAdminLogin']);
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
    Route::get('/manager/orders/{id}', [ManagerController::class, 'showOrder'])->name('manager.orders.show');
    Route::post('/manager/orders/{id}/status', [ManagerController::class, 'updateOrderStatus']);
    Route::get('/manager/custom-orders', [ManagerController::class, 'customOrders'])->name('manager.custom-orders');
    Route::post('/manager/custom-orders/{id}/status', [ManagerController::class, 'updateCustomOrderStatus']);
    Route::get('/manager/support-tickets', [ManagerController::class, 'supportTickets'])->name('manager.support-tickets');
    Route::post('/manager/support-tickets/{id}/status', [ManagerController::class, 'updateSupportTicketStatus']);

    Route::get('/manager/deliveries', [ManagerController::class, 'deliveries'])->name('manager.deliveries');
    Route::post('/manager/deliveries/assign', [ManagerController::class, 'assignRider']);

    Route::get('/manager/settings', [ManagerController::class, 'settings'])->name('manager.settings');
    Route::post('/manager/settings/update', [ManagerController::class, 'updateSettings']);
});

// Root domain homepage route for SBMart App Download
Route::get('/', function () {
    $host = request()->getHost();
    if (str_starts_with($host, 'admin.')) {
        if (session('user_type') === 'admin') {
            return app(AdminController::class)->dashboard();
        }
        return redirect()->route('admin.login');
    }
    return view('welcome');
});

// Separate Super Admin-Only Routes
Route::middleware(['admin.only'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard']);

    // Admin-Only Utility Tools
    Route::get('/admin/clear-all-orders', function () {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('order_items')->truncate();
            DB::table('order_status_histories')->truncate();
            DB::table('deliveries')->truncate();
            DB::table('coupon_redemptions')->truncate();
            DB::table('custom_order_requests')->truncate();
            DB::table('orders')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return response()->json([
                'status' => 'success',
                'message' => 'All previous test orders and related history items have been successfully deleted!'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    });

    // Stores Management
    Route::get('/admin/stores', [AdminController::class, 'stores']);
    Route::get('/admin/stores/create', [AdminController::class, 'createStore']);
    Route::post('/admin/stores/store', [AdminController::class, 'storeStore']);
    Route::get('/admin/stores/{id}/settings', [AdminController::class, 'storeSettings']);
    Route::post('/admin/stores/{id}/settings', [AdminController::class, 'updateStoreSettings']);
    Route::post('/admin/stores/{id}/delete', [AdminController::class, 'deleteStore']);
    Route::post('/admin/stores/{id}/managers/store', [AdminController::class, 'addStoreManager']);
    Route::post('/admin/stores/managers/{managerId}/update', [AdminController::class, 'updateStoreManager']);
    Route::get('/admin/stores/managers/{managerId}/login', [AdminController::class, 'oneClickManagerLogin']);

    // Category Management
    Route::get('/admin/categories', [AdminController::class, 'categories']);
    Route::get('/admin/categories/sync-icons', [AdminController::class, 'syncCategoryIcons']);
    Route::post('/admin/categories/store', [AdminController::class, 'storeCategory']);
    Route::post('/admin/categories/update', [AdminController::class, 'updateCategory']);
    Route::post('/admin/categories/delete', [AdminController::class, 'deleteCategory']);

    // Master Product Catalog
    Route::get('/admin/products', [AdminController::class, 'products']);
    Route::get('/admin/products/create', [AdminController::class, 'createProduct']);
    Route::post('/admin/products/store', [AdminController::class, 'storeProduct']);
    Route::get('/admin/products/{id}/edit', [AdminController::class, 'editProduct']);
    Route::post('/admin/products/update', [AdminController::class, 'updateProduct']);
    Route::post('/admin/products/delete/{id}', [AdminController::class, 'deleteProduct']);
    Route::post('/admin/products/bulk-action', [AdminController::class, 'bulkActionProducts']);
    Route::post('/admin/products/quick-update', [AdminController::class, 'quickUpdateProduct']);
    Route::get('/admin/products/{id}/variants', [AdminController::class, 'productVariants']);
    Route::post('/admin/products/{id}/variants/store', [AdminController::class, 'storeProductVariant']);

    // Product Ordering by Category
    Route::get('/admin/product-ordering', [AdminController::class, 'productOrdering']);
    Route::post('/admin/product-ordering/update', [AdminController::class, 'updateProductOrdering']);

    // Ratings & Reviews Management
    Route::get('/admin/ratings', [AdminController::class, 'ratings']);

    // WordPress Media Library APIs
    Route::get('/admin/media/library', [AdminController::class, 'getMediaLibrary']);
    Route::post('/admin/media/upload', [AdminController::class, 'uploadMedia']);
    Route::post('/admin/media/delete', [AdminController::class, 'deleteMedia']);

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
    Route::get('/admin/custom-orders', [AdminController::class, 'customOrders']);
    Route::post('/admin/custom-orders/{id}/status', [AdminController::class, 'updateCustomOrderStatus']);
    Route::get('/admin/support-tickets', [AdminController::class, 'supportTickets']);
    Route::post('/admin/support-tickets/{id}/status', [AdminController::class, 'updateSupportTicketStatus']);
    Route::get('/admin/customers', [AdminController::class, 'customers']);
    Route::post('/admin/customers/store', [AdminController::class, 'storeCustomer']);
    Route::get('/admin/customers/{id}', [AdminController::class, 'showCustomer']);
    Route::post('/admin/customers/{id}/update-status', [AdminController::class, 'updateCustomerStatus']);
    Route::post('/admin/customers/{id}/reactivate', [AdminController::class, 'reactivateCustomer']);
    Route::post('/admin/customers/{id}/change-password', [AdminController::class, 'changeCustomerPassword']);
    Route::post('/admin/customers/{id}/delete', [AdminController::class, 'deleteCustomer']);
    Route::post('/admin/customers/quick-credit', [AdminController::class, 'quickCreditWallet']);

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

    // Promotional Sliders Management
    Route::get('/admin/sliders', [AdminController::class, 'sliders']);
    Route::post('/admin/sliders/store', [AdminController::class, 'storeSlider']);
    Route::post('/admin/sliders/{id}/toggle', [AdminController::class, 'toggleSlider']);
    Route::post('/admin/sliders/{id}/delete', [AdminController::class, 'deleteSlider']);

    // Firebase Push Notification Center
    Route::get('/admin/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
    Route::post('/admin/notifications/settings', [\App\Http\Controllers\NotificationController::class, 'saveSettings']);
    Route::post('/admin/notifications/send', [\App\Http\Controllers\NotificationController::class, 'sendManual']);
});

// High-Performance REST API Routes for Flutter App
Route::prefix('api/v1')->group(function () {
    Route::post('/auth/register', [ApiController::class, 'register']);
    Route::post('/auth/login', [ApiController::class, 'login']);
    Route::post('/user/fcm-token', [ApiController::class, 'registerFcmToken']);
    Route::get('/store/select', [ApiController::class, 'selectStore']);

    Route::get('/sync-check', [ApiController::class, 'checkSyncStatus']);
    Route::get('/sliders', [ApiController::class, 'getSliders']);
    Route::get('/categories', [ApiController::class, 'getCategories']);
    Route::get('/products', [ApiController::class, 'getProducts']);
    Route::post('/orders', [ApiController::class, 'createOrder']);
    Route::get('/user/addresses', [ApiController::class, 'getAddresses']);
    Route::post('/user/addresses/store', [ApiController::class, 'storeAddress']);
    Route::get('/user/orders', [ApiController::class, 'getUserOrders']);
    Route::get('/coupons', [ApiController::class, 'getCoupons']);
    Route::post('/coupons/validate', [ApiController::class, 'validateCoupon']);
    Route::get('/user/wallet', [ApiController::class, 'getUserWallet']);
    Route::get('/user/wishlist', [ApiController::class, 'getUserWishlist']);
    Route::post('/user/wishlist/toggle', [ApiController::class, 'toggleWishlist']);
    Route::get('/user/profile', [ApiController::class, 'getUserProfile']);
    Route::post('/user/profile/update', [ApiController::class, 'updateUserProfile']);
    Route::post('/user/delete-account', [ApiController::class, 'deleteAccount']);
    Route::post('/custom-order/request', [ApiController::class, 'submitCustomOrderRequest']);
    Route::post('/ratings/submit', [ApiController::class, 'submitRating']);
    Route::post('/support/tickets', [ApiController::class, 'createSupportTicket']);
    Route::get('/support/tickets', [ApiController::class, 'getUserSupportTickets']);
    Route::get('/stores', [ApiController::class, 'getAllStores']);
    Route::get('/invoice/signed-url', [ApiController::class, 'getSignedInvoiceUrl']);

// Cryptographically Signed Public Web Invoice Download
Route::get('/invoice/download/{orderNumber}', [AdminController::class, 'downloadInvoiceWeb'])->name('invoice.download')->middleware('signed');

    // Dedicated Store Manager App API Routes
    Route::post('/manager/login', [ApiController::class, 'managerLogin']);
    Route::get('/manager/orders', [ApiController::class, 'getManagerOrders']);
    Route::get('/manager/riders', [ApiController::class, 'getManagerRiders']);
    Route::post('/manager/orders/update-status', [ApiController::class, 'updateManagerOrderStatus']);

    Route::get('/git-pull-deploy', function () {
        $output = shell_exec('cd ' . base_path() . ' && git checkout -- . && git reset --hard origin/main && git pull origin main 2>&1');
        
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $output .= "\nMigration output: " . \Illuminate\Support\Facades\Artisan::output();
        } catch (\Exception $e) {
            $output .= "\nMigration Warning: " . $e->getMessage();
        }

        try {
            $jsonPath = storage_path('app/firebase-service-account.json');
            $b64Path = storage_path('app/firebase-service-account.b64');
            $jsonContent = null;

            if (file_exists($jsonPath)) {
                $jsonContent = file_get_contents($jsonPath);
            } elseif (file_exists($b64Path)) {
                $jsonContent = base64_decode(file_get_contents($b64Path));
            }

            if (!empty($jsonContent)) {
                $parsed = json_decode($jsonContent, true);
                $projectId = $parsed['project_id'] ?? 'sbmart-26423';
                
                DB::table('fcm_settings')->truncate();
                DB::table('fcm_settings')->insert([
                    'project_id' => $projectId,
                    'service_account_json' => $jsonContent,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            $output .= "\nFCM Sync Warning: " . $e->getMessage();
        }

        return response()->json(['status' => 'success', 'output' => $output]);
    });
});
