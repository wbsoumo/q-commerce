<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Exception;

class CheckoutValidationService
{
    /**
     * Validate full checkout requirements server-side before processing.
     */
    public static function validateAndCalculateCheckout(array $data): array
    {
        $storeId = $data['store_id'] ?? 1;
        $items = $data['items'] ?? [];
        $userPhone = $data['user_phone'] ?? '';
        $userAddress = $data['delivery_address'] ?? '';
        $pincode = $data['pincode'] ?? '741101';
        $userLat = $data['latitude'] ?? null;
        $userLng = $data['longitude'] ?? null;

        // 1. Validate Store Exists and Is Operational
        $store = DB::table('stores')->where('id', $storeId)->first();
        if (!$store) {
            throw new Exception("Selected store does not exist.");
        }

        $opStatus = StoreOperationalService::checkStoreStatus($store);
        if (!$opStatus['is_operational']) {
            throw new Exception("Store Unavailable: " . $opStatus['reason']);
        }

        // 2. Validate Customer Distance & Delivery Coverage (Bypass radius check for Store Pickup)
        $orderType = $data['order_type'] ?? 'delivery';
        $maxRadius = (float)($store->delivery_radius_km ?? 15.0);

        if ($orderType !== 'pickup' && $userLat && $userLng && !empty($store->latitude) && !empty($store->longitude)) {
            $distanceKm = self::haversineDistance((float)$store->latitude, (float)$store->longitude, (float)$userLat, (float)$userLng);
            if ($distanceKm > $maxRadius) {
                throw new Exception("Delivery location is outside the store's operating radius (" . number_format($maxRadius, 2) . " km). Distance is " . number_format($distanceKm, 2) . " km.");
            }
        }

        // 3. Validate Products, Prices, and Stock Reservation
        $calculatedSubtotal = 0.00;
        $validatedItems = [];

        foreach ($items as $item) {
            $productId = $item['product_id'] ?? null;
            $variantId = $item['variant_id'] ?? null;
            $reqQty = (int)($item['quantity'] ?? 1);

            if ($reqQty <= 0) {
                throw new Exception("Invalid item quantity requested.");
            }

            $price = 0.00;
            $productName = '';

            if ($variantId) {
                $variant = DB::table('product_variants')->where('id', $variantId)->where('is_active', true)->first();
                if (!$variant) {
                    throw new Exception("Requested product variant is currently unavailable.");
                }
                $productName = $variant->variant_name;
                $price = (float)$variant->price;
                $availableStock = $variant->stock - $variant->reserved_stock;
                if ($availableStock < $reqQty) {
                    throw new Exception("Stock unavailable for '{$productName}'. Only {$availableStock} left.");
                }
            } else {
                $product = DB::table('products')->where('id', $productId)->where('is_active', true)->first();
                if (!$product) {
                    throw new Exception("Product is unavailable or disabled.");
                }
                $productName = $product->name;

                // Check store override price & stock
                $storeInv = DB::table('store_product_inventories')
                    ->where('store_id', $storeId)
                    ->where('product_id', $productId)
                    ->first();

                if ($storeInv) {
                    if (!$storeInv->is_available) {
                        throw new Exception("Product '{$productName}' is out of stock in this store.");
                    }
                    $price = (float)($storeInv->custom_price ?? $product->price);
                    $availableStock = $storeInv->custom_stock - ($storeInv->custom_reserved_stock ?? 0);
                } else {
                    $price = (float)$product->price;
                    $availableStock = $product->stock - ($product->reserved_stock ?? 0);
                }

                if ($availableStock < $reqQty) {
                    throw new Exception("Stock unavailable for '{$productName}'. Only {$availableStock} left in stock.");
                }
            }

            $lineTotal = round($price * $reqQty, 2);
            $calculatedSubtotal += $lineTotal;

            $validatedItems[] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'name' => $productName,
                'price' => $price,
                'quantity' => $reqQty,
                'total' => $lineTotal,
            ];
        }

        // 4. Server-Side Delivery Fee & Minimum Order Calculation
        $feeRes = DeliveryFeeCalculatorService::calculateFee($storeId, $pincode, $distanceKm, $calculatedSubtotal);

        if (!$feeRes['is_min_order_satisfied']) {
            throw new Exception("Minimum order amount for this store is ₹{$feeRes['min_order_amount']}. Your cart total is ₹{$calculatedSubtotal}.");
        }

        $deliveryFee = $feeRes['delivery_fee'];
        $grandTotal = round($calculatedSubtotal + $deliveryFee, 2);

        return [
            'store_id' => $storeId,
            'subtotal' => round($calculatedSubtotal, 2),
            'delivery_fee' => $deliveryFee,
            'grand_total' => $grandTotal,
            'items' => $validatedItems,
        ];
    }

    private static function haversineDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 2);
    }
}
