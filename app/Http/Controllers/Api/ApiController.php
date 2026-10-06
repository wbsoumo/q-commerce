<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\StoreOperationalService;
use App\Services\CheckoutValidationService;
use App\Services\InventoryService;
use App\Services\WalletService;
use Exception;

class ApiController extends Controller
{
    // Auto-Select Store based on user coordinates & Operational Check
    public function selectStore(Request $request)
    {
        $latInput = $request->query('lat');
        $lngInput = $request->query('lng');

        // Default to Krishnanagar coords if null/invalid
        $lat = ($latInput !== null && is_numeric($latInput)) ? (float)$latInput : 23.4013;
        $lng = ($lngInput !== null && is_numeric($lngInput)) ? (float)$lngInput : 88.5010;

        // Fetch all active stores
        $stores = DB::table('stores')->where('is_active', true)->get();

        $eligibleStores = [];
        $allStoresWithDistance = [];

        foreach ($stores as $store) {
            if ($store->latitude === null || $store->longitude === null) {
                continue;
            }

            $storeLat = (float)$store->latitude;
            $storeLng = (float)$store->longitude;
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

            $storeObj = clone $store;
            $storeObj->calculated_distance_km = $distanceKm;

            $allStoresWithDistance[] = $storeObj;

            // Delivery eligibility check takes priority over distance
            if ($distanceKm <= $radiusKm) {
                $eligibleStores[] = $storeObj;
            }
        }

        $selectedStore = null;
        $isWithinCoverage = false;
        $minDistanceKm = 999999.0;

        if (!empty($eligibleStores)) {
            // Sort eligible stores by distance ascending
            usort($eligibleStores, function ($a, $b) {
                return $a->calculated_distance_km <=> $b->calculated_distance_km;
            });
            $selectedStore = $eligibleStores[0];
            $isWithinCoverage = true;
            $minDistanceKm = $selectedStore->calculated_distance_km;
        } elseif (!empty($allStoresWithDistance)) {
            // No store eligible: pick nearest overall store for messaging
            usort($allStoresWithDistance, function ($a, $b) {
                return $a->calculated_distance_km <=> $b->calculated_distance_km;
            });
            $selectedStore = $allStoresWithDistance[0];
            $isWithinCoverage = false;
            $minDistanceKm = $selectedStore->calculated_distance_km;
        }

        // Fetch Global App Settings dynamically from app_settings table or active stores configuration
        $globalBannerColor = null;
        $globalBannerTitle = null;

        if (\Illuminate\Support\Facades\Schema::hasTable('app_settings')) {
            $appSettings = DB::table('app_settings')->first();
            if ($appSettings) {
                $globalBannerColor = $appSettings->banner_color ?? null;
                $globalBannerTitle = $appSettings->banner_title ?? null;
            }
        }

        if ($selectedStore) {
            $globalBannerColor = $globalBannerColor ?? $selectedStore->banner_color ?? null;
            $globalBannerTitle = $globalBannerTitle ?? $selectedStore->banner_title ?? null;
        }

        if (empty($globalBannerColor) || empty($globalBannerTitle)) {
            $fallbackStore = DB::table('stores')->where('is_active', true)->first();
            $globalBannerColor = $globalBannerColor ?? $fallbackStore->banner_color ?? '#0C831F';
            $globalBannerTitle = $globalBannerTitle ?? $fallbackStore->banner_title ?? 'Mega Diwali Sale';
        }

        if ($selectedStore) {
            $opStatus = StoreOperationalService::checkStoreStatus($selectedStore);
            $selectedStore->is_operational = $isWithinCoverage && ($opStatus['is_operational'] ?? true);
            $selectedStore->is_serviceable = $isWithinCoverage;
            $selectedStore->distance_km = round($minDistanceKm, 2);
            $selectedStore->closure_reason = !$isWithinCoverage
                ? "We are currently not available at your location. Distance to nearest store (" . ($selectedStore->name ?? 'Store') . ") is " . round($minDistanceKm, 1) . " km (Coverage limit: " . ($selectedStore->delivery_radius_km ?? 15) . " km)."
                : ($opStatus['reason'] ?? 'Store is open and operational.');
            $selectedStore->address = str_replace('RATANR FLAT, ', '', $selectedStore->address ?? '');
            $selectedStore->delivery_time_mins = $selectedStore->estimated_delivery_time_mins ?? 15;
            $selectedStore->banner_color = $globalBannerColor;
            $selectedStore->banner_title = $globalBannerTitle;
            $selectedStore->promo_cards = isset($selectedStore->promo_grid_json) && !empty($selectedStore->promo_grid_json)
                ? json_decode($selectedStore->promo_grid_json, true)
                : null;
        }

        return response()->json([
            'status' => 'success',
            'is_serviceable' => $isWithinCoverage,
            'global_settings' => [
                'banner_color' => $globalBannerColor,
                'banner_title' => $globalBannerTitle,
            ],
            'store' => $selectedStore ?? [
                'name' => 'Krishnanagar Main Store',
                'address' => '11E Krishnanagar Main Road',
                'delivery_time_mins' => 15,
                'banner_color' => $globalBannerColor,
                'banner_title' => $globalBannerTitle,
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

        $lastSliderUpdate = Schema::hasTable('sliders') ? (DB::table('sliders')->max('updated_at') ?? '1970-01-01 00:00:00') : '1970-01-01 00:00:00';
        $sliderCount = Schema::hasTable('sliders') ? DB::table('sliders')->where('is_active', true)->count() : 0;
        $slidersVersion = md5("{$lastSliderUpdate}_{$sliderCount}");

        $sinceTime = $request->query('updated_since');
        $changedProductIds = [];
        $deletedProductIds = [];

        if ($sinceTime) {
            $changedProductIds = DB::table('products')
                ->where('updated_at', '>', $sinceTime)
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();
            
            $deletedProductIds = DB::table('products')
                ->where('is_active', false)
                ->where('updated_at', '>', $sinceTime)
                ->pluck('id')
                ->toArray();
        }

        return response()->json([
            'status' => 'success',
            'versions' => [
                'store' => $storeVersion,
                'categories' => $categoriesVersion,
                'products' => $productsVersion,
                'sliders' => $slidersVersion,
            ],
            'last_updated' => [
                'categories' => $lastCategoryUpdate,
                'products' => $lastProductUpdate,
                'sliders' => $lastSliderUpdate,
            ],
            'delta' => [
                'changed_product_ids' => $changedProductIds,
                'deleted_product_ids' => $deletedProductIds,
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

        if ($request->query('special_deals') || $request->query('is_special_deal')) {
            $query->where('products.is_special_deal', true);
        }

        if (Schema::hasColumn('products', 'sort_order')) {
            $query->orderBy('products.sort_order', 'asc')->orderBy('products.id', 'desc');
        } else {
            $query->orderBy('products.id', 'desc');
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

        // Attach Variants & Format Image URLs
        foreach ($products as $prod) {
            if (!empty($prod->image) && !str_starts_with($prod->image, 'http://') && !str_starts_with($prod->image, 'https://')) {
                $prod->image = asset(ltrim($prod->image, '/'));
            }

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

            $orderType = $request->input('order_type', 'delivery');
            $deliveryFee = $orderType === 'pickup' ? 0.00 : $checkoutResult['delivery_fee'];
            $grandTotal = $checkoutResult['subtotal'] + $deliveryFee;

            WalletService::ensureSchema();

            $walletPaid = 0.00;
            if ($request->input('use_wallet', false)) {
                $orderTmpNum = ($orderType === 'pickup' ? 'PICK-' : 'ORD-') . strtoupper(uniqid());
                $walletPaid = WalletService::deductWalletForOrder($validatedData['user_phone'], $orderTmpNum, $grandTotal);
            }
            $payableAmount = max(0.00, $grandTotal - $walletPaid);

            return DB::transaction(function() use ($validatedData, $checkoutResult, $request, $orderType, $deliveryFee, $grandTotal, $walletPaid, $payableAmount) {
                $orderNumber = ($orderType === 'pickup' ? 'PICK-' : 'ORD-') . strtoupper(uniqid());

                // 1. Insert Order Record
                $orderId = DB::table('orders')->insertGetId([
                    'order_number' => $orderNumber,
                    'store_id' => $checkoutResult['store_id'],
                    'user_name' => $validatedData['user_name'],
                    'user_phone' => $validatedData['user_phone'],
                    'delivery_address' => $orderType === 'pickup' ? 'Self Pickup at Store' : $validatedData['delivery_address'],
                    'latitude' => $request->input('latitude', 23.4126),
                    'longitude' => $request->input('longitude', 88.4292),
                    'subtotal' => $checkoutResult['subtotal'],
                    'delivery_fee' => $deliveryFee,
                    'grand_total' => $grandTotal,
                    'wallet_paid' => $walletPaid,
                    'payable_amount' => $payableAmount,
                    'payment_method' => $request->input('payment_method', 'PhonePe UPI'),
                    'order_type' => $orderType,
                    'pickup_date' => $request->input('pickup_date'),
                    'pickup_time' => $request->input('pickup_time'),
                    'receiver_name' => $request->input('receiver_name'),
                    'receiver_phone' => $request->input('receiver_phone'),
                    'is_for_someone_else' => $request->input('is_for_someone_else', false),
                    'status' => 'Pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 1b. Insert Store Pickup Record if Pickup Order and table exists
                if ($orderType === 'pickup' && \Illuminate\Support\Facades\Schema::hasTable('store_pickup_orders')) {
                    try {
                        $store = DB::table('stores')->where('id', $checkoutResult['store_id'])->first();
                        DB::table('store_pickup_orders')->insert([
                            'order_id' => $orderId,
                            'store_id' => $checkoutResult['store_id'],
                            'customer_name' => $request->input('receiver_name') ?: $validatedData['user_name'],
                            'customer_phone' => $request->input('receiver_phone') ?: $validatedData['user_phone'],
                            'pickup_date' => $request->input('pickup_date') ?: now()->toDateString(),
                            'pickup_slot_time' => $request->input('pickup_time') ?: '10:00 AM - 11:00 AM',
                            'store_opening_time' => $store->opening_time ?? '06:00 AM',
                            'store_closing_time' => $store->closing_time ?? '11:00 PM',
                            'pickup_status' => 'Scheduled',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } catch (\Throwable $pickupEx) {}
                }

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
                    'reason' => $orderType === 'pickup' ? 'Customer placed Store Pickup order' : 'Customer placed Home Delivery order',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // 4. Record/Update Customer Record
                $customer = DB::table('customers')->where('phone', $validatedData['user_phone'])->first();
                if ($customer) {
                    DB::table('customers')->where('id', $customer->id)->update([
                        'total_orders' => $customer->total_orders + 1,
                        'total_spent' => $customer->total_spent + $grandTotal,
                        'last_order_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    DB::table('customers')->insert([
                        'name' => $validatedData['user_name'],
                        'phone' => $validatedData['user_phone'],
                        'status' => 'Active',
                        'total_orders' => 1,
                        'total_spent' => $grandTotal,
                        'last_order_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                // Fast return response; dispatch store manager FCM notification asynchronously
                try {
                    \App\Services\FcmNotificationService::sendNotification(
                        "🚨 NEW ORDER #{$orderNumber}",
                        "New order received: ₹{$grandTotal} ({$validatedData['user_name']})",
                        'store_managers',
                        null,
                        null,
                        $orderId
                    );
                } catch (\Throwable $th) {}

                return response()->json([
                    'status' => 'success',
                    'message' => 'Order placed successfully and stock reserved.',
                    'order_number' => $orderNumber,
                    'order_id' => $orderId,
                    'grand_total' => $grandTotal,
                    'order_type' => $orderType,
                    'order' => [
                        'id' => $orderId,
                        'order_number' => $orderNumber,
                        'user_name' => $validatedData['user_name'],
                        'user_phone' => $validatedData['user_phone'],
                        'delivery_address' => $orderType === 'pickup' ? 'Self Pickup at Store' : $validatedData['delivery_address'],
                        'subtotal' => $checkoutResult['subtotal'],
                        'delivery_fee' => $deliveryFee,
                        'grand_total' => $grandTotal,
                        'payment_method' => $request->input('payment_method', 'Cash on Delivery'),
                        'order_type' => $orderType,
                        'status' => 'Pending',
                        'store_name' => $store->name ?? 'Sonarbangla Mart (Krishnanagar Store)',
                        'store_address' => $store->address ?? 'Holding 42, Main Road, Krishnanagar, Nadia - 741101',
                        'store_phone' => $store->phone ?? '8016222991',
                        'store_lat' => (float)($store->latitude ?? 23.4013),
                        'store_lng' => (float)($store->longitude ?? 88.5010),
                        'pickup_details' => [
                            'store_name' => $store->name ?? 'Sonarbangla Mart (Krishnanagar Store)',
                            'store_address' => $store->address ?? 'Holding 42, Main Road, Krishnanagar, Nadia - 741101',
                            'store_phone' => $store->phone ?? '8016222991',
                            'store_lat' => (float)($store->latitude ?? 23.4013),
                            'store_lng' => (float)($store->longitude ?? 88.5010),
                        ],
                        'items' => array_map(function($it) {
                            $p = isset($it['product_id']) ? DB::table('products')->where('id', $it['product_id'])->first() : null;
                            $it['product_name'] = $it['name'] ?? $p->name ?? 'Item';
                            $it['product_image'] = $it['product_image'] ?? $it['image'] ?? $p->image ?? '';
                            return $it;
                        }, $checkoutResult['items']),
                        'created_at' => now()->toDateTimeString(),
                    ],
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
        $userPhone = $request->query('phone');
        if (empty($userPhone)) {
            return response()->json([
                'status' => 'success',
                'data' => [],
            ])->header('Access-Control-Allow-Origin', '*')
              ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
              ->header('Access-Control-Allow-Headers', '*');
        }

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

            $userPhone = !empty($validated['user_phone']) ? $validated['user_phone'] : $validated['receiver_phone'];

            $id = DB::table('user_addresses')->insertGetId([
                'user_phone' => $userPhone,
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

    // Get Real-Time User Orders & Live Lifecycle Tracking for Mobile App
    public function getUserOrders(Request $request)
    {
        $rawPhone = $request->query('phone');
        if (empty($rawPhone)) {
            return response()->json([
                'status' => 'success',
                'count' => 0,
                'data' => []
            ])->header('Access-Control-Allow-Origin', '*');
        }

        $phone = trim($rawPhone);
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        $query = DB::table('orders');
        if (!empty($cleanPhone)) {
            $query->where(function($q) use ($cleanPhone, $phone) {
                $q->where('user_phone', 'LIKE', "%$cleanPhone%")
                  ->orWhere('receiver_phone', 'LIKE', "%$cleanPhone%");
                if (!empty($phone)) {
                    $q->orWhere('user_phone', $phone)
                      ->orWhere('receiver_phone', $phone);
                }
            });
        }

        $orders = $query->orderBy('id', 'desc')->get();

        foreach ($orders as $ord) {
            $ord->items = DB::table('order_items')
                ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
                ->select(
                    'order_items.*',
                    DB::raw('COALESCE(order_items.product_name, products.name, "Item") as product_name'),
                    DB::raw('COALESCE(products.image, "") as product_image'),
                    'products.unit'
                )
                ->where('order_items.order_id', $ord->id)
                ->get();

            // Populate store details & lat/lng
            $store = DB::table('stores')->where('id', $ord->store_id)->first();
            if (!$store) {
                $store = DB::table('stores')->first();
            }

            $ord->store_name = $store->name ?? 'Sonarbangla Mart (Krishnanagar Store)';
            $ord->store_address = $store->address ?? 'Holding 42, Main Road, Krishnanagar, Nadia - 741101';
            $ord->store_phone = $store->phone ?? '8016222991';
            $ord->store_lat = (float)($store->latitude ?? 23.4013);
            $ord->store_lng = (float)($store->longitude ?? 88.5010);

            $ord->status_history = DB::table('order_status_histories')
                ->where('order_id', $ord->id)
                ->orderBy('created_at', 'asc')
                ->get();

            $pickupRow = \Illuminate\Support\Facades\Schema::hasTable('store_pickup_orders')
                ? DB::table('store_pickup_orders')->where('order_id', $ord->id)->first()
                : null;

            $ord->pickup_details = [
                'store_name' => $ord->store_name,
                'store_address' => $ord->store_address,
                'store_phone' => $ord->store_phone,
                'store_lat' => $ord->store_lat,
                'store_lng' => $ord->store_lng,
                'pickup_date' => $pickupRow->pickup_date ?? $ord->pickup_date ?? date('Y-m-d'),
                'pickup_slot_time' => $pickupRow->pickup_slot_time ?? $ord->pickup_time ?? '10:00 AM - 11:00 AM',
                'pickup_status' => $pickupRow->pickup_status ?? 'Scheduled',
            ];

            $ord->delivery_details = $ord->order_type === 'pickup'
                ? null
                : DB::table('deliveries')
                    ->leftJoin('delivery_partners', 'deliveries.delivery_partner_id', '=', 'delivery_partners.id')
                    ->select('deliveries.*', 'delivery_partners.name as rider_name', 'delivery_partners.phone as rider_phone')
                    ->where('deliveries.order_id', $ord->id)
                    ->first();
        }

        return response()->json([
            'status' => 'success',
            'data' => $orders,
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
          ->header('Access-Control-Allow-Headers', '*');
    }

    // Get Active Public Coupons List (Private coupons hidden from list, valid via typing code)
    public function getCoupons(Request $request)
    {
        // Safe check for visibility column
        $query = DB::table('coupons')->where('is_active', true);
        if (\Illuminate\Support\Facades\Schema::hasColumn('coupons', 'visibility')) {
            $query->where('visibility', 'public');
        }

        $coupons = $query->where(function($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $coupons,
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
          ->header('Access-Control-Allow-Headers', '*');
    }

    // High-Security Coupon Validation & Rule Engine Endpoint
    public function validateCoupon(Request $request)
    {
        $code = strtoupper(trim($request->input('code', '')));
        $subtotal = (float)$request->input('subtotal', 0.0);
        $phone = $request->input('user_phone', '8016222991');
        $deviceId = $request->input('device_id', '');
        $orderType = $request->input('order_type', 'delivery');
        $storeId = $request->input('store_id', 1);

        $coupon = DB::table('coupons')->where('code', $code)->first();

        if (!$coupon) {
            return response()->json(['status' => 'error', 'message' => 'Invalid coupon code. Please check and try again.']);
        }

        if (!$coupon->is_active) {
            return response()->json(['status' => 'error', 'message' => 'This coupon has expired or is inactive.']);
        }

        // Rule 1: Validity Window
        if ($coupon->start_date && now()->lt(\Carbon\Carbon::parse($coupon->start_date))) {
            return response()->json(['status' => 'error', 'message' => 'This coupon offer has not started yet.']);
        }
        if ($coupon->end_date && now()->gt(\Carbon\Carbon::parse($coupon->end_date))) {
            return response()->json(['status' => 'error', 'message' => 'This coupon offer has expired.']);
        }

        // Rule 2: Minimum Cart Subtotal
        if ($subtotal < (float)$coupon->min_cart_amount) {
            $diff = (float)$coupon->min_cart_amount - $subtotal;
            return response()->json([
                'status' => 'error',
                'message' => "Add items worth ₹" . number_format($diff, 0) . " more to apply coupon $code.",
                'required_min' => (float)$coupon->min_cart_amount
            ]);
        }

        // Rule 3: Fulfillment Type (Pickup vs Delivery)
        if ($coupon->allowed_order_type !== 'all' && $coupon->allowed_order_type !== $orderType) {
            $modeText = $coupon->allowed_order_type === 'pickup' ? 'Store Pickup' : 'Home Delivery';
            return response()->json(['status' => 'error', 'message' => "Coupon $code is valid only for $modeText orders."]);
        }

        // Rule 4: Store Specific
        if ($coupon->allowed_store_id && (int)$coupon->allowed_store_id !== (int)$storeId) {
            return response()->json(['status' => 'error', 'message' => "Coupon $code is not applicable for your selected darkstore."]);
        }

        // Rule 5: User Phone Restrictions
        if (!empty($coupon->allowed_user_phones)) {
            $allowedPhones = array_map('trim', explode(',', $coupon->allowed_user_phones));
            if (!in_array($phone, $allowedPhones)) {
                return response()->json(['status' => 'error', 'message' => "Coupon $code is exclusive to selected accounts."]);
            }
        }

        // Rule 6: Device Fingerprint Lock (1 Device 1 Time Apply)
        if ($coupon->restrict_one_device && !empty($deviceId)) {
            $deviceUsed = DB::table('coupon_redemptions')
                ->where('coupon_id', $coupon->id)
                ->where('device_id', $deviceId)
                ->exists();
            if ($deviceUsed) {
                return response()->json(['status' => 'error', 'message' => "Coupon $code has already been redeemed on this device."]);
            }
        }

        // Rule 7: Per-User Redemption Limit
        $userRedemptionsCount = DB::table('coupon_redemptions')
            ->where('coupon_id', $coupon->id)
            ->where('user_phone', $phone)
            ->count();
        if ($userRedemptionsCount >= (int)$coupon->max_uses_per_user) {
            return response()->json(['status' => 'error', 'message' => "You have reached the maximum limit for coupon $code."]);
        }

        // Rule 8: Global Redemption Budget Limit
        if ($coupon->max_global_uses && (int)$coupon->total_uses_count >= (int)$coupon->max_global_uses) {
            return response()->json(['status' => 'error', 'message' => "Coupon $code offer limit reached."]);
        }

        // Rule 9: First Order Only
        if ($coupon->is_first_order_only) {
            $hasPreviousOrders = DB::table('orders')->where('user_phone', $phone)->exists();
            if ($hasPreviousOrders) {
                return response()->json(['status' => 'error', 'message' => "Coupon $code is valid for new customers on first order only."]);
            }
        }

        // Calculate Discount Amount
        $discount = 0.0;
        if ($coupon->discount_type === 'flat') {
            $discount = (float)$coupon->discount_value;
        } elseif ($coupon->discount_type === 'percentage') {
            $discount = ($subtotal * (float)$coupon->discount_value) / 100.0;
            if ($coupon->max_discount_amount && $discount > (float)$coupon->max_discount_amount) {
                $discount = (float)$coupon->max_discount_amount;
            }
        } elseif ($coupon->discount_type === 'free_delivery') {
            $discount = 25.0; // Waives standard delivery fee
        }

        if ($discount > $subtotal) {
            $discount = $subtotal;
        }

        return response()->json([
            'status' => 'success',
            'message' => "Coupon $code applied successfully!",
            'data' => [
                'code' => $coupon->code,
                'title' => $coupon->title,
                'discount_type' => $coupon->discount_type,
                'discount_amount' => round($discount, 2),
                'is_free_delivery' => (bool)$coupon->is_free_delivery,
            ]
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
          ->header('Access-Control-Allow-Headers', '*');
    }

    // Get Real-Time Customer Wallet Balance
    public function getUserWallet(Request $request)
    {
        $rawPhone = trim($request->query('phone', $request->input('phone', '')));

        if (!\Illuminate\Support\Facades\Schema::hasColumn('customers', 'wallet_balance')) {
            try {
                \Illuminate\Support\Facades\Schema::table('customers', function ($table) {
                    $table->decimal('wallet_balance', 10, 2)->default(0.00)->after('total_spent');
                });
            } catch (\Exception $e) {}
        }

        if (empty($rawPhone)) {
            return response()->json([
                'status' => 'success',
                'wallet_balance' => 0.00,
                'currency' => '₹',
            ])->header('Access-Control-Allow-Origin', '*')
              ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
              ->header('Access-Control-Allow-Headers', '*');
        }

        $phoneDigits = preg_replace('/[^0-9]/', '', $rawPhone);
        $phoneWithPlus = strlen($phoneDigits) === 10 ? '+91' . $phoneDigits : '+' . $phoneDigits;

        $customer = DB::table('customers')
            ->where('phone', $rawPhone)
            ->orWhere('phone', $phoneWithPlus)
            ->orWhere('phone', $phoneDigits)
            ->first();

        if (!$customer) {
            $user = DB::table('users')
                ->where('phone', $rawPhone)
                ->orWhere('phone', $phoneWithPlus)
                ->orWhere('phone', $phoneDigits)
                ->first();

            if ($user) {
                DB::table('customers')->insert([
                    'name' => $user->name ?? 'Customer',
                    'phone' => $user->phone,
                    'status' => 'Active',
                    'total_orders' => 0,
                    'total_spent' => 0.00,
                    'wallet_balance' => 0.00,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $balance = 0.00;
            } else {
                $balance = 0.00;
            }
        } else {
            $balance = (float)($customer->wallet_balance ?? 0.00);
        }

        $transactions = WalletService::getTransactions($rawPhone);

        return response()->json([
            'status' => 'success',
            'wallet_balance' => round($balance, 2),
            'currency' => '₹',
            'transactions' => $transactions,
        ])->header('Access-Control-Allow-Origin', '*')
          ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, OPTIONS')
          ->header('Access-Control-Allow-Headers', '*');
    }

    // Register User with Phone, Password, IP and Device Data
    public function register(Request $request)
    {
        $name = trim($request->input('name', ''));
        $rawPhone = trim($request->input('phone', ''));
        $password = trim($request->input('password', ''));
        $deviceInfo = $request->input('device_info') ?? $request->header('User-Agent') ?? 'Mobile App';
        $ipAddress = $request->ip() ?? '127.0.0.1';

        if (empty($name) || empty($rawPhone) || empty($password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Name, phone number, and password are required.'
            ], 400);
        }

        // Format phone number with +91 if necessary
        $phoneDigits = preg_replace('/[^0-9]/', '', $rawPhone);
        if (strlen($phoneDigits) === 10) {
            $phone = '+91' . $phoneDigits;
        } elseif (strlen($phoneDigits) === 12 && str_starts_with($phoneDigits, '91')) {
            $phone = '+' . $phoneDigits;
        } else {
            $phone = str_starts_with($rawPhone, '+') ? $rawPhone : '+' . $rawPhone;
        }

        // Check if phone already registered
        $existingUser = DB::table('users')->where('phone', $phone)->first();
        if ($existingUser) {
            return response()->json([
                'status' => 'error',
                'message' => 'Mobile number already registered. Please login instead.'
            ], 422);
        }

        $email = $phoneDigits . '@sbmart.com';
        $hashedPassword = \Illuminate\Support\Facades\Hash::make($password);

        $userId = DB::table('users')->insertGetId([
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'password' => $hashedPassword,
            'device_info' => $deviceInfo,
            'ip_address' => $ipAddress,
            'role' => 'customer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Auto-seed initial device FCM token record
        DB::table('fcm_tokens')->updateOrInsert(
            ['user_phone' => $phone],
            [
                'fcm_token' => 'fcm_' . md5($phone . $deviceInfo),
                'device_type' => 'android',
                'updated_at' => now(),
            ]
        );

        // Auto-create Customer profile so user displays in Admin Panel immediately
        DB::table('customers')->updateOrInsert(
            ['phone' => $phone],
            [
                'name' => $name,
                'email' => $email,
                'status' => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $token = 'sb_token_' . md5($userId . time() . rand(1000, 9999));


        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful!',
            'token' => $token,
            'user' => [
                'id' => $userId,
                'name' => $name,
                'phone' => $phone,
                'email' => $email,
                'device_info' => $deviceInfo,
                'ip_address' => $ipAddress,
            ]
        ])->header('Access-Control-Allow-Origin', '*');
    }

    // Login User with Phone and Password
    public function login(Request $request)
    {
        $rawPhone = trim($request->input('phone', ''));
        $password = trim($request->input('password', ''));
        $deviceInfo = $request->input('device_info') ?? $request->header('User-Agent') ?? 'Mobile App';
        $ipAddress = $request->ip() ?? '127.0.0.1';

        if (empty($rawPhone) || empty($password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Phone number and password are required.'
            ], 400);
        }

        // Format phone number with +91
        $phoneDigits = preg_replace('/[^0-9]/', '', $rawPhone);
        if (strlen($phoneDigits) === 10) {
            $phone = '+91' . $phoneDigits;
        } elseif (strlen($phoneDigits) === 12 && str_starts_with($phoneDigits, '91')) {
            $phone = '+' . $phoneDigits;
        } else {
            $phone = str_starts_with($rawPhone, '+') ? $rawPhone : '+' . $rawPhone;
        }

        $user = DB::table('users')->where('phone', $phone)->first();

        if (!$user) {
            // Check fallback for 10 digit without prefix or email
            $user = DB::table('users')->where('phone', $phoneDigits)->orWhere('email', $rawPhone)->first();
        }

        if (!$user || !\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid mobile number or password.'
            ], 401);
        }

        // Reject login if account_status is 'deleted'
        if (isset($user->account_status) && strtolower($user->account_status) === 'deleted') {
            return response()->json([
                'status' => 'error',
                'message' => 'This account has been deleted. Please create a new account to continue.'
            ], 403)->header('Access-Control-Allow-Origin', '*');
        }

        // Update IP & Device Info on login
        DB::table('users')->where('id', $user->id)->update([
            'device_info' => $deviceInfo,
            'ip_address' => $ipAddress,
            'updated_at' => now(),
        ]);

        $token = 'sb_token_' . md5($user->id . time() . rand(1000, 9999));

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone ?? $phone,
                'email' => $user->email,
                'device_info' => $deviceInfo,
                'ip_address' => $ipAddress,
            ]
        ])->header('Access-Control-Allow-Origin', '*');
    }

    // Register/Update User Device FCM Push Token
    public function registerFcmToken(Request $request)
    {
        $rawPhone = trim($request->input('phone', '8016222991'));
        $fcmToken = trim($request->input('fcm_token', ''));
        $deviceType = $request->input('device_type', 'android');

        if (empty($fcmToken)) {
            return response()->json(['status' => 'error', 'message' => 'FCM Token is required.'], 400);
        }

        $phoneDigits = preg_replace('/[^0-9]/', '', $rawPhone);
        $phone = (strlen($phoneDigits) === 10) ? '+91' . $phoneDigits : '+' . $phoneDigits;

        // Clean previous tokens for this user so ONLY ONE latest device receives notifications
        DB::table('fcm_tokens')->where('user_phone', $phone)->orWhere('user_phone', $rawPhone)->orWhere('user_phone', $phoneDigits)->delete();

        // Also delete if this specific token was registered under a different user
        DB::table('fcm_tokens')->where('fcm_token', $fcmToken)->delete();

        // Insert fresh single active token for this user
        DB::table('fcm_tokens')->insert([
            'user_phone' => $phone,
            'fcm_token' => $fcmToken,
            'device_type' => $deviceType,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'FCM token registered successfully!'
        ])->header('Access-Control-Allow-Origin', '*');
    }

    // Get Active Promotional Sliders for App Home Screen
    public function getSliders()
    {
        try {
            $sliders = DB::table('sliders')
                ->where('is_active', true)
                ->orderBy('display_order', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $sliders
            ])->header('Access-Control-Allow-Origin', '*');
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'success',
                'data' => []
            ])->header('Access-Control-Allow-Origin', '*');
        }
    }

    // --- STORE MANAGER APP API ENDPOINTS ---

    // 1. Store Manager Mobile Login
    public function managerLogin(Request $request)
    {
        $loginInput = trim($request->input('login', $request->input('phone', $request->input('email', ''))));
        $password = $request->input('password', '');

        if (empty($loginInput) || empty($password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please enter login phone/email and password.'
            ], 400)->header('Access-Control-Allow-Origin', '*');
        }

        // Find user by phone or email with role store_manager or admin
        $user = DB::table('users')
            ->where(function ($query) use ($loginInput) {
                $query->where('email', $loginInput)
                      ->orWhere('phone', $loginInput)
                      ->orWhere('phone', '+91' . preg_replace('/[^0-9]/', '', $loginInput));
            })
            ->whereIn('role', ['store_manager', 'admin'])
            ->first();

        if (!$user || !\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Store Manager credentials.'
            ], 401)->header('Access-Control-Allow-Origin', '*');
        }

        $storeId = (int)($user->store_id ?? 1);
        $store = DB::table('stores')->where('id', $storeId)->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Store Manager login successful!',
            'manager' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? $loginInput,
                'role' => $user->role,
                'store_id' => $storeId,
                'store_name' => $store ? $store->name : 'Store #' . $storeId,
                'store_address' => $store ? ($store->address . ', ' . $store->city) : '',
            ]
        ])->header('Access-Control-Allow-Origin', '*');
    }

    // 2. Fetch Store Orders for Store Manager App (Latest First)
    public function getManagerOrders(Request $request)
    {
        try {
            $storeId = (int)$request->input('store_id', $request->query('store_id', 1));
            $status = $request->query('status');

            $query = DB::table('orders');

            if ($storeId > 0) {
                $query->where(function ($q) use ($storeId) {
                    $q->where('store_id', $storeId)
                      ->orWhereNull('store_id');
                });
            }

            if (!empty($status) && strtolower($status) !== 'all') {
                $query->where('status', $status);
            }

            $orders = $query->orderBy('id', 'desc')->get();

            $formattedOrders = [];
            foreach ($orders as $o) {
                $itemsData = [];
                if (!empty($o->items)) {
                    $decoded = json_decode($o->items, true);
                    if (is_array($decoded)) {
                        $itemsData = $decoded;
                    }
                }

                if (empty($itemsData)) {
                    $orderItems = DB::table('order_items')->where('order_id', $o->id)->get();
                    foreach ($orderItems as $it) {
                        $price = property_exists($it, 'price') ? $it->price : (property_exists($it, 'unit_price') ? $it->unit_price : 0);
                        $total = property_exists($it, 'subtotal') ? $it->subtotal : (property_exists($it, 'total_price') ? $it->total_price : 0);
                        $name = property_exists($it, 'product_name') ? $it->product_name : (property_exists($it, 'name') ? $it->name : ('Product #' . $it->product_id));

                        $itemsData[] = [
                            'product_id' => $it->product_id,
                            'name' => $name,
                            'price' => (float)$price,
                            'quantity' => (int)($it->quantity ?? 1),
                            'total' => (float)$total,
                        ];
                    }
                }

                // Fetch delivery partner info if assigned
                $delivery = DB::table('deliveries')
                    ->leftJoin('delivery_partners', 'deliveries.delivery_partner_id', '=', 'delivery_partners.id')
                    ->select('deliveries.*', 'delivery_partners.name as partner_name', 'delivery_partners.phone as partner_phone')
                    ->where('deliveries.order_id', $o->id)
                    ->first();

                $formattedOrders[] = [
                    'id' => $o->id,
                    'order_number' => $o->order_number ?? ('ORD-' . $o->id),
                    'store_id' => $o->store_id ?? 1,
                    'user_name' => $o->receiver_name ?? $o->user_name ?? 'Customer',
                    'user_phone' => $o->receiver_phone ?? $o->user_phone ?? '',
                    'customer_name' => $o->user_name ?? 'Customer',
                    'customer_phone' => $o->user_phone ?? '',
                    'delivery_address' => $o->delivery_address ?? 'Pickup / Home Delivery',
                    'latitude' => (float)($o->latitude ?? 23.4013),
                    'longitude' => (float)($o->longitude ?? 88.5010),
                    'status' => $o->status ?? 'Pending',
                    'payment_method' => $o->payment_method ?? 'Cash on Delivery',
                    'order_type' => $o->order_type ?? 'delivery',
                    'pickup_date' => $o->pickup_date ?? null,
                    'pickup_time' => $o->pickup_time ?? null,
                    'subtotal' => (float)($o->subtotal ?? $o->grand_total ?? 0),
                    'grand_total' => (float)($o->grand_total ?? 0),
                    'created_at' => $o->created_at,
                    'items' => $itemsData,
                    'delivery_partner' => $delivery ? [
                        'id' => $delivery->delivery_partner_id,
                        'name' => $delivery->partner_name ?? 'Delivery Rider',
                        'phone' => $delivery->partner_phone ?? '',
                        'status' => $delivery->status ?? 'Assigned',
                    ] : null,
                ];
            }

            return response()->json([
                'status' => 'success',
                'count' => count($formattedOrders),
                'orders' => $formattedOrders,
            ])->header('Access-Control-Allow-Origin', '*');
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getFile() . ':' . $e->getLine(),
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    // 3. Fetch Delivery Partners List for Manager App (Safe Query with Fallback)
    public function getManagerRiders(Request $request)
    {
        try {
            $storeId = (int)$request->input('store_id', $request->query('store_id', 1));

            $query = DB::table('delivery_partners');

            $columns = Schema::getColumnListing('delivery_partners');
            $storeCol = in_array('store_id', $columns) ? 'store_id' : (in_array('assigned_store_id', $columns) ? 'assigned_store_id' : null);
            $activeCol = in_array('is_active', $columns) ? 'is_active' : (in_array('status', $columns) ? 'status' : null);

            if ($storeCol) {
                $query->where(function ($q) use ($storeCol, $storeId) {
                    $q->where($storeCol, $storeId)
                      ->orWhereNull($storeCol);
                });
            }

            if ($activeCol) {
                if ($activeCol === 'status') {
                    $query->where('status', 'Active');
                } else {
                    $query->where('is_active', true);
                }
            }

            $riders = $query->get();

            if ($riders->isEmpty()) {
                $riders = DB::table('delivery_partners')->get();
            }

            return response()->json([
                'status' => 'success',
                'data' => $riders,
            ])->header('Access-Control-Allow-Origin', '*');
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'success',
                'data' => [],
                'message' => $e->getMessage()
            ])->header('Access-Control-Allow-Origin', '*');
        }
    }

    // 4. Manager Update Order Status & Assign Delivery Partner
    public function updateManagerOrderStatus(Request $request)
    {
        try {
            $orderId = (int)$request->input('order_id');
            $newStatus = $request->input('status'); // Pending, Packing, Out for Delivery, Delivered, Cancelled
            $riderId = $request->input('delivery_partner_id');

            if (!$orderId || empty($newStatus)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'order_id and status are required.'
                ], 400)->header('Access-Control-Allow-Origin', '*');
            }

            $order = DB::table('orders')->where('id', $orderId)->first();
            if (!$order) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found.'
                ], 404)->header('Access-Control-Allow-Origin', '*');
            }

            DB::table('orders')->where('id', $orderId)->update([
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

            if (!empty($riderId) && Schema::hasTable('deliveries')) {
                try {
                    $existingDelivery = DB::table('deliveries')->where('order_id', $orderId)->first();
                    if ($existingDelivery) {
                        DB::table('deliveries')->where('id', $existingDelivery->id)->update([
                            'delivery_partner_id' => $riderId,
                            'status' => $newStatus === 'Delivered' ? 'Delivered' : 'Assigned',
                            'updated_at' => now(),
                        ]);
                    } else {
                        DB::table('deliveries')->insert([
                            'order_id' => $orderId,
                            'store_id' => $order->store_id ?? 1,
                            'delivery_partner_id' => $riderId,
                            'status' => $newStatus === 'Delivered' ? 'Delivered' : 'Assigned',
                            'assigned_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } catch (\Throwable $th) {}
            }

            $updatedOrder = DB::table('orders')->where('id', $orderId)->first();

            // Trigger FCM Push Notification to Customer App
            try {
                $custPhone = $order->user_phone ?? $order->receiver_phone ?? null;
                $statusTitles = [
                    'Packing' => '📦 Order Being Packed!',
                    'Out for Delivery' => '🛵 Order Out for Delivery!',
                    'Delivered' => '🎉 Order Delivered Successfully!',
                    'Cancelled' => '❌ Order Status Update',
                ];
                $title = $statusTitles[$newStatus] ?? "Order #{$order->order_number} Status Updated";
                $body = "Your order #{$order->order_number} has been updated to {$newStatus}. Tap to view details.";

                \App\Services\FcmNotificationService::sendNotification(
                    $title,
                    $body,
                    $custPhone ? 'specific_user' : 'all',
                    $custPhone,
                    null,
                    $orderId
                );
            } catch (\Throwable $fcmEx) {
                // Prevent FCM dispatch issues from blocking API response
            }

            return response()->json([
                'status' => 'success',
                'message' => "Order #{$orderId} status updated to '{$newStatus}' successfully!",
                'order' => $updatedOrder,
            ])->header('Access-Control-Allow-Origin', '*');
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    // Ensure wishlists table exists in DB
    private function ensureWishlistsTableExists()
    {
        if (!Schema::hasTable('wishlists')) {
            try {
                Schema::create('wishlists', function ($table) {
                    $table->id();
                    $table->string('user_phone');
                    $table->unsignedBigInteger('product_id');
                    $table->timestamps();
                    $table->unique(['user_phone', 'product_id']);
                });
            } catch (\Throwable $e) {}
        }
    }

    // Toggle Product in User Wishlist
    public function toggleWishlist(Request $request)
    {
        $this->ensureWishlistsTableExists();
        $userPhone = trim($request->input('user_phone', $request->input('phone', '8016222991')));
        $productId = $request->input('product_id');

        if (empty($productId)) {
            return response()->json(['status' => 'error', 'message' => 'Product ID is required.'], 400)->header('Access-Control-Allow-Origin', '*');
        }

        $existing = DB::table('wishlists')
            ->where('user_phone', $userPhone)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            DB::table('wishlists')
                ->where('user_phone', $userPhone)
                ->where('product_id', $productId)
                ->delete();

            return response()->json([
                'status' => 'success',
                'action' => 'removed',
                'is_wishlisted' => false,
                'message' => 'Product removed from wishlist.',
            ])->header('Access-Control-Allow-Origin', '*');
        } else {
            DB::table('wishlists')->insert([
                'user_phone' => $userPhone,
                'product_id' => $productId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'action' => 'added',
                'is_wishlisted' => true,
                'message' => 'Product added to wishlist!',
            ])->header('Access-Control-Allow-Origin', '*');
        }
    }

    // Get User Wishlisted Products with Product Details
    public function getUserWishlist(Request $request)
    {
        $this->ensureWishlistsTableExists();
        $userPhone = trim($request->query('user_phone', $request->query('phone', '8016222991')));
        $storeId = (int)$request->query('store_id', 1);

        $wishlistRecords = DB::table('wishlists')
            ->where('user_phone', $userPhone)
            ->orderBy('id', 'desc')
            ->get();

        $productIds = $wishlistRecords->pluck('product_id')->toArray();

        if (empty($productIds)) {
            return response()->json([
                'status' => 'success',
                'data' => [],
                'product_ids' => [],
            ])->header('Access-Control-Allow-Origin', '*');
        }

        $products = DB::table('products')
            ->whereIn('products.id', $productIds)
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
                'store_product_inventories.is_available'
            )
            ->get();

        foreach ($products as $prod) {
            if (!empty($prod->image) && !str_starts_with($prod->image, 'http://') && !str_starts_with($prod->image, 'https://')) {
                $prod->image = asset(ltrim($prod->image, '/'));
            }

            $prod->effective_price = $prod->custom_price ?? $prod->price;
            $prod->effective_mrp = $prod->custom_mrp ?? $prod->mrp;
            $totalStock = $prod->custom_stock ?? $prod->stock;
            $prod->available_stock = max(0, $totalStock);
        }

        return response()->json([
            'status' => 'success',
            'data' => $products,
            'product_ids' => array_map('strval', $productIds),
        ])->header('Access-Control-Allow-Origin', '*');
    }

    // Auto-migrate account_status & deleted_at columns on users table
    private function ensureUserAccountStatusColumnsExist()
    {
        try {
            if (!Schema::hasColumn('users', 'account_status')) {
                Schema::table('users', function ($table) {
                    $table->string('account_status')->default('active')->after('role');
                });
            }
            if (!Schema::hasColumn('users', 'deleted_at')) {
                Schema::table('users', function ($table) {
                    $table->timestamp('deleted_at')->nullable()->after('account_status');
                });
            }
        } catch (\Throwable $e) {}
    }

    // Authenticated API: POST /api/v1/user/delete-account
    public function deleteAccount(Request $request)
    {
        $this->ensureUserAccountStatusColumnsExist();

        $rawPhone = trim($request->input('phone', $request->input('user_phone', '')));
        $password = trim($request->input('password', ''));

        if (empty($password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Password is required to confirm account deletion.'
            ], 400)->header('Access-Control-Allow-Origin', '*');
        }

        // Format phone number to locate user
        $phoneDigits = preg_replace('/[^0-9]/', '', $rawPhone);
        if (strlen($phoneDigits) === 10) {
            $phone = '+91' . $phoneDigits;
        } elseif (strlen($phoneDigits) === 12 && str_starts_with($phoneDigits, '91')) {
            $phone = '+' . $phoneDigits;
        } else {
            $phone = str_starts_with($rawPhone, '+') ? $rawPhone : '+' . $rawPhone;
        }

        $user = DB::table('users')->where('phone', $phone)->first();
        if (!$user && !empty($phoneDigits)) {
            $user = DB::table('users')->where('phone', $phoneDigits)->orWhere('email', $rawPhone)->first();
        }

        if (!$user) {
            // Fallback for default demo phone if not passed
            $user = DB::table('users')->where('phone', '8016222991')->orWhere('phone', '+918016222991')->first();
        }

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User account not found.'
            ], 404)->header('Access-Control-Allow-Origin', '*');
        }

        // 2. Verify entered password against hash
        if (!\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Incorrect password. Account deletion canceled.'
            ], 401)->header('Access-Control-Allow-Origin', '*');
        }

        // 3. Perform transactional soft deletion & session/token invalidation
        DB::beginTransaction();
        try {
            DB::table('users')->where('id', $user->id)->update([
                'account_status' => 'deleted',
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

            // Update customers table status to 'Deleted'
            if ($user->phone) {
                DB::table('customers')->where('phone', $user->phone)->orWhere('user_id', $user->id)->update([
                    'status' => 'Deleted',
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('customers')->where('user_id', $user->id)->update([
                    'status' => 'Deleted',
                    'updated_at' => now(),
                ]);
            }

            // Clear registered FCM token to stop push notifications for deleted user
            if ($user->phone) {
                DB::table('fcm_tokens')->where('user_phone', $user->phone)->orWhere('user_phone', $rawPhone)->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Your account has been deleted successfully.',
            ])->header('Access-Control-Allow-Origin', '*');
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete account: ' . $e->getMessage()
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }

    private function ensureCustomOrderRequestsTableExists()
    {
        if (!Schema::hasTable('custom_order_requests')) {
            Schema::create('custom_order_requests', function ($table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('user_name')->nullable();
                $table->string('user_phone')->nullable()->index();
                $table->string('order_type')->default('Normal Custom Order');
                $table->string('image_path')->nullable();
                $table->decimal('latitude', 10, 7)->nullable();
                $table->decimal('longitude', 10, 7)->nullable();
                $table->text('address')->nullable();
                $table->text('remarks')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
                $table->timestamps();
            });
        } elseif (!Schema::hasColumn('custom_order_requests', 'store_id')) {
            Schema::table('custom_order_requests', function ($table) {
                $table->unsignedBigInteger('store_id')->nullable()->index()->after('id');
            });
        }
    }

    // Submit Custom / Bulk Order Request (Completely separate from normal ordering)
    public function submitCustomOrderRequest(Request $request)
    {
        $this->ensureCustomOrderRequestsTableExists();

        try {
            $userPhone = trim($request->input('user_phone', ''));
            $rawPhone = preg_replace('/[^0-9]/', '', $userPhone);

            // Resolve user from database matching phone or token
            $user = null;
            if (!empty($userPhone)) {
                $user = DB::table('users')->where('phone', $userPhone)->orWhere('phone', 'LIKE', "%$rawPhone%")->first();
            }

            $userId = $user ? $user->id : null;
            $userName = $user ? $user->name : ($request->input('user_name') ?? 'Customer');
            $finalPhone = $user ? $user->phone : ($userPhone ?: null);

            $orderType = $request->input('order_type', 'Normal Custom Order');
            $address = $request->input('address', '');
            $remarks = $request->input('remarks', '');
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');

            // Resolve Store ID (passed explicitly or dynamically calculated via coordinates)
            $storeId = $request->input('store_id');
            if (empty($storeId) && is_numeric($latitude) && is_numeric($longitude)) {
                $lat = (float)$latitude;
                $lng = (float)$longitude;
                $stores = DB::table('stores')->where('is_active', true)->get();
                $minDist = 999999.0;
                $nearestStoreId = null;

                foreach ($stores as $s) {
                    if ($s->latitude === null || $s->longitude === null) continue;
                    $sLat = (float)$s->latitude;
                    $sLng = (float)$s->longitude;
                    $dLat = deg2rad($sLat - $lat);
                    $dLng = deg2rad($sLng - $lng);
                    $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat)) * cos(deg2rad($sLat)) * sin($dLng / 2) * sin($dLng / 2);
                    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                    $dist = 6371.0 * $c;

                    if ($dist < $minDist) {
                        $minDist = $dist;
                        $nearestStoreId = $s->id;
                    }
                }
                $storeId = $nearestStoreId;
            }

            if (empty($storeId)) {
                $firstStore = DB::table('stores')->where('is_active', true)->first();
                $storeId = $firstStore ? $firstStore->id : 1;
            }

            $imagePath = null;
            if ($request->hasFile('image') || $request->hasFile('file') || $request->hasFile('photo')) {
                $file = $request->file('image') ?? $request->file('file') ?? $request->file('photo');
                $ext = $file->getClientOriginalExtension() ?: 'jpg';
                $filename = 'custom_order_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                
                $primaryDir = public_path('uploads/custom_orders');
                if (!file_exists($primaryDir)) {
                    @mkdir($primaryDir, 0777, true);
                }
                $file->move($primaryDir, $filename);
                $imagePath = 'uploads/custom_orders/' . $filename;

                // Sync to secondary public directories if different
                $secondaryDirs = array_diff([
                    base_path('public/uploads/custom_orders'),
                    base_path('public_html/uploads/custom_orders'),
                    base_path('uploads/custom_orders'),
                ], [$primaryDir]);

                foreach ($secondaryDirs as $secDir) {
                    try {
                        if (!file_exists($secDir)) {
                            @mkdir($secDir, 0777, true);
                        }
                        @copy($primaryDir . '/' . $filename, $secDir . '/' . $filename);
                    } catch (\Throwable $e) {}
                }

            } elseif ($request->input('image') && is_string($request->input('image')) && strlen($request->input('image')) > 20) {
                $rawImage = $request->input('image');
                $ext = 'jpg';
                if (preg_match('/^data:image\/(\w+);base64,/', $rawImage, $type)) {
                    $rawImage = substr($rawImage, strpos($rawImage, ',') + 1);
                    $ext = strtolower($type[1]);
                }
                $rawImage = str_replace(' ', '+', $rawImage);
                $decodedData = base64_decode($rawImage);
                if ($decodedData !== false) {
                    $filename = 'custom_order_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                    $targetDirs = array_unique([
                        public_path('uploads/custom_orders'),
                        base_path('public/uploads/custom_orders'),
                        base_path('public_html/uploads/custom_orders'),
                        base_path('uploads/custom_orders'),
                    ]);

                    foreach ($targetDirs as $tDir) {
                        try {
                            if (!file_exists($tDir)) {
                                @mkdir($tDir, 0777, true);
                            }
                            @file_put_contents($tDir . '/' . $filename, $decodedData);
                        } catch (\Throwable $e) {}
                    }
                    $imagePath = 'uploads/custom_orders/' . $filename;
                }
            }

            $id = DB::table('custom_order_requests')->insertGetId([
                'store_id' => $storeId,
                'user_id' => $userId,
                'user_name' => $userName,
                'user_phone' => $finalPhone,
                'order_type' => $orderType,
                'image_path' => $imagePath,
                'latitude' => is_numeric($latitude) ? (float)$latitude : null,
                'longitude' => is_numeric($longitude) ? (float)$longitude : null,
                'address' => $address,
                'remarks' => $remarks,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Your custom order request has been submitted successfully! We will arrange it for you.',
                'data' => [
                    'id' => $id,
                    'order_type' => $orderType,
                    'status' => 'pending',
                    'image_path' => $imagePath,
                    'created_at' => now()->toDateTimeString(),
                ]
            ])->header('Access-Control-Allow-Origin', '*');

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit custom order request: ' . $e->getMessage()
            ], 500)->header('Access-Control-Allow-Origin', '*');
        }
    }
}


