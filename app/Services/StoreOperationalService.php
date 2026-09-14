<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StoreOperationalService
{
    /**
     * Determine if a store is currently operational and open.
     */
    public static function checkStoreStatus($store): array
    {
        if (!$store) {
            return [
                'is_operational' => false,
                'reason' => 'Store not found.'
            ];
        }

        // 1. Check basic status field
        if (isset($store->status) && $store->status !== 'Active') {
            $reason = match ($store->status) {
                'Inactive' => 'Store is currently inactive.',
                'Temporarily Closed' => $store->temporary_closure_reason ?? 'Store is temporarily closed.',
                'Maintenance' => 'Store is under scheduled maintenance.',
                default => 'Store is closed.'
            };
            return [
                'is_operational' => false,
                'reason' => $reason
            ];
        }

        if (isset($store->is_active) && !$store->is_active) {
            return [
                'is_operational' => false,
                'reason' => 'Store is currently inactive.'
            ];
        }

        // 2. Check Vacation Mode
        if (!empty($store->vacation_mode)) {
            return [
                'is_operational' => false,
                'reason' => 'Store is currently on vacation mode.'
            ];
        }

        $now = Carbon::now();

        // 3. Check Holidays
        if (!empty($store->holidays)) {
            $holidays = is_array($store->holidays) ? $store->holidays : json_decode($store->holidays, true);
            $todayDate = $now->toDateString();
            if (is_array($holidays) && in_array($todayDate, $holidays)) {
                return [
                    'is_operational' => false,
                    'reason' => 'Store is closed today for holiday.'
                ];
            }
        }

        // 4. Check Operating Hours / Daily Schedule
        if (!empty($store->opening_time) && !empty($store->closing_time)) {
            $currentTime = $now->format('H:i:s');
            $open = Carbon::createFromTimeString($store->opening_time)->format('H:i:s');
            $close = Carbon::createFromTimeString($store->closing_time)->format('H:i:s');

            if ($currentTime < $open || $currentTime > $close) {
                return [
                    'is_operational' => false,
                    'reason' => "Store is closed. Operating hours are from " . date('h:i A', strtotime($open)) . " to " . date('h:i A', strtotime($close)) . "."
                ];
            }
        }

        return [
            'is_operational' => true,
            'reason' => 'Store is open and operational.'
        ];
    }
}
