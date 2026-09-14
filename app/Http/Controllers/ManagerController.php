<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    // Dedicated Manager Dashboard View
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user->role !== 'store_manager' && $user->role !== 'admin')) {
            return redirect('/manager/login')->with('error', 'Unauthorized access.');
        }

        $storeId = (int)($user->store_id ?? 1);
        $store = DB::table('stores')->where('id', $storeId)->first();

        // 1. Fetch Store Orders
        $ordersQuery = DB::table('orders')->where('store_id', $storeId)->orderBy('id', 'desc');

        if ($request->filled('order_status')) {
            $ordersQuery->where('status', $request->order_status);
        }

        $orders = $ordersQuery->take(20)->get();

        // 2. Fetch Store Product Catalog & Inventories
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
            ->where(function($q) use ($storeId) {
                $q->where('products.scope', '=', 'global')
                  ->orWhere('products.store_id', '=', $storeId);
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $productsQuery->where(function($q) use ($search) {
                $q->where('products.name', 'LIKE', "%{$search}%")
                  ->orWhere('products.sku', 'LIKE', "%{$search}%");
            });
        }

        $products = $productsQuery->get();

        // 3. Store Delivery Personnel / Riders
        $riders = DB::table('delivery_partners')->where('status', 'Active')->get();

        // 4. Store Delivery List
        $deliveries = DB::table('deliveries')
            ->leftJoin('orders', 'deliveries.order_id', '=', 'orders.id')
            ->leftJoin('delivery_partners', 'deliveries.delivery_partner_id', '=', 'delivery_partners.id')
            ->select('deliveries.*', 'orders.order_number', 'orders.grand_total', 'orders.status as order_status', 'delivery_partners.name as rider_name')
            ->where('deliveries.store_id', $storeId)
            ->orderBy('deliveries.id', 'desc')
            ->get();

        // Stats
        $pendingOrdersCount = DB::table('orders')->where('store_id', $storeId)->where('status', 'Pending')->count();
        $totalOrdersCount = DB::table('orders')->where('store_id', $storeId)->count();
        $outForDeliveryCount = DB::table('orders')->where('store_id', $storeId)->where('status', 'Out for Delivery')->count();

        return view('manager.dashboard', compact(
            'store',
            'products',
            'orders',
            'deliveries',
            'riders',
            'pendingOrdersCount',
            'totalOrdersCount',
            'outForDeliveryCount'
        ));
    }

    // Manager: Update Branch Price & Stock
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

    // Manager: Update Order Status & Assign Delivery Rider
    public function updateOrderStatus(Request $request, $id)
    {
        $user = Auth::user();
        $storeId = (int)($user->store_id ?? 1);

        $order = DB::table('orders')->where('id', $id)->where('store_id', $storeId)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found or does not belong to your store branch.');
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

    // Manager: Assign Delivery Partner / Rider
    public function assignRider(Request $request)
    {
        $user = Auth::user();
        $storeId = (int)($user->store_id ?? 1);
        $deliveryId = $request->input('delivery_id');
        $riderId = $request->input('delivery_partner_id');

        $delivery = DB::table('deliveries')->where('id', $deliveryId)->where('store_id', $storeId)->first();
        if (!$delivery) {
            return redirect()->back()->with('error', 'Delivery assignment unauthorized for this store branch.');
        }

        DB::table('deliveries')->where('id', $deliveryId)->update([
            'delivery_partner_id' => $riderId,
            'delivery_status' => 'Assigned',
            'assigned_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Delivery partner assigned successfully!');
    }

    // Manager: Update Branch Operational Hours & Settings
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
}
