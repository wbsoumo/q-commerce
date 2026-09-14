<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ManagerController extends Controller
{
    private function getStoreData()
    {
        $user = Auth::user();
        if (!$user || ($user->role !== 'store_manager' && $user->role !== 'admin')) {
            return null;
        }
        $storeId = (int)($user->store_id ?? 1);
        return DB::table('stores')->where('id', $storeId)->first();
    }

    // 0. Store Manager Main Dashboard (Today's Details Only)
    public function dashboard()
    {
        $store = $this->getStoreData();
        if (!$store) return redirect('/manager/login');
        $storeId = $store->id;
        $today = now()->toDateString();

        // Today's Metrics
        $todayOrdersCount = DB::table('orders')
            ->where('store_id', $storeId)
            ->whereDate('created_at', $today)
            ->count();

        $todayPendingCount = DB::table('orders')
            ->where('store_id', $storeId)
            ->where('status', 'Pending')
            ->whereDate('created_at', $today)
            ->count();

        $todayDispatchedCount = DB::table('orders')
            ->where('store_id', $storeId)
            ->where('status', 'Out for Delivery')
            ->whereDate('created_at', $today)
            ->count();

        $todayDeliveredCount = DB::table('orders')
            ->where('store_id', $storeId)
            ->where('status', 'Delivered')
            ->whereDate('created_at', $today)
            ->count();

        $todayRevenue = DB::table('orders')
            ->where('store_id', $storeId)
            ->where('status', 'Delivered')
            ->whereDate('created_at', $today)
            ->sum('grand_total');

        // Today's Recent Orders
        $recentOrders = DB::table('orders')
            ->where('store_id', $storeId)
            ->whereDate('created_at', $today)
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        // If no orders today, fall back to latest branch orders for display
        if ($recentOrders->isEmpty()) {
            $recentOrders = DB::table('orders')
                ->where('store_id', $storeId)
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();
        }

        // Active Deliveries Today
        $recentDeliveries = DB::table('deliveries')
            ->leftJoin('orders', 'deliveries.order_id', '=', 'orders.id')
            ->leftJoin('delivery_partners', 'deliveries.delivery_partner_id', '=', 'delivery_partners.id')
            ->select('deliveries.*', 'orders.order_number', 'orders.grand_total', 'orders.status as order_status', 'delivery_partners.name as rider_name')
            ->where('deliveries.store_id', $storeId)
            ->orderBy('deliveries.id', 'desc')
            ->take(5)
            ->get();

        return view('manager.dashboard', compact(
            'store',
            'todayOrdersCount',
            'todayPendingCount',
            'todayDispatchedCount',
            'todayDeliveredCount',
            'todayRevenue',
            'recentOrders',
            'recentDeliveries'
        ));
    }

    // 1. Live Inventory & Pricing Page
    public function inventory(Request $request)
    {
        $store = $this->getStoreData();
        if (!$store) return redirect('/manager/login');
        $storeId = $store->id;

        $hasStoreIdsCol = Schema::hasColumn('products', 'store_ids');
        $productsQuery = DB::table('products')
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
            $productsQuery->where(function($q) use ($search) {
                $q->where('products.name', 'LIKE', "%{$search}%")
                  ->orWhere('products.sku', 'LIKE', "%{$search}%");
            });
        }

        $products = $productsQuery->get();
        return view('manager.inventory', compact('store', 'products'));
    }

    // 1.1 Create Product Form Page for Store Managers
    public function createProduct()
    {
        $store = $this->getStoreData();
        if (!$store) return redirect('/manager/login');

        $categories = DB::table('categories')->where('is_active', true)->get();
        return view('manager.products.create', compact('store', 'categories'));
    }

    // 1.2 Edit Product Form Page for Store Managers
    public function editProduct($id)
    {
        $store = $this->getStoreData();
        if (!$store) return redirect('/manager/login');

        $product = DB::table('products')->where('id', $id)->first();
        if (!$product) {
            return redirect('/manager/inventory')->with('error', 'Product not found.');
        }

        $inventory = DB::table('store_product_inventories')
            ->where('store_id', $store->id)
            ->where('product_id', $id)
            ->first();

        $categories = DB::table('categories')->where('is_active', true)->get();
        return view('manager.products.edit', compact('store', 'product', 'inventory', 'categories'));
    }

    // 1.3 Update Product Handler for Store Managers
    public function updateProduct(Request $request)
    {
        $store = $this->getStoreData();
        if (!$store) return redirect('/manager/login');
        $storeId = $store->id;

        $id = $request->input('id');
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string',
            'sku' => 'required|string|unique:products,sku,' . $id,
            'unit' => 'required|string',
            'price' => 'required|numeric',
            'mrp' => 'required|numeric',
            'custom_stock' => 'required|integer',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'gallery_files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $updateData = [
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'unit' => $validated['unit'],
            'description' => $request->input('description', ''),
            'updated_at' => now(),
        ];

        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/products'), $fileName);
            $updateData['image'] = 'uploads/products/' . $fileName;
        }

        if ($request->hasFile('gallery_files')) {
            $galleryPaths = [];
            foreach ($request->file('gallery_files') as $idx => $gFile) {
                $gName = time() . '_gal_' . $idx . '_' . $gFile->getClientOriginalName();
                $gFile->move(public_path('uploads/products'), $gName);
                $galleryPaths[] = 'uploads/products/' . $gName;
            }
            $updateData['gallery'] = json_encode($galleryPaths);
        }

        // Update main product details
        DB::table('products')->where('id', $id)->update($updateData);

        // Update branch inventory override
        DB::table('store_product_inventories')->updateOrInsert(
            ['store_id' => $storeId, 'product_id' => $id],
            [
                'custom_price' => $validated['price'],
                'custom_mrp' => $validated['mrp'],
                'custom_stock' => $validated['custom_stock'],
                'updated_at' => now(),
            ]
        );

        return redirect('/manager/inventory')->with('success', 'Product updated successfully!');
    }

    // 2. Store Orders Page
    public function orders(Request $request)
    {
        $store = $this->getStoreData();
        if (!$store) return redirect('/manager/login');
        $storeId = $store->id;

        $ordersQuery = DB::table('orders')->where('store_id', $storeId)->orderBy('id', 'desc');

        if ($request->filled('status')) {
            $ordersQuery->where('status', $request->status);
        }

        $orders = $ordersQuery->get();
        return view('manager.orders', compact('store', 'orders'));
    }

    // 3. Delivery Dispatch Page
    public function deliveries(Request $request)
    {
        $store = $this->getStoreData();
        if (!$store) return redirect('/manager/login');
        $storeId = $store->id;

        $deliveries = DB::table('deliveries')
            ->leftJoin('orders', 'deliveries.order_id', '=', 'orders.id')
            ->leftJoin('delivery_partners', 'deliveries.delivery_partner_id', '=', 'delivery_partners.id')
            ->select('deliveries.*', 'orders.order_number', 'orders.grand_total', 'orders.status as order_status', 'delivery_partners.name as rider_name')
            ->where('deliveries.store_id', $storeId)
            ->orderBy('deliveries.id', 'desc')
            ->get();

        $riders = DB::table('delivery_partners')->where('status', 'Active')->get();

        return view('manager.deliveries', compact('store', 'deliveries', 'riders'));
    }

    // 4. Branch Operations Settings Page
    public function settings()
    {
        $store = $this->getStoreData();
        if (!$store) return redirect('/manager/login');

        return view('manager.settings', compact('store'));
    }

    // Manager Actions
    public function updateInventory(Request $request)
    {
        $user = Auth::user();
        $storeId = (int)($user->store_id ?? 1);
        $productId = (int)$request->input('product_id');

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

        return redirect()->back()->with('success', 'Branch product price & stock updated!');
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $user = Auth::user();
        $storeId = (int)($user->store_id ?? 1);

        $order = DB::table('orders')->where('id', $id)->where('store_id', $storeId)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found or unauthorized.');
        }

        $newStatus = $request->input('status');
        DB::table('orders')->where('id', $id)->update(['status' => $newStatus, 'updated_at' => now()]);

        DB::table('order_status_histories')->insert([
            'order_id' => $id,
            'previous_status' => $order->status,
            'new_status' => $newStatus,
            'reason' => $request->input('reason', 'Status updated by Store Manager'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', "Order #{$order->order_number} status updated to {$newStatus}!");
    }

    public function assignRider(Request $request)
    {
        $user = Auth::user();
        $storeId = (int)($user->store_id ?? 1);
        $deliveryId = $request->input('delivery_id');
        $riderId = $request->input('delivery_partner_id');

        $delivery = DB::table('deliveries')->where('id', $deliveryId)->where('store_id', $storeId)->first();
        if (!$delivery) {
            return redirect()->back()->with('error', 'Delivery assignment unauthorized.');
        }

        DB::table('deliveries')->where('id', $deliveryId)->update([
            'delivery_partner_id' => $riderId,
            'delivery_status' => 'Assigned',
            'assigned_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Delivery partner assigned successfully!');
    }

    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        $storeId = (int)($user->store_id ?? 1);

        DB::table('stores')->where('id', $storeId)->update([
            'status' => $request->input('status', 'Active'),
            'opening_time' => $request->input('opening_time', '06:00'),
            'closing_time' => $request->input('closing_time', '23:00'),
            'min_order_amount' => $request->input('min_order_amount', 0),
            'delivery_fee' => $request->input('delivery_fee', 15),
            'free_delivery_threshold' => $request->input('free_delivery_threshold', 299),
            'estimated_delivery_time_mins' => $request->input('estimated_delivery_time_mins', 15),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Branch operational settings updated successfully!');
    }

    // Manager Add Product to Store
    public function storeProduct(Request $request)
    {
        $user = Auth::user();
        $storeId = (int)($user->store_id ?? 1);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string',
            'sku' => 'required|string|unique:products,sku',
            'unit' => 'required|string',
            'price' => 'required|numeric',
            'mrp' => 'required|numeric',
            'stock' => 'required|integer',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'gallery_files.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ]);

        $imagePath = 'image 41.png';
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
        }

        DB::table('products')->insert([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'unit' => $validated['unit'],
            'price' => $validated['price'],
            'mrp' => $validated['mrp'],
            'stock' => $validated['stock'],
            'scope' => 'store_specific',
            'store_id' => $storeId,
            'image' => $imagePath,
            'gallery' => !empty($galleryPaths) ? json_encode($galleryPaths) : null,
            'description' => $request->input('description', ''),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/manager/inventory')->with('success', 'New product successfully added to your store branch!');
    }
}
