<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WalletService
{
    public static function ensureSchema()
    {
        try {
            if (!Schema::hasColumn('customers', 'wallet_balance')) {
                Schema::table('customers', function ($table) {
                    $table->decimal('wallet_balance', 10, 2)->default(0.00)->after('total_spent');
                });
            }

            if (!Schema::hasColumn('orders', 'wallet_paid')) {
                Schema::table('orders', function ($table) {
                    $table->decimal('wallet_paid', 10, 2)->default(0.00)->after('delivery_fee');
                    $table->decimal('payable_amount', 10, 2)->default(0.00)->after('wallet_paid');
                    $table->boolean('is_wallet_refunded')->default(false)->after('payable_amount');
                });
            }

            if (!Schema::hasTable('wallet_transactions')) {
                Schema::create('wallet_transactions', function ($table) {
                    $table->id();
                    $table->string('user_phone');
                    $table->string('type'); // 'credit' or 'debit'
                    $table->decimal('amount', 10, 2);
                    $table->string('description')->nullable();
                    $table->string('order_number')->nullable();
                    $table->decimal('running_balance', 10, 2)->default(0.00);
                    $table->timestamps();
                });
            }
        } catch (\Throwable $e) {}
    }

    public static function getCustomerWalletBalance(string $phone): float
    {
        self::ensureSchema();
        $rawPhone = trim($phone);
        $phoneDigits = preg_replace('/[^0-9]/', '', $rawPhone);
        $phoneWithPlus = strlen($phoneDigits) === 10 ? '+91' . $phoneDigits : '+' . $phoneDigits;

        $customer = DB::table('customers')
            ->where('phone', $rawPhone)
            ->orWhere('phone', $phoneWithPlus)
            ->orWhere('phone', $phoneDigits)
            ->first();

        return $customer ? (float)($customer->wallet_balance ?? 0.00) : 0.00;
    }

    public static function deductWalletForOrder(string $phone, string $orderNumber, float $amountToDeduct): float
    {
        self::ensureSchema();
        if ($amountToDeduct <= 0) return 0.00;

        $rawPhone = trim($phone);
        $phoneDigits = preg_replace('/[^0-9]/', '', $rawPhone);
        $phoneWithPlus = strlen($phoneDigits) === 10 ? '+91' . $phoneDigits : '+' . $phoneDigits;

        $customer = DB::table('customers')
            ->where('phone', $rawPhone)
            ->orWhere('phone', $phoneWithPlus)
            ->orWhere('phone', $phoneDigits)
            ->first();

        if (!$customer) return 0.00;

        $currentBalance = (float)($customer->wallet_balance ?? 0.00);
        $actualDeduction = min($currentBalance, $amountToDeduct);

        if ($actualDeduction > 0) {
            $newBalance = max(0.00, $currentBalance - $actualDeduction);

            DB::table('customers')->where('id', $customer->id)->update([
                'wallet_balance' => $newBalance,
                'updated_at' => now(),
            ]);

            DB::table('wallet_transactions')->insert([
                'user_phone' => $customer->phone,
                'type' => 'debit',
                'amount' => $actualDeduction,
                'description' => "Paid for Order #{$orderNumber}",
                'order_number' => $orderNumber,
                'running_balance' => $newBalance,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $actualDeduction;
    }

    public static function refundWalletForOrder($order): bool
    {
        self::ensureSchema();
        if (!$order) return false;

        $walletPaid = (float)($order->wallet_paid ?? 0.00);
        if ($walletPaid <= 0) return false;

        // Prevent double refunds
        if (!empty($order->is_wallet_refunded)) return false;

        $rawPhone = trim($order->user_phone ?? '');
        $phoneDigits = preg_replace('/[^0-9]/', '', $rawPhone);
        $phoneWithPlus = strlen($phoneDigits) === 10 ? '+91' . $phoneDigits : '+' . $phoneDigits;

        $customer = DB::table('customers')
            ->where('phone', $rawPhone)
            ->orWhere('phone', $phoneWithPlus)
            ->orWhere('phone', $phoneDigits)
            ->first();

        if (!$customer) return false;

        $currentBalance = (float)($customer->wallet_balance ?? 0.00);
        $newBalance = $currentBalance + $walletPaid;

        DB::table('customers')->where('id', $customer->id)->update([
            'wallet_balance' => $newBalance,
            'updated_at' => now(),
        ]);

        DB::table('orders')->where('id', $order->id)->update([
            'is_wallet_refunded' => true,
            'updated_at' => now(),
        ]);

        DB::table('wallet_transactions')->insert([
            'user_phone' => $customer->phone,
            'type' => 'credit',
            'amount' => $walletPaid,
            'description' => "Refund for Cancelled Order #{$order->order_number}",
            'order_number' => $order->order_number,
            'running_balance' => $newBalance,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return true;
    }

    public static function getTransactions(string $phone): array
    {
        self::ensureSchema();
        $rawPhone = trim($phone);
        $phoneDigits = preg_replace('/[^0-9]/', '', $rawPhone);
        $phoneWithPlus = strlen($phoneDigits) === 10 ? '+91' . $phoneDigits : '+' . $phoneDigits;

        $txs = DB::table('wallet_transactions')
            ->where('user_phone', $rawPhone)
            ->orWhere('user_phone', $phoneWithPlus)
            ->orWhere('user_phone', $phoneDigits)
            ->orderBy('id', 'desc')
            ->get();

        $result = [];
        foreach ($txs as $t) {
            $result[] = [
                'id' => $t->id,
                'type' => $t->type,
                'amount' => (float)$t->amount,
                'description' => $t->description ?? '',
                'order_number' => $t->order_number ?? '',
                'running_balance' => (float)$t->running_balance,
                'created_at' => \Carbon\Carbon::parse($t->created_at)->format('d M Y, h:i A'),
            ];
        }

        return $result;
    }
}
