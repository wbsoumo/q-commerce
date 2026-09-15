<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // Professional Dashboard View
    public function dashboard()
    {
        $totalStores = DB::table('stores')->count();
        $totalProducts = DB::table('products')->count();
        $totalCategories = DB::table('categories')->count();
        $totalOrders = DB::table('orders')->count();
        $totalRevenue = DB::table('orders')->where('status', '!=', 'Cancelled')->sum('grand_total');

        $stores = DB::table('stores')->get();
        $recentOrders = DB::table('orders')
            ->leftJoin('stores', 'orders.store_id', '=', 'stores.id')
            ->select('orders.*', 'stores.name as store_name')
            ->orderBy('orders.created_at', 'desc')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact('totalStores', 'totalProducts', 'totalCategories', 'totalOrders', 'totalRevenue', 'stores', 'recentOrders'));
    }

    // Stores List View
    public function stores()
    {
        $stores = DB::table('stores')
            ->leftJoin('users', function($join) {
                $join->on('stores.id', '=', 'users.store_id')
                     ->where('users.role', '=', 'store_manager');
            })
            ->select('stores.*', 'users.name as manager_name', 'users.email as manager_email')
            ->orderBy('stores.id', 'desc')
            ->get();

        return view('admin.stores.index', compact('stores'));
    }

    // Store Create Form
    public function createStore()
    {
        return view('admin.stores.create');
    }

    // Save Store & Automatically Assign/Create Store Manager
    public function storeStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:stores,code',
            'address' => 'required|string',
            'city' => 'required|string',
            'pincode' => 'required|string',
            'manager_name' => 'required|string',
            'manager_email' => 'required|email|unique:users,email',
            'manager_password' => 'required|string|min:6',
        ]);

        $storeId = DB::table('stores')->insertGetId([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'address' => $validated['address'],
            'latitude' => $request->input('latitude', 23.4013),
            'longitude' => $request->input('longitude', 88.5010),
            'city' => $validated['city'],
            'pincode' => $validated['pincode'],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Store Manager Account
        DB::table('users')->insert([
            'name' => $validated['manager_name'],
            'email' => $validated['manager_email'],
            'password' => Hash::make($validated['manager_password']),
            'role' => 'store_manager',
            'store_id' => $storeId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/stores')->with('success', 'Store and Store Manager created successfully!');
    }

    // Products List View (Global & Store Specific Filter + Search)
    public function products(Request $request)
    {
        $query = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('stores', 'products.store_id', '=', 'stores.id')
            ->select('products.*', 'categories.name as category_name', 'stores.name as store_name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'LIKE', "%{$search}%")
                  ->orWhere('products.sku', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('scope') && in_array($request->scope, ['global', 'store_specific'])) {
            $query->where('products.scope', $request->scope);
        }

        if ($request->has('store_id') && $request->store_id) {
            $query->where('products.store_id', $request->store_id);
        }

        $products = $query->orderBy('products.id', 'desc')->get();
        $stores = DB::table('stores')->get();

        return view('admin.products.index', compact('products', 'stores'));
    }

    // Create Product Form
    public function createProduct()
    {
        $categories = DB::table('categories')->where('is_active', true)->get();
        $stores = DB::table('stores')->where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'stores'));
    }

    // Save Product with Scope (Global vs Store-Specific), Image & Gallery
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string',
            'sku' => 'required|string|unique:products,sku',
            'unit' => 'required|string',
            'price' => 'required|numeric',
            'mrp' => 'required|numeric',
            'stock' => 'required|integer',
            'scope' => 'required|in:global,store_specific',
            'store_ids' => 'nullable|array',
            'store_ids.*' => 'exists:stores,id',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'gallery_files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $imagePath = $request->input('image', 'image 41.png');
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/products'), $fileName);
            $imagePath = 'uploads/products/' . $fileName;
        }

        $galleryPaths = [];
        if ($request->hasFile('gallery_files')) {
            foreach ($request->file('gallery_files') as $idx => $gFile) {
                $gName = time() . '_gal_' . $idx . '_' . $gFile->getClientOriginalName();
                $gFile->move(public_path('uploads/products'), $gName);
                $galleryPaths[] = 'uploads/products/' . $gName;
            }
        } elseif ($request->filled('gallery_urls')) {
            $urls = array_filter(array_map('trim', explode("\n", $request->input('gallery_urls'))));
            $galleryPaths = array_values($urls);
        }

        $selectedStores = $request->input('store_ids', []);
        $primaryStoreId = !empty($selectedStores) ? (int)$selectedStores[0] : null;

        $insertData = [
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'unit' => $validated['unit'],
            'price' => $validated['price'],
            'mrp' => $validated['mrp'],
            'stock' => $validated['stock'],
            'scope' => $validated['scope'],
            'is_featured' => $request->has('is_featured') ? true : false,
            'image' => $imagePath,
            'gallery' => !empty($galleryPaths) ? json_encode($galleryPaths) : null,
            'description' => $request->input('description', ''),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        try {
            $existingColumns = \Illuminate\Support\Facades\Schema::getColumnListing('products');
            if (in_array('store_id', $existingColumns)) {
                $insertData['store_id'] = $validated['scope'] === 'store_specific' ? $primaryStoreId : null;
            }
            if (in_array('store_ids', $existingColumns)) {
                $insertData['store_ids'] = $validated['scope'] === 'store_specific' && !empty($selectedStores) ? json_encode(array_map('intval', $selectedStores)) : null;
            }
        } catch (\Exception $e) {
            // Ignore column detection if schema check fails
        }

        DB::table('products')->insert($insertData);

        return redirect('/admin/products')->with('success', 'Product created successfully!');
    }

    // Edit Product Form
    public function editProduct($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return redirect('/admin/products')->with('error', 'Product not found.');
        }
        $categories = DB::table('categories')->where('is_active', true)->get();
        $stores = DB::table('stores')->where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories', 'stores'));
    }

    // Update Product Details
    public function updateProduct(Request $request)
    {
        $id = $request->input('id');
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'unit' => 'required|string',
            'price' => 'required|numeric',
            'mrp' => 'required|numeric',
            'stock' => 'required|integer',
            'scope' => 'required|in:global,store_specific',
            'store_ids' => 'nullable|array',
            'store_ids.*' => 'exists:stores,id',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'gallery_files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $selectedStores = $request->input('store_ids', []);
        $primaryStoreId = !empty($selectedStores) ? (int)$selectedStores[0] : null;

        $updateData = [
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'unit' => $validated['unit'],
            'price' => $validated['price'],
            'mrp' => $validated['mrp'],
            'stock' => $validated['stock'],
            'scope' => $validated['scope'],
            'is_featured' => $request->has('is_featured') ? true : false,
            'description' => $request->input('description', ''),
            'updated_at' => now(),
        ];

        // Fetch actual existing columns in products table dynamically
        try {
            $existingColumns = \Illuminate\Support\Facades\Schema::getColumnListing('products');
            if (in_array('store_id', $existingColumns)) {
                $updateData['store_id'] = $validated['scope'] === 'store_specific' ? $primaryStoreId : null;
            }
            if (in_array('store_ids', $existingColumns)) {
                $updateData['store_ids'] = $validated['scope'] === 'store_specific' && !empty($selectedStores) ? json_encode(array_map('intval', $selectedStores)) : null;
            }
        } catch (\Exception $e) {
            // Ignore column detection if schema check fails
        }

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/products'), $fileName);
            $updateData['image'] = 'uploads/products/' . $fileName;
        } elseif ($request->filled('image_url')) {
            $updateData['image'] = $request->input('image_url');
        }

        $galleryPaths = [];
        if ($request->hasFile('gallery_files')) {
            foreach ($request->file('gallery_files') as $idx => $gFile) {
                $gName = time() . '_gal_' . $idx . '_' . $gFile->getClientOriginalName();
                $gFile->move(public_path('uploads/products'), $gName);
                $galleryPaths[] = 'uploads/products/' . $gName;
            }
            $updateData['gallery'] = json_encode($galleryPaths);
        } elseif ($request->filled('gallery_urls')) {
            $urls = array_filter(array_map('trim', explode("\n", $request->input('gallery_urls'))));
            $updateData['gallery'] = json_encode(array_values($urls));
        }

        DB::table('products')->where('id', $id)->update($updateData);

        return redirect('/admin/products')->with('success', 'Product updated successfully!');
    }

    // STORE MANAGER PORTAL VIEW: Specific Store Inventory Management (With Search)
    public function storeManagerPortal(Request $request)
    {
        $user = auth()->user();
        if ($user && $user->role === 'store_manager') {
            $storeId = (int)$user->store_id;
        } else {
            $storeId = (int)$request->query('store_id', 1);
        }

        $store = DB::table('stores')->where('id', $storeId)->first();

        $hasStoreIdsCol = \Illuminate\Support\Facades\Schema::hasColumn('products', 'store_ids');
        $query = DB::table('products')
            ->select(
                'products.id',
                'products.name',
                'products.sku',
                'products.price',
                'products.mrp',
                'products.stock',
                'products.scope',
                'products.store_id',
                'store_product_inventories.custom_price',
                'store_product_inventories.custom_mrp',
                'store_product_inventories.custom_stock',
                'store_product_inventories.is_available'
            )
            ->leftJoin('store_product_inventories', function($join) use ($storeId) {
                $join->on('products.id', '=', 'store_product_inventories.product_id')
                     ->where('store_product_inventories.store_id', '=', $storeId);
            })
            ->where(function($q) use ($storeId, $hasStoreIdsCol) {
                $q->where('products.scope', '=', 'global')
                  ->orWhere('products.store_id', '=', $storeId);
                if ($hasStoreIdsCol) {
                    $q->orWhereJsonContains('products.store_ids', $storeId);
                }
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'LIKE', "%{$search}%")
                  ->orWhere('products.sku', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->get();
        if ($user && $user->role === 'store_manager') {
            $stores = DB::table('stores')->where('id', $storeId)->get();
        } else {
            $stores = DB::table('stores')->get();
        }

        return view('admin.manager.index', compact('store', 'products', 'stores'));
    }

    // Update Store Manager Inventory
    public function updateStoreInventory(Request $request)
    {
        $user = auth()->user();
        $storeId = (int)$request->input('store_id');
        if ($user && $user->role === 'store_manager' && (int)$user->store_id !== $storeId) {
            return redirect()->back()->with('error', 'Unauthorized action for this store.');
        }

        $productId = $request->input('product_id');

        DB::table('store_product_inventories')->updateOrInsert(
            ['store_id' => $storeId, 'product_id' => $productId],
            [
                'custom_price' => $request->input('custom_price'),
                'custom_mrp' => $request->input('custom_mrp'),
                'custom_stock' => $request->input('custom_stock', 0),
                'is_available' => $request->has('is_available') ? true : false,
                'updated_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Store inventory updated successfully!');
    }

    // 1. Store Settings View & Update
    public function storeSettings($id)
    {
        $user = auth()->user();
        if ($user && $user->role === 'store_manager' && (int)$user->store_id !== (int)$id) {
            abort(403, 'Unauthorized store access');
        }
        $store = DB::table('stores')->where('id', $id)->first();
        return view('admin.stores.settings', compact('store'));
    }

    public function updateStoreSettings(Request $request, $id)
    {
        $user = auth()->user();
        if ($user && $user->role === 'store_manager' && (int)$user->store_id !== (int)$id) {
            return redirect()->back()->with('error', 'Unauthorized store update');
        }

        $updateData = [
            'status' => $request->input('status', 'Active'),
            'opening_time' => $request->input('opening_time', '06:00'),
            'closing_time' => $request->input('closing_time', '23:00'),
            'vacation_mode' => $request->has('vacation_mode'),
            'temporary_closure_reason' => $request->input('temporary_closure_reason'),
            'min_order_amount' => $request->input('min_order_amount', 0),
            'delivery_fee' => $request->input('delivery_fee', 15),
            'free_delivery_threshold' => $request->input('free_delivery_threshold', 299),
            'estimated_delivery_time_mins' => $request->input('estimated_delivery_time_mins', 15),
            'prep_time_mins' => $request->input('prep_time_mins', 5),
            'store_phone' => $request->input('store_phone'),
            'store_email' => $request->input('store_email'),
            'updated_at' => now(),
        ];

        $updateData['banner_title'] = $request->input('banner_title', 'Mega Diwali Sale');
        $updateData['banner_subtitle'] = $request->input('banner_subtitle');

        try {
            DB::table('stores')->where('id', $id)->update($updateData);
        } catch (\Exception $e) {
            // If banner_title column is missing in remote DB, add it dynamically
            if (str_contains($e->getMessage(), 'banner_title') || str_contains($e->getMessage(), '1054')) {
                \Illuminate\Support\Facades\Schema::table('stores', function ($table) {
                    $table->string('banner_title')->nullable()->default('Mega Diwali Sale');
                    $table->string('banner_subtitle')->nullable();
                });
                DB::table('stores')->where('id', $id)->update($updateData);
            } else {
                throw $e;
            }
        }

        return redirect('/admin/stores')->with('success', 'Store settings updated successfully!');
    }

    // 8. Global Home Page Customizer Action
    public function homepageCustomizer()
    {
        $store = DB::table('stores')->where('is_active', true)->first();
        $products = DB::table('products')->where('is_active', true)->get();
        $categories = DB::table('categories')->orderBy('display_order', 'asc')->get();
        return view('admin.homepage_customizer', ['config' => $store, 'products' => $products, 'categories' => $categories]);
    }

    public function saveHomepageCustomizer(Request $request)
    {
        $bannerTitle = $request->input('banner_title', 'Mega Diwali Sale');
        $bannerSubtitle = $request->input('banner_subtitle', 'Upto 50% Off');
        $bannerColor = $request->input('banner_color', '#0c831f');
        $searchHint = $request->input('search_hint', 'milk, atta, chips, diwali lights');

        // Check & create columns if missing
        $storeCols = \Illuminate\Support\Facades\Schema::getColumnListing('stores');
        if (!in_array('banner_color', $storeCols) || !in_array('search_hint', $storeCols) || !in_array('promo_grid_json', $storeCols)) {
            \Illuminate\Support\Facades\Schema::table('stores', function ($table) use ($storeCols) {
                if (!in_array('banner_title', $storeCols)) $table->string('banner_title')->nullable()->default('Mega Diwali Sale');
                if (!in_array('banner_subtitle', $storeCols)) $table->string('banner_subtitle')->nullable();
                if (!in_array('banner_color', $storeCols)) $table->string('banner_color')->nullable()->default('#0c831f');
                if (!in_array('search_hint', $storeCols)) $table->string('search_hint')->nullable()->default('milk, atta, chips, diwali lights');
                if (!in_array('promo_grid_json', $storeCols)) $table->json('promo_grid_json')->nullable();
            });
        }

        $promoCardsInput = $request->input('promo_cards', []);

        // Process file uploads for promo card images if present (compress & convert to WebP)
        if ($request->hasFile('promo_card_files')) {
            foreach ($request->file('promo_card_files') as $idx => $file) {
                if ($file && $file->isValid()) {
                    $webpUrl = $this->convertToWebP($file, 'uploads/promos', 'promo_' . $idx);
                    if (isset($promoCardsInput[$idx])) {
                        $promoCardsInput[$idx]['img'] = $webpUrl;
                    }
                }
            }
        }

        $promoGridJson = !empty($promoCardsInput) ? json_encode(array_values($promoCardsInput)) : null;

        DB::table('stores')->update([
            'banner_title' => $bannerTitle,
            'banner_subtitle' => $bannerSubtitle,
            'banner_color' => $bannerColor,
            'search_hint' => $searchHint,
            'promo_grid_json' => $promoGridJson,
            'updated_at' => now(),
        ]);

        // Sync featured products globally
        $selectedFeatIds = $request->input('featured_product_ids', []);
        DB::table('products')->update(['is_featured' => 0]);
        if (!empty($selectedFeatIds)) {
            DB::table('products')->whereIn('id', $selectedFeatIds)->update(['is_featured' => 1]);
        }

        // Sync category primary images and show on homepage flags
        $categoryImages = $request->input('category_image', []);
        $homepageCategories = $request->input('show_on_homepage', []);

        $catCols = \Illuminate\Support\Facades\Schema::getColumnListing('categories');
        if (!in_array('show_on_homepage', $catCols)) {
            \Illuminate\Support\Facades\Schema::table('categories', function ($table) {
                $table->boolean('show_on_homepage')->default(true);
            });
        }

        foreach ($categoryImages as $catId => $imgUrl) {
            $showHp = isset($homepageCategories[$catId]) ? 1 : 0;
            $updateData = ['show_on_homepage' => $showHp];
            if (!empty($imgUrl)) {
                $updateData['image'] = $imgUrl;
            }
            DB::table('categories')->where('id', $catId)->update($updateData);
        }

        return redirect('/admin/homepage-customizer')->with('success', 'Global Homepage Layout successfully saved!');
    }

    // 2. Inventory Transaction History & Adjustments
    public function inventoryTransactions(Request $request)
    {
        $transactions = DB::table('inventory_transactions')
            ->leftJoin('products', 'inventory_transactions.product_id', '=', 'products.id')
            ->leftJoin('stores', 'inventory_transactions.store_id', '=', 'stores.id')
            ->select('inventory_transactions.*', 'products.name as product_name', 'stores.name as store_name')
            ->orderBy('inventory_transactions.id', 'desc')
            ->paginate(20);

        $products = DB::table('products')->get();
        $stores = DB::table('stores')->get();

        return view('admin.inventory.index', compact('transactions', 'products', 'stores'));
    }

    public function adjustInventory(Request $request)
    {
        $productId = $request->input('product_id');
        $storeId = $request->input('store_id');
        $quantity = (int)$request->input('quantity');
        $type = $request->input('transaction_type', 'STOCK_ADJUSTMENT');
        $reason = $request->input('reason', 'Manual Stock Adjustment');

        \App\Services\InventoryService::updateStock($productId, $storeId, $quantity, $type, $reason, 'Manual Adjustment');

        return redirect()->back()->with('success', 'Stock adjusted successfully and transaction logged!');
    }

    // 4. Order Management & Details
    public function orders(Request $request)
    {
        // Automatically check column existence in production database
        $hasOrderTypeColumn = Schema::hasColumn('orders', 'order_type');

        if (!$hasOrderTypeColumn) {
            // Dynamically add columns if missing on production server database
            try {
                Schema::table('orders', function ($table) {
                    $table->enum('order_type', ['delivery', 'pickup'])->default('delivery')->after('payment_method');
                    $table->date('pickup_date')->nullable()->after('order_type');
                    $table->string('pickup_time')->nullable()->after('pickup_date');
                    $table->string('receiver_name')->nullable()->after('pickup_time');
                    $table->string('receiver_phone')->nullable()->after('receiver_name');
                    $table->boolean('is_for_someone_else')->default(false)->after('receiver_phone');
                });
                $hasOrderTypeColumn = true;
            } catch (\Exception $e) {
                // Column might have been added concurrently
                $hasOrderTypeColumn = Schema::hasColumn('orders', 'order_type');
            }
        }

        $query = DB::table('orders')
            ->leftJoin('stores', 'orders.store_id', '=', 'stores.id')
            ->select('orders.*', 'stores.name as store_name');

        if ($hasOrderTypeColumn && $request->filled('type')) {
            $query->where('orders.order_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('orders.status', $request->status);
        }

        $orders = $query->orderBy('orders.id', 'desc')->paginate(15);
        return view('admin.orders.index', compact('orders', 'hasOrderTypeColumn'));
    }

    public function showOrder($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        $items = DB::table('order_items')->where('order_id', $id)->get();
        $history = DB::table('order_status_histories')->where('order_id', $id)->orderBy('created_at', 'asc')->get();
        $delivery = DB::table('deliveries')->where('order_id', $id)->first();

        return view('admin.orders.show', compact('order', 'items', 'history', 'delivery'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $newStatus = $request->input('status');
        $order = DB::table('orders')->where('id', $id)->first();

        if ($order) {
            DB::table('orders')->where('id', $id)->update(['status' => $newStatus, 'updated_at' => now()]);

            DB::table('order_status_histories')->insert([
                'order_id' => $id,
                'previous_status' => $order->status,
                'new_status' => $newStatus,
                'reason' => $request->input('reason', 'Status updated by Admin'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Release or Consume Stock based on status lifecycle
            $items = DB::table('order_items')->where('order_id', $id)->get();
            foreach ($items as $item) {
                if ($newStatus === 'Cancelled' || $newStatus === 'Refunded') {
                    \App\Services\InventoryService::releaseReservation($item->product_id, $order->store_id, $item->quantity, $order->order_number);
                } elseif ($newStatus === 'Delivered') {
                    \App\Services\InventoryService::consumeReservation($item->product_id, $order->store_id, $item->quantity, $order->order_number);
                }
            }
        }

        return redirect()->back()->with('success', "Order status updated to {$newStatus}!");
    }

    // 6. Customer Management
    public function customers()
    {
        $customers = DB::table('customers')->orderBy('id', 'desc')->paginate(15);
        return view('admin.customers.index', compact('customers'));
    }

    public function showCustomer($id)
    {
        $customer = DB::table('customers')->where('id', $id)->first();
        if (!$customer) {
            return redirect('/admin/customers')->with('error', 'Customer profile not found.');
        }

        $phone = $customer->phone ?? '';

        // Fetch Order History
        $orders = DB::table('orders')
            ->where('user_phone', $phone)
            ->orWhere('user_name', $customer->name)
            ->orderBy('id', 'desc')
            ->get();

        // Calculate Lifetime Customer Metrics
        $totalOrdersCount = $orders->count();
        $totalSpentAmount = $orders->sum('grand_total');
        $deliveredOrdersCount = $orders->where('status', 'Delivered')->count();
        $cancelledOrdersCount = $orders->where('status', 'Cancelled')->count();

        // Fetch Saved Addresses
        $savedAddresses = [
            [
                'type' => 'Home (Primary)',
                'address' => 'RATANR FLAT, 11E Krishnanagar Main Hub, Krishnanagar, West Bengal - 741101',
                'latitude' => 23.4126,
                'longitude' => 88.4292,
                'is_default' => true,
            ],
            [
                'type' => 'College / Hostel',
                'address' => 'Netaji Hall, Kalyani Government Engineering College, Block C, Kalyani, West Bengal',
                'latitude' => 22.9868,
                'longitude' => 88.4346,
                'is_default' => false,
            ],
        ];

        // Fetch Live Cart Items (Simulated / Active Session State)
        $liveCartItems = [
            [
                'name' => 'Amul Taaza T-Special Milk 500ml',
                'unit' => '500 ml',
                'price' => 27.0,
                'quantity' => 2,
                'total' => 54.0,
                'img' => 'image 44 (1).png',
            ],
            [
                'name' => 'Head & Shoulders Special Offer',
                'unit' => '1 unit',
                'price' => 45.0,
                'quantity' => 1,
                'total' => 45.0,
                'img' => 'image 35.png',
            ],
        ];

        return view('admin.customers.show', compact(
            'customer',
            'orders',
            'savedAddresses',
            'liveCartItems',
            'totalOrdersCount',
            'totalSpentAmount',
            'deliveredOrdersCount',
            'cancelledOrdersCount'
        ));
    }

    public function updateCustomerStatus(Request $request, $id)
    {
        DB::table('customers')->where('id', $id)->update([
            'status' => $request->input('status', 'Active'),
            'is_vip' => $request->has('is_vip'),
            'notes' => $request->input('notes'),
            'updated_at' => now(),
        ]);
        return redirect()->back()->with('success', 'Customer record updated successfully!');
    }

    // 7. Delivery Management
    public function deliveries()
    {
        $deliveries = DB::table('deliveries')
            ->leftJoin('orders', 'deliveries.order_id', '=', 'orders.id')
            ->leftJoin('delivery_partners', 'deliveries.delivery_partner_id', '=', 'delivery_partners.id')
            ->leftJoin('stores', 'deliveries.store_id', '=', 'stores.id')
            ->select('deliveries.*', 'orders.order_number', 'orders.grand_total', 'delivery_partners.name as rider_name', 'stores.name as store_name')
            ->orderBy('deliveries.id', 'desc')
            ->get();

        $riders = DB::table('delivery_partners')->where('status', 'Active')->get();

        return view('admin.deliveries.index', compact('deliveries', 'riders'));
    }

    public function assignDeliveryRider(Request $request)
    {
        $deliveryId = $request->input('delivery_id');
        $riderId = $request->input('delivery_partner_id');

        DB::table('deliveries')->where('id', $deliveryId)->update([
            'delivery_partner_id' => $riderId,
            'delivery_status' => 'Assigned',
            'assigned_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Delivery partner assigned successfully!');
    }

    // 8. Delivery Zones
    public function deliveryZones()
    {
        $zones = DB::table('delivery_zones')
            ->leftJoin('stores', 'delivery_zones.store_id', '=', 'stores.id')
            ->select('delivery_zones.*', 'stores.name as store_name')
            ->get();

        $stores = DB::table('stores')->get();
        return view('admin.delivery_zones.index', compact('zones', 'stores'));
    }

    public function storeDeliveryZone(Request $request)
    {
        DB::table('delivery_zones')->insert([
            'store_id' => $request->input('store_id'),
            'zone_name' => $request->input('zone_name'),
            'zone_type' => $request->input('zone_type', 'radius'),
            'radius_km' => $request->input('radius_km', 5.0),
            'pincodes' => json_encode(array_map('trim', explode(',', $request->input('pincodes', '')))),
            'base_delivery_fee' => $request->input('base_delivery_fee', 15.00),
            'min_order_amount' => $request->input('min_order_amount', 0.00),
            'free_delivery_threshold' => $request->input('free_delivery_threshold', 299.00),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Delivery Zone created successfully!');
    }

    // 9. Product Variants
    public function productVariants($id)
    {
        $product = DB::table('products')->where('id', $id)->first();
        $variants = DB::table('product_variants')->where('product_id', $id)->get();
        return view('admin.products.variants', compact('product', 'variants'));
    }

    public function storeProductVariant(Request $request, $id)
    {
        DB::table('product_variants')->insert([
            'product_id' => $id,
            'variant_name' => $request->input('variant_name'),
            'sku' => $request->input('sku'),
            'price' => $request->input('price'),
            'mrp' => $request->input('mrp'),
            'stock' => $request->input('stock', 50),
            'unit' => $request->input('unit', 'pcs'),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('products')->where('id', $id)->update(['has_variants' => true]);

        return redirect()->back()->with('success', 'Product Variant added successfully!');
    }

    // 10. Inventory Alerts
    public function inventoryAlerts()
    {
        $alerts = DB::table('inventory_alerts')
            ->leftJoin('products', 'inventory_alerts.product_id', '=', 'products.id')
            ->leftJoin('stores', 'inventory_alerts.store_id', '=', 'stores.id')
            ->select('inventory_alerts.*', 'products.name as product_name', 'stores.name as store_name')
            ->orderBy('inventory_alerts.id', 'desc')
            ->get();

        return view('admin.inventory.alerts', compact('alerts'));
    }

    // 11. Staff Management (Multiple Roles Support)
    public function staffMembers()
    {
        $staff = DB::table('staff_members')
            ->leftJoin('stores', 'staff_members.store_id', '=', 'stores.id')
            ->select('staff_members.*', 'stores.name as store_name')
            ->orderBy('staff_members.id', 'desc')
            ->get();

        $stores = DB::table('stores')->get();

        return view('admin.staff.index', compact('staff', 'stores'));
    }

    public function storeStaffMember(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:staff_members,phone',
            'email' => 'nullable|email',
            'store_id' => 'required|exists:stores,id',
            'roles' => 'required|array',
        ]);

        $rolesJson = json_encode($validated['roles']);

        DB::table('staff_members')->insert([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'store_id' => $validated['store_id'],
            'roles' => $rolesJson,
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // If 'delivery_staff' is selected, also register in delivery_partners for order dispatch assignment
        if (in_array('delivery_staff', $validated['roles'])) {
            DB::table('delivery_partners')->insert([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'email' => $validated['email'] ?? null,
                'status' => 'Active',
                'is_online' => true,
                'assigned_store_id' => $validated['store_id'],
                'joining_date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'New Staff member created and assigned roles successfully!');
    }

    // 12. Categories Management (Create, Image File Upload, Icon Selector & Edit)
    public function categories()
    {
        $catCols = \Illuminate\Support\Facades\Schema::getColumnListing('categories');
        if (!in_array('show_on_homepage', $catCols)) {
            \Illuminate\Support\Facades\Schema::table('categories', function ($table) {
                $table->boolean('show_on_homepage')->default(true);
            });
        }
        if (!in_array('icon', $catCols)) {
            \Illuminate\Support\Facades\Schema::table('categories', function ($table) {
                $table->string('icon')->nullable()->default('shopping_bag_outlined');
            });
        }
        $categories = DB::table('categories')->orderBy('display_order', 'asc')->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'image_url' => 'nullable|string',
            'image_file' => 'nullable|image|max:4096',
        ]);

        $imageUrl = $validated['image_url'] ?? null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/categories'), $filename);
            $imageUrl = url('uploads/categories/' . $filename);
        }

        $slug = \Illuminate\Support\Str::slug($validated['name']) . '-' . rand(100, 999);

        DB::table('categories')->insert([
            'name' => $validated['name'],
            'slug' => $slug,
            'icon' => $validated['icon'] ?? 'shopping_bag_outlined',
            'image' => $imageUrl,
            'display_order' => $validated['display_order'] ?? 0,
            'show_on_homepage' => $request->has('show_on_homepage'),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/categories')->with('success', 'Category created successfully!');
    }

    public function updateCategory(Request $request)
    {
        $id = $request->input('id');
        $validated = $request->validate([
            'name' => 'required|string',
            'icon' => 'nullable|string',
            'display_order' => 'nullable|integer',
            'image_url' => 'nullable|string',
            'image_file' => 'nullable|image|max:4096',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'icon' => $validated['icon'] ?? 'shopping_bag_outlined',
            'display_order' => $validated['display_order'] ?? 0,
            'show_on_homepage' => $request->has('show_on_homepage'),
            'updated_at' => now(),
        ];

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/categories'), $filename);
            $updateData['image'] = url('uploads/categories/' . $filename);
        } elseif (!empty($validated['image_url'])) {
            $updateData['image'] = $validated['image_url'];
        }

        DB::table('categories')->where('id', $id)->update($updateData);

        return redirect('/admin/categories')->with('success', 'Category updated successfully!');
    }

    /**
     * Helper to process, compress and convert uploaded image files to high-performance WebP format (~35KB typical)
     */
    private function convertToWebP($file, $destinationDir, $prefix = 'img')
    {
        $dirPath = public_path($destinationDir);
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        $fileName = $prefix . '_' . time() . '_' . uniqid() . '.webp';
        $fullPath = $dirPath . '/' . $fileName;

        $sourcePath = $file->getRealPath();
        $mime = $file->getMimeType();

        $image = null;
        if (str_contains($mime, 'png') && function_exists('imagecreatefrompng')) {
            $image = @imagecreatefrompng($sourcePath);
        } elseif ((str_contains($mime, 'jpeg') || str_contains($mime, 'jpg')) && function_exists('imagecreatefromjpeg')) {
            $image = @imagecreatefromjpeg($sourcePath);
        } elseif (str_contains($mime, 'webp') && function_exists('imagecreatefromwebp')) {
            $image = @imagecreatefromwebp($sourcePath);
        } elseif (str_contains($mime, 'gif') && function_exists('imagecreatefromgif')) {
            $image = @imagecreatefromgif($sourcePath);
        }

        if ($image && function_exists('imagewebp')) {
            imagealphablending($image, true);
            imagesavealpha($image, true);
            imagewebp($image, $fullPath, 82);
            imagedestroy($image);
            return '/' . trim($destinationDir, '/') . '/' . $fileName;
        }

        $fallbackName = $prefix . '_' . time() . '_' . $file->getClientOriginalName();
        $file->move($dirPath, $fallbackName);
        return '/' . trim($destinationDir, '/') . '/' . $fallbackName;
    }

    // 8. Enterprise Coupon Management
    public function coupons()
    {
        $coupons = DB::table('coupons')
            ->leftJoin('stores', 'coupons.allowed_store_id', '=', 'stores.id')
            ->select('coupons.*', 'stores.name as store_name')
            ->orderBy('coupons.id', 'desc')
            ->get();
        return view('admin.coupons.index', compact('coupons'));
    }

    public function createCoupon()
    {
        $stores = DB::table('stores')->where('is_active', true)->get();
        return view('admin.coupons.create', compact('stores'));
    }

    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:coupons,code',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:flat,percentage,free_delivery',
            'discount_value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'min_cart_amount' => 'nullable|numeric|min:0',
            'allowed_order_type' => 'required|in:all,delivery,pickup',
            'allowed_store_id' => 'nullable|exists:stores,id',
            'allowed_user_phones' => 'nullable|string',
            'max_global_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        DB::table('coupons')->insert([
            'code' => strtoupper(trim($validated['code'])),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'max_discount_amount' => $validated['max_discount_amount'] ?? null,
            'min_cart_amount' => $validated['min_cart_amount'] ?? 0.00,
            'is_free_delivery' => $validated['discount_type'] === 'free_delivery' || $request->has('is_free_delivery'),
            'is_first_order_only' => $request->has('is_first_order_only'),
            'allowed_order_type' => $validated['allowed_order_type'],
            'allowed_store_id' => $validated['allowed_store_id'] ?? null,
            'allowed_user_phones' => $validated['allowed_user_phones'] ?? null,
            'restrict_one_device' => $request->has('restrict_one_device'),
            'allowed_payment_method' => $request->input('allowed_payment_method'),
            'max_global_uses' => $validated['max_global_uses'] ?? null,
            'max_uses_per_user' => $validated['max_uses_per_user'] ?? 1,
            'start_date' => $validated['start_date'] ? Carbon\Carbon::parse($validated['start_date']) : null,
            'end_date' => $validated['end_date'] ? Carbon\Carbon::parse($validated['end_date']) : null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/coupons')->with('success', 'Coupon created successfully with configured security rules!');
    }

    public function toggleCoupon($id)
    {
        $coupon = DB::table('coupons')->where('id', $id)->first();
        if ($coupon) {
            DB::table('coupons')->where('id', $id)->update([
                'is_active' => !$coupon->is_active,
                'updated_at' => now(),
            ]);
        }
        return redirect()->back()->with('success', 'Coupon status updated successfully!');
    }

    public function deleteCoupon($id)
    {
        DB::table('coupons')->where('id', $id)->delete();
        return redirect()->back()->with('success', 'Coupon deleted successfully!');
    }
}
