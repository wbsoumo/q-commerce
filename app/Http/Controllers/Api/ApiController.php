<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\StoreOperationalService;
use App\Services\CheckoutValidationService;
use App\Services\InventoryService;
use Exception;

class ApiController extends Controller
{
    // Auto-Select Store based on user coordinates & Operational Check
    public function selectStore(Request $request)
    {
        $lat = (float)$request->query('lat', 23.4013);
        $lng = (float)$request->query('lng', 88.5010);

        // Fetch all active stores
        $stores = DB::table('stores')->where('is_active', true)->get();

        $selectedStore = null;
        $minDistanceKm = 999999;
        $isWithinCoverage = false;

        foreach ($stores as $store) {
            $storeLat = (float)($store->latitude ?? 23.4013);
            $storeLng = (float)($store->longitude ?? 88.5010);
            $radiusKm = (float)($store->delivery_radius_km ?? 15.0);

            // Haversine distance calculation formula
            $earthRadiusKm = 6371.0;
            $dLat = deg2rad($storeLat - $lat);
            $dLng = deg2rad($storeLng - $lng);
            $a = sin($dLat / 2) * sin($dLat / 2) +
                 cos(deg2rad($lat)) * cos(deg2rad($storeLat)) *
                 sin($dLng / 2) * sin($dLng / 2);
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distanceKm = $earthRadiusKm * $c;

            if ($distanceKm < $minDistanceKm) {
                $minDistanceKm = $distanceKm;
                $selectedStore = $store;
                if ($distanceKm <= $radiusKm) {
                    $isWithinCoverage = true;
                }
            }
        }

        if ($selectedStore) {
            $opStatus = StoreOperationalService::checkStoreStatus($selectedStore);
            $selectedStore->is_operational = $isWithinCoverage && $opStatus['is_operational'];
            $selectedStore->is_serviceable = $isWithinCoverage;
            $selectedStore->distance_km = round($minDistanceKm, 2);
            $selectedStore->closure_reason = !$isWithinCoverage
                ? "We are currently not available at your location. Distance to nearest store is " . round($minDistanceKm, 1) . " km (Coverage limit: " . ($selectedStore->delivery_radius_km ?? 15) . " km)."
                : $opStatus['reason'];
            $selectedStore->delivery_time_mins = $selectedStore->estimated_delivery_time_mins ?? 15;
        }

        return response()->json([
            'status' => 'success',
            'is_serviceable' => $isWithinCoverage,
            'store' => $selectedStore ?? [
                'name' => 'Krishnanagar Main Store',
                'address' => '11E Krishnanagar Main Road',
                'delivery_time_mins' => 15,
                'is_operational' => true,
                'is_serviceable' => true,
                'closure_reason' => 'Store is open and operational.',
            ]
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
          ->header('Access-Control-Allow-Headers', '*');
    }

    // Ultra-Fast Light-Weight Sync Status Checker API
    public function checkSyncStatus(Request $request)
    {
        $storeId = $request->query('store_id', 1);

        $store = DB::table('stores')->where('id', $storeId)->first();
        $storeVersion = md5(json_encode([
            $store->name ?? '',
            $store->banner_title ?? '',
            $store->banner_color ?? '',
            $store->search_hint ?? '',
            $store->updated_at ?? ''
        ]));

        $lastCategoryUpdate = DB::table('categories')->max('updated_at') ?? '1970-01-01 00:00:00';
        $categoryCount = DB::table('categories')->where('is_active', true)->count();
        $categoriesVersion = md5("{$lastCategoryUpdate}_{$categoryCount}");

        $lastProductUpdate = DB::table('products')->max('updated_at') ?? '1970-01-01 00:00:00';
        $productCount = DB::table('products')->where('is_active', true)->count();
        $productsVersion = md5("{$lastProductUpdate}_{$productCount}");

        return response()->json([
            'status' => 'success',
            'versions' => [
                'store' => $storeVersion,
                'categories' => $categoriesVersion,
                'products' => $productsVersion,
            ],
            'last_updated' => [
                'categories' => $lastCategoryUpdate,
                'products' => $lastProductUpdate,
            ],
            'server_time' => now()->toIso8601String(),
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
          ->header('Access-Control-Allow-Headers', '*');
    }

    // Get All Categories with ETag & Incremental Sync Support
    public function getCategories(Request $request)
    {
        $query = DB::table('categories')->where('is_active', true);
        
        $updatedSince = $request->query('updated_since');
        if ($updatedSince) {
            $query->where('updated_at', '>', $updatedSince);
        }

        $categories = $query->orderBy('display_order', 'asc')->get();
        $etag = md5(json_encode($categories));

        if ($request->header('If-None-Match') === $etag) {
            return response()->json(['status' => 'not_modified'], 304)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
                ->header('Access-Control-Allow-Headers', '*');
        }

        return response()->json([
            'status' => 'success',
            'data' => $categories,
            'server_time' => now()->toIso8601String(),
        ])->header('ETag', $etag)
          ->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
          ->header('Access-Control-Allow-Headers', '*');
    }

    // Get Products by Category with Incremental Sync & ETag Support
    public function getProducts(Request $request)
    {
        $categoryId = $request->query('category_id');
        $storeId = $request->query('store_id', 1);
        $updatedSince = $request->query('updated_since');

        $query = DB::table('products')->where('products.is_active', true);

        if ($categoryId) {
            $query->where('products.category_id', $categoryId);
        }

        if ($updatedSince) {
            $query->where('products.updated_at', '>', $updatedSince);
        }

        // Left join store product overrides & categories
        $products = $query
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('store_product_inventories', function($join) use ($storeId) {
                $join->on('products.id', '=', 'store_product_inventories.product_id')
                     ->where('store_product_inventories.store_id', '=', $storeId);
            })
            ->select(
                'products.*',
                'categories.name as category_name',
                'store_product_inventories.custom_price',
                'store_product_inventories.custom_mrp',
                'store_product_inventories.custom_stock',
                'store_product_inventories.custom_reserved_stock',
                'store_product_inventories.is_available'
            )
            ->get();

        // Attach Variants if product has variants
        foreach ($products as $prod) {
            if ($prod->has_variants) {
                $prod->variants = DB::table('product_variants')
                    ->where('product_id', $prod->id)
                    ->where('is_active', true)
                    ->get();
            } else {
                $prod->variants = [];
            }

            // Calculate Effective Price & Available Stock
            $prod->effective_price = $prod->custom_price ?? $prod->price;
            $prod->effective_mrp = $prod->custom_mrp ?? $prod->mrp;
            $totalStock = $prod->custom_stock ?? $prod->stock;
            $resStock = $prod->custom_reserved_stock ?? $prod->reserved_stock;
            $prod->available_stock = max(0, $totalStock - $resStock);
        }

        $etag = md5(json_encode($products));
        if ($request->header('If-None-Match') === $etag) {
            return response()->json(['status' => 'not_modified'], 304)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
                ->header('Access-Control-Allow-Headers', '*');
        }

        return response()->json([
            'status' => 'success',
            'count' => $products->count(),
            'data' => $products,
            'server_time' => now()->toIso8601String(),
        ])->header('ETag', $etag)
          ->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
          ->header('Access-Control-Allow-Headers', '*');
    }

    // Create New Order with Advanced Validation & Stock Reservation
    public function createOrder(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_name' => 'required|string',
                'user_phone' => 'required|string',
                'delivery_address' => 'required|string',
                'items' => 'required|array',
                'store_id' => 'nullable|integer',
            ]);

            // Run Server-Side Validation & Fee Calculation Engine
            $checkoutResult = CheckoutValidationService::validateAndCalculateCheckout(array_merge($request->all(), [
                'store_id' => $request->input('store_id', 1)
            ]));

            return DB::transaction(function() use ($validatedData, $checkoutResult, $request) {
                $orderNumber = 'ORD-' . strtoupper(uniqid());

                // 1. Insert Order Record
                $orderId = DB::table('orders')->insertGetId([
                    'order_number' => $orderNumber,
                    'store_id' => $checkoutResult['store_id'],
                    'user_name' => $validatedData['user_name'],
                    'user_phone' => $validatedData['user_phone'],
                    'delivery_address' => $validatedData['delivery_address'],
                    'latitude' => $request->input('latitude', 23.4126),
                    'longitude' => $request->input('longitude', 88.4292),
                    'subtotal' => $checkoutResult['subtotal'],
                    'delivery_fee' => $checkoutResult['delivery_fee'],
                    'grand_total' => $checkoutResult['grand_total'],
                    'payment_method' => $request->input('payment_method', 'PhonePe UPI'),
                    'status' => 'Pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 2. Insert Order Items & Reserve Stock
                foreach ($checkoutResult['items'] as $item) {
                    DB::table('order_items')->insert([
                        'order_id' => $orderId,
                        'product_id' => $item['product_id'],
                        'product_name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Reserve Stock atomically
                    InventoryService::reserveStock(
                        $item['product_id'],
                        $checkoutResult['store_id'],
                        $item['quantity'],
                        $orderNumber,
                        $item['variant_id'] ?? null
                    );
                }

                // 3. Log Initial Status History
                DB::table('order_status_histories')->insert([
                    'order_id' => $orderId,
                    'previous_status' => null,
                    'new_status' => 'Pending',
                    'reason' => 'Customer created order via Mobile App',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 4. Record/Update Customer Record
                $customer = DB::table('customers')->where('phone', $validatedData['user_phone'])->first();
                if ($customer) {
                    DB::table('customers')->where('id', $customer->id)->update([
                        'total_orders' => $customer->total_orders + 1,
                        'total_spent' => $customer->total_spent + $checkoutResult['grand_total'],
                        'last_order_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('customers')->insert([
                        'name' => $validatedData['user_name'],
                        'phone' => $validatedData['user_phone'],
                        'status' => 'Active',
                        'total_orders' => 1,
                        'total_spent' => $checkoutResult['grand_total'],
                        'last_order_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Order placed successfully and stock reserved.',
                    'order_number' => $orderNumber,
                    'grand_total' => $checkoutResult['grand_total'],
                ], 201);
            });

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 422);
        }
    }

    // Get User Saved Addresses
    public function getAddresses(Request $request)
    {
        $userPhone = $request->query('phone', '8016222991');
        $addresses = DB::table('user_addresses')
            ->where('user_phone', $userPhone)
            ->orderBy('is_default', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $addresses,
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
          ->header('Access-Control-Allow-Headers', '*');
    }

    // Save New User Address with Custom Type & Receiver Info
    public function storeAddress(Request $request)
    {
        try {
            $validated = $request->validate([
                'address_type' => 'required|string', // Home, Work, Other
                'custom_type_name' => 'nullable|string',
                'address_details' => 'required|string',
                'receiver_name' => 'required|string',
                'receiver_phone' => 'required|string',
                'is_for_someone_else' => 'nullable|boolean',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'user_phone' => 'nullable|string',
            ]);

            $id = DB::table('user_addresses')->insertGetId([
                'user_phone' => $validated['user_phone'] ?? '8016222991',
                'address_type' => $validated['address_type'],
                'custom_type_name' => $validated['custom_type_name'] ?? null,
                'address_details' => $validated['address_details'],
                'receiver_name' => $validated['receiver_name'],
                'receiver_phone' => $validated['receiver_phone'],
                'is_for_someone_else' => $request->input('is_for_someone_else', false),
                'latitude' => $request->input('latitude', 23.4126),
                'longitude' => $request->input('longitude', 88.4292),
                'is_default' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $addressRecord = DB::table('user_addresses')->where('id', $id)->first();

            return response()->json([
                'status' => 'success',
                'message' => 'Address saved successfully.',
                'data' => $addressRecord,
            ], 201)->header('Access-Control-Allow-Origin', '*')
              ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
              ->header('Access-Control-Allow-Headers', '*');
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 422)->header('Access-Control-Allow-Origin', '*');
        }
    }
}
