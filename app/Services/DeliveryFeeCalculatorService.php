<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DeliveryFeeCalculatorService
{
    /**
     * Calculate zone/radius delivery fee based on store, customer pincode, distance, and subtotal.
     */
    public static function calculateFee(?int $storeId, ?string $pincode, float $distanceKm, float $subtotal): array
    {
        $deliveryFee = 15.00;
        $minOrderAmount = 0.00;
        $freeDeliveryThreshold = 299.00;

        if ($storeId) {
            // Check delivery zones first
            $zone = null;
            if ($pincode) {
                $zone = DB::table('delivery_zones')
                    ->where('store_id', $storeId)
                    ->where('is_active', true)
                    ->where('zone_type', 'pincode')
                    ->whereJsonContains('pincodes', $pincode)
                    ->first();
            }

            if (!$zone) {
                $zone = DB::table('delivery_zones')
                    ->where('store_id', $storeId)
                    ->where('is_active', true)
                    ->where('zone_type', 'radius')
                    ->where('radius_km', '>=', $distanceKm)
                    ->orderBy('radius_km', 'asc')
                    ->first();
            }

            if ($zone) {
                $deliveryFee = $zone->base_delivery_fee + ($zone->per_km_fee * max(0, $distanceKm - 1));
                $minOrderAmount = $zone->min_order_amount;
                $freeDeliveryThreshold = $zone->free_delivery_threshold;
            } else {
                $store = DB::table('stores')->where('id', $storeId)->first();
                if ($store) {
                    $deliveryFee = $store->delivery_fee ?? 15.00;
                    $minOrderAmount = $store->min_order_amount ?? 0.00;
                    $freeDeliveryThreshold = $store->free_delivery_threshold ?? 299.00;
                }
            }
        }

        // Apply free delivery threshold rule
        if ($freeDeliveryThreshold > 0 && $subtotal >= $freeDeliveryThreshold) {
            $deliveryFee = 0.00;
        }

        return [
            'delivery_fee' => round($deliveryFee, 2),
            'min_order_amount' => round($minOrderAmount, 2),
            'free_delivery_threshold' => round($freeDeliveryThreshold, 2),
            'is_free_delivery' => ($deliveryFee == 0.00),
            'is_min_order_satisfied' => ($subtotal >= $minOrderAmount),
        ];
    }
}
