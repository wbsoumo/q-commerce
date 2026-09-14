<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Record stock movement and update stock values atomically.
     */
    public static function updateStock(
        int $productId,
        ?int $storeId,
        int $quantity,
        string $transactionType,
        ?string $reason = null,
        ?string $referenceType = null,
        ?string $referenceId = null,
        ?int $userId = null,
        ?int $variantId = null
    ): bool {
        return DB::transaction(function () use ($productId, $storeId, $quantity, $transactionType, $reason, $referenceType, $referenceId, $userId, $variantId) {
            $prevStock = 0;
            $newStock = 0;

            if ($variantId) {
                // Variant Stock Adjustment
                $variant = DB::table('product_variants')->where('id', $variantId)->lockForUpdate()->first();
                if (!$variant) return false;

                $prevStock = $variant->stock;
                $newStock = max(0, $prevStock + $quantity);

                DB::table('product_variants')->where('id', $variantId)->update([
                    'stock' => $newStock,
                    'updated_at' => now(),
                ]);
            } elseif ($storeId) {
                // Store Product Inventory Override
                $inv = DB::table('store_product_inventories')
                    ->where('store_id', $storeId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($inv) {
                    $prevStock = $inv->custom_stock;
                    $newStock = max(0, $prevStock + $quantity);

                    DB::table('store_product_inventories')
                        ->where('id', $inv->id)
                        ->update([
                            'custom_stock' => $newStock,
                            'updated_at' => now(),
                        ]);
                } else {
                    // Fallback to base product if store inventory record doesn't exist
                    $prod = DB::table('products')->where('id', $productId)->lockForUpdate()->first();
                    if (!$prod) return false;
                    $prevStock = $prod->stock;
                    $newStock = max(0, $prevStock + $quantity);
                    DB::table('products')->where('id', $productId)->update([
                        'stock' => $newStock,
                        'updated_at' => now(),
                    ]);
                }
            } else {
                // Base Product Stock
                $prod = DB::table('products')->where('id', $productId)->lockForUpdate()->first();
                if (!$prod) return false;

                $prevStock = $prod->stock;
                $newStock = max(0, $prevStock + $quantity);

                DB::table('products')->where('id', $productId)->update([
                    'stock' => $newStock,
                    'updated_at' => now(),
                ]);
            }

            // Record Inventory Transaction History
            DB::table('inventory_transactions')->insert([
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'store_id' => $storeId,
                'quantity' => $quantity,
                'previous_stock' => $prevStock,
                'new_stock' => $newStock,
                'transaction_type' => $transactionType,
                'reason' => $reason,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Check Low / Critical Stock Alerts
            self::checkInventoryAlerts($productId, $storeId, $newStock, $variantId);

            return true;
        });
    }

    /**
     * Reserve Stock during order creation
     */
    public static function reserveStock(int $productId, ?int $storeId, int $quantity, string $orderNumber, ?int $variantId = null): bool
    {
        return DB::transaction(function () use ($productId, $storeId, $quantity, $orderNumber, $variantId) {
            if ($storeId) {
                $inv = DB::table('store_product_inventories')
                    ->where('store_id', $storeId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($inv) {
                    $available = $inv->custom_stock - $inv->custom_reserved_stock;
                    if ($available < $quantity) return false;

                    DB::table('store_product_inventories')->where('id', $inv->id)->update([
                        'custom_reserved_stock' => $inv->custom_reserved_stock + $quantity,
                        'updated_at' => now(),
                    ]);

                    self::recordTransaction($productId, $variantId, $storeId, -$quantity, $inv->custom_stock, $inv->custom_stock, 'ORDER_RESERVED', 'Stock reserved for order', 'Order', $orderNumber);
                    return true;
                }
            }

            $prod = DB::table('products')->where('id', $productId)->lockForUpdate()->first();
            if (!$prod) return false;

            $available = $prod->stock - $prod->reserved_stock;
            if ($available < $quantity) return false;

            DB::table('products')->where('id', $productId)->update([
                'reserved_stock' => $prod->reserved_stock + $quantity,
                'updated_at' => now(),
            ]);

            self::recordTransaction($productId, $variantId, $storeId, -$quantity, $prod->stock, $prod->stock, 'ORDER_RESERVED', 'Stock reserved for order', 'Order', $orderNumber);
            return true;
        });
    }

    /**
     * Release Stock Reservation (On Order Cancellation)
     */
    public static function releaseReservation(int $productId, ?int $storeId, int $quantity, string $orderNumber, ?int $variantId = null): void
    {
        DB::transaction(function () use ($productId, $storeId, $quantity, $orderNumber, $variantId) {
            if ($storeId) {
                $inv = DB::table('store_product_inventories')
                    ->where('store_id', $storeId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($inv) {
                    $newRes = max(0, $inv->custom_reserved_stock - $quantity);
                    DB::table('store_product_inventories')->where('id', $inv->id)->update([
                        'custom_reserved_stock' => $newRes,
                        'updated_at' => now(),
                    ]);
                    self::recordTransaction($productId, $variantId, $storeId, $quantity, $inv->custom_stock, $inv->custom_stock, 'ORDER_CANCELLED', 'Reservation released on order cancellation', 'Order', $orderNumber);
                    return;
                }
            }

            $prod = DB::table('products')->where('id', $productId)->lockForUpdate()->first();
            if ($prod) {
                $newRes = max(0, $prod->reserved_stock - $quantity);
                DB::table('products')->where('id', $prod->id)->update([
                    'reserved_stock' => $newRes,
                    'updated_at' => now(),
                ]);
                self::recordTransaction($productId, $variantId, $storeId, $quantity, $prod->stock, $prod->stock, 'ORDER_CANCELLED', 'Reservation released on order cancellation', 'Order', $orderNumber);
            }
        });
    }

    /**
     * Consume Stock Reservation (On Order Confirmation / Delivery)
     */
    public static function consumeReservation(int $productId, ?int $storeId, int $quantity, string $orderNumber, ?int $variantId = null): void
    {
        DB::transaction(function () use ($productId, $storeId, $quantity, $orderNumber, $variantId) {
            if ($storeId) {
                $inv = DB::table('store_product_inventories')
                    ->where('store_id', $storeId)
                    ->where('product_id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($inv) {
                    $prevStock = $inv->custom_stock;
                    $newStock = max(0, $prevStock - $quantity);
                    $newRes = max(0, $inv->custom_reserved_stock - $quantity);

                    DB::table('store_product_inventories')->where('id', $inv->id)->update([
                        'custom_stock' => $newStock,
                        'custom_reserved_stock' => $newRes,
                        'updated_at' => now(),
                    ]);
                    self::recordTransaction($productId, $variantId, $storeId, -$quantity, $prevStock, $newStock, 'ORDER_CONFIRMED', 'Stock consumed on order completion', 'Order', $orderNumber);
                    return;
                }
            }

            $prod = DB::table('products')->where('id', $productId)->lockForUpdate()->first();
            if ($prod) {
                $prevStock = $prod->stock;
                $newStock = max(0, $prevStock - $quantity);
                $newRes = max(0, $prod->reserved_stock - $quantity);

                DB::table('products')->where('id', $prod->id)->update([
                    'stock' => $newStock,
                    'reserved_stock' => $newRes,
                    'updated_at' => now(),
                ]);
                self::recordTransaction($productId, $variantId, $storeId, -$quantity, $prevStock, $newStock, 'ORDER_CONFIRMED', 'Stock consumed on order completion', 'Order', $orderNumber);
            }
        });
    }

    private static function recordTransaction(
        int $productId,
        ?int $variantId,
        ?int $storeId,
        int $quantity,
        int $prevStock,
        int $newStock,
        string $type,
        string $reason,
        string $refType,
        string $refId
    ): void {
        DB::table('inventory_transactions')->insert([
            'product_id' => $productId,
            'product_variant_id' => $variantId,
            'store_id' => $storeId,
            'quantity' => $quantity,
            'previous_stock' => $prevStock,
            'new_stock' => $newStock,
            'transaction_type' => $type,
            'reason' => $reason,
            'reference_type' => $refType,
            'reference_id' => $refId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private static function checkInventoryAlerts(int $productId, ?int $storeId, int $currentStock, ?int $variantId = null): void
    {
        $lowThreshold = 10;
        $criticalThreshold = 5;

        if ($storeId) {
            $inv = DB::table('store_product_inventories')->where('store_id', $storeId)->where('product_id', $productId)->first();
            if ($inv) {
                $lowThreshold = $inv->custom_low_stock_threshold ?? 10;
                $criticalThreshold = $inv->custom_critical_stock_threshold ?? 5;
            }
        }

        $alertType = null;
        if ($currentStock <= 0) {
            $alertType = 'out_of_stock';
        } elseif ($currentStock <= $criticalThreshold) {
            $alertType = 'critical_stock';
        } elseif ($currentStock <= $lowThreshold) {
            $alertType = 'low_stock';
        }

        if ($alertType) {
            DB::table('inventory_alerts')->insert([
                'store_id' => $storeId,
                'product_id' => $productId,
                'product_variant_id' => $variantId,
                'alert_type' => $alertType,
                'current_stock' => $currentStock,
                'threshold' => ($alertType === 'out_of_stock') ? 0 : (($alertType === 'critical_stock') ? $criticalThreshold : $lowThreshold),
                'is_resolved' => false,
                'notification_channel' => 'dashboard',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
