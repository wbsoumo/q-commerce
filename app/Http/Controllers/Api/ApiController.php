<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    // Auto-Select Store based on user coordinates
    public function selectStore(Request $request)
    {
        $lat = $request->query('lat', 23.4013);
        $lng = $request->query('lng', 88.5010);

        // Fetch active store
        $store = DB::table('stores')->where('is_active', true)->first();

        return response()->json([
            'status' => 'success',
            'store' => $store ?? [
                'name' => 'Krishnanagar Main Store',
                'address' => '11E Krishnanagar Main Road',
                'delivery_time_mins' => 15,
            ]
        ]);
    }

    // Get All Categories
    public function getCategories()
    {
        $categories = DB::table('categories')
            ->where('is_active', true)
            ->orderBy('display_order', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    // Get Products by Category
    public function getProducts(Request $request)
    {
        $categoryId = $request->query('category_id');
        $query = DB::table('products')->where('is_active', true);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->get();

        return response()->json([
            'status' => 'success',
            'count' => $products->count(),
            'data' => $products
        ]);
    }

    // Create New Order
    public function createOrder(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string',
            'user_phone' => 'required|string',
            'delivery_address' => 'required|string',
            'items' => 'required|array',
            'subtotal' => 'required|numeric',
            'grand_total' => 'required|numeric',
        ]);

        $orderNumber = 'ORD-' . strtoupper(uniqid());

        $orderId = DB::table('orders')->insertGetId([
            'order_number' => $orderNumber,
            'user_name' => $validated['user_name'],
            'user_phone' => $validated['user_phone'],
            'delivery_address' => $validated['delivery_address'],
            'subtotal' => $validated['subtotal'],
            'delivery_fee' => 15.00,
            'grand_total' => $validated['grand_total'],
            'payment_method' => $request->input('payment_method', 'PhonePe UPI'),
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($validated['items'] as $item) {
            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'product_id' => $item['product_id'] ?? null,
                'product_name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'total' => $item['price'] * $item['quantity'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order placed successfully',
            'order_number' => $orderNumber,
        ], 201);
    }
}
