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

            $orderType = $request->input('order_type', 'delivery');
            $deliveryFee = $orderType === 'pickup' ? 0.00 : $checkoutResult['delivery_fee'];
            $grandTotal = $checkoutResult['subtotal'] + $deliveryFee;

            return DB::transaction(function() use ($validatedData, $checkoutResult, $request, $orderType, $deliveryFee, $grandTotal) {
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

                // 1b. Insert Store Pickup Record if Pickup Order
                if ($orderType === 'pickup') {
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

                return response()->json([
                    'status' => 'success',
                    'message' => 'Order placed successfully and stock reserved.',
                    'order_number' => $orderNumber,
                    'grand_total' => $grandTotal,
                    'order_type' => $orderType,
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

    // Get Real-Time User Orders & Live Lifecycle Tracking for Mobile App
    public function getUserOrders(Request $request)
    {
        $phone = $request->query('phone', '8016222991');

        $orders = DB::table('orders')
            ->where('user_phone', $phone)
            ->orWhere('receiver_phone', $phone)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($orders as $ord) {
            $ord->items = DB::table('order_items')
                ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
                ->select('order_items.*', 'products.image as product_image', 'products.unit')
                ->where('order_items.order_id', $ord->id)
                ->get();
            $ord->status_history = DB::table('order_status_histories')
                ->where('order_id', $ord->id)
                ->orderBy('created_at', 'asc')
                ->get();
            $ord->pickup_details = $ord->order_type === 'pickup'
                ? DB::table('store_pickup_orders')->where('order_id', $ord->id)->first()
                : null;
            $ord->delivery_details = $ord->order_type === 'delivery'
                ? DB::table('deliveries')
                    ->leftJoin('delivery_partners', 'deliveries.delivery_partner_id', '=', 'delivery_partners.id')
                    ->select('deliveries.*', 'delivery_partners.name as rider_name', 'delivery_partners.phone as rider_phone')
                    ->where('deliveries.order_id', $ord->id)
                    ->first()
                : null;
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
        $phone = $request->query('phone', '8016222991');

        if (!\Illuminate\Support\Facades\Schema::hasColumn('customers', 'wallet_balance')) {
            try {
                \Illuminate\Support\Facades\Schema::table('customers', function ($table) {
                    $table->decimal('wallet_balance', 10, 2)->default(0.00)->after('total_spent');
                });
            } catch (\Exception $e) {}
        }

        $customer = DB::table('customers')->where('phone', $phone)->first();
        $balance = $customer ? (float)$customer->wallet_balance : 150.00;

        return response()->json([
            'status' => 'success',
            'wallet_balance' => round($balance, 2),
            'currency' => '₹',
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

        $existing = DB::table('fcm_tokens')->where('fcm_token', $fcmToken)->first();

        if ($existing) {
            DB::table('fcm_tokens')->where('id', $existing->id)->update([
                'user_phone' => $phone,
                'device_type' => $deviceType,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('fcm_tokens')->insert([
                'user_phone' => $phone,
                'fcm_token' => $fcmToken,
                'device_type' => $deviceType,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'FCM token registered successfully!'
        ])->header('Access-Control-Allow-Origin', '*');
    }
}


