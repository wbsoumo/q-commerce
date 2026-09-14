<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // Auto Database Import & Installer Route
    public function importDatabase()
    {
        try {
            Artisan::call('migrate:fresh', ['--force' => true]);
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\QCommerceSeeder',
                '--force' => true
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Multi-Vendor Multi-Store Database tables successfully imported & seeded!',
                'timestamp' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database import failed: ' . $e->getMessage()
            ], 500);
        }
    }

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

    // Save Product with Scope (Global vs Store-Specific)
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
            'store_id' => 'nullable|required_if:scope,store_specific|exists:stores,id',
        ]);

        DB::table('products')->insert([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'unit' => $validated['unit'],
            'price' => $validated['price'],
            'mrp' => $validated['mrp'],
            'stock' => $validated['stock'],
            'scope' => $validated['scope'],
            'store_id' => $validated['scope'] === 'store_specific' ? $validated['store_id'] : null,
            'image' => $request->input('image', 'image 41.png'),
            'description' => $request->input('description', ''),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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
            'store_id' => 'nullable|required_if:scope,store_specific|exists:stores,id',
        ]);

        DB::table('products')->where('id', $id)->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'unit' => $validated['unit'],
            'price' => $validated['price'],
            'mrp' => $validated['mrp'],
            'stock' => $validated['stock'],
            'scope' => $validated['scope'],
            'store_id' => $validated['scope'] === 'store_specific' ? $validated['store_id'] : null,
            'updated_at' => now(),
        ]);

        return redirect('/admin/products')->with('success', 'Product updated successfully!');
    }

    // STORE MANAGER PORTAL VIEW: Specific Store Inventory Management (With Search)
    public function storeManagerPortal(Request $request)
    {
        $storeId = (int)$request->query('store_id', 1);
        $store = DB::table('stores')->where('id', $storeId)->first();

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
            ->where(function($q) use ($storeId) {
                $q->where('products.scope', '=', 'global')
                  ->orWhere('products.store_id', '=', $storeId);
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'LIKE', "%{$search}%")
                  ->orWhere('products.sku', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->get();
        $stores = DB::table('stores')->get();

        return view('admin.manager.index', compact('store', 'products', 'stores'));
    }

    // Update Store Manager Inventory
    public function updateStoreInventory(Request $request)
    {
        $storeId = $request->input('store_id');
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
        $store = DB::table('stores')->where('id', $id)->first();
        return view('admin.stores.settings', compact('store'));
    }

    public function updateStoreSettings(Request $request, $id)
    {
        DB::table('stores')->where('id', $id)->update([
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
        ]);

        return redirect('/admin/stores')->with('success', 'Store settings updated successfully!');
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

    // 4. Order Details & Timeline History
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
        $orders = DB::table('orders')->where('user_phone', $customer->phone ?? '')->get();
        return view('admin.customers.show', compact('customer', 'orders'));
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
}
