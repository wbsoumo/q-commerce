<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Upgrade Stores Table
        Schema::table('stores', function (Blueprint $table) {
            $table->enum('status', ['Active', 'Inactive', 'Temporarily Closed', 'Maintenance'])->default('Active')->after('is_active');
            $table->time('opening_time')->default('06:00:00')->after('status');
            $table->time('closing_time')->default('23:00:00')->after('opening_time');
            $table->json('operating_hours')->nullable()->after('closing_time');
            $table->json('holidays')->nullable()->after('operating_hours');
            $table->string('temporary_closure_reason')->nullable()->after('holidays');
            $table->boolean('vacation_mode')->default(false)->after('temporary_closure_reason');
            $table->decimal('min_order_amount', 10, 2)->default(0.00)->after('vacation_mode');
            $table->decimal('delivery_fee', 10, 2)->default(15.00)->after('min_order_amount');
            $table->decimal('free_delivery_threshold', 10, 2)->default(299.00)->after('delivery_fee');
            $table->integer('estimated_delivery_time_mins')->default(15)->after('free_delivery_threshold');
            $table->integer('prep_time_mins')->default(5)->after('estimated_delivery_time_mins');
            $table->string('store_phone')->nullable()->after('prep_time_mins');
            $table->string('store_email')->nullable()->after('store_phone');
        });

        // 2. Inventory Thresholds on Products & Store Overrides
        Schema::table('products', function (Blueprint $table) {
            $table->integer('reserved_stock')->default(0)->after('stock');
            $table->integer('low_stock_threshold')->default(10)->after('reserved_stock');
            $table->integer('critical_stock_threshold')->default(5)->after('low_stock_threshold');
            $table->boolean('has_variants')->default(false)->after('critical_stock_threshold');
            $table->string('brand')->nullable()->after('has_variants');
        });

        Schema::table('store_product_inventories', function (Blueprint $table) {
            $table->integer('custom_reserved_stock')->default(0)->after('custom_stock');
            $table->integer('custom_low_stock_threshold')->default(10)->after('custom_reserved_stock');
            $table->integer('custom_critical_stock_threshold')->default(5)->after('custom_low_stock_threshold');
        });

        // 3. Product Attributes & Variants Tables
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. Size, Weight, Color, Pack Size
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('variant_name'); // e.g., 500ml, 1L, 5kg
            $table->string('sku')->unique();
            $table->string('barcode')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('mrp', 10, 2);
            $table->integer('stock')->default(100);
            $table->integer('reserved_stock')->default(0);
            $table->string('weight')->nullable();
            $table->string('unit')->default('pcs');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Inventory Transactions / Stock History Table
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('quantity'); // Positive or Negative
            $table->integer('previous_stock');
            $table->integer('new_stock');
            $table->enum('transaction_type', [
                'STOCK_IN',
                'STOCK_OUT',
                'ORDER_RESERVED',
                'ORDER_CONFIRMED',
                'ORDER_CANCELLED',
                'ORDER_REFUNDED',
                'STOCK_ADJUSTMENT',
                'DAMAGED',
                'EXPIRED',
                'STOCK_TRANSFER'
            ]);
            $table->string('reason')->nullable();
            $table->string('reference_type')->nullable(); // Order, Manual Adjustment, etc.
            $table->string('reference_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 5. Order Status History & Extended Order Statuses
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('previous_status')->nullable();
            $table->string('new_status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        // 6. Customers Table
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->enum('status', ['Active', 'Blocked', 'Suspended'])->default('Active');
            $table->boolean('is_vip')->default(false);
            $table->text('notes')->nullable();
            $table->json('tags')->nullable();
            $table->integer('total_orders')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0.00);
            $table->timestamp('last_order_at')->nullable();
            $table->timestamps();
        });

        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('address_type')->default('Home'); // Home, Work, Other
            $table->text('full_address');
            $table->string('city');
            $table->string('pincode');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 7. Delivery Partners & Deliveries Tables
        Schema::create('delivery_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('profile_image')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->boolean('is_online')->default(false);
            $table->decimal('current_lat', 10, 7)->nullable();
            $table->decimal('current_lng', 10, 7)->nullable();
            $table->foreignId('assigned_store_id')->nullable()->constrained('stores')->onDelete('set null');
            $table->date('joining_date')->nullable();
            $table->timestamps();
        });

        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('delivery_partner_id')->nullable()->constrained('delivery_partners')->onDelete('set null');
            $table->enum('delivery_status', [
                'Waiting for Assignment',
                'Assigned',
                'Picked Up',
                'Out for Delivery',
                'Delivered',
                'Failed',
                'Cancelled'
            ])->default('Waiting for Assignment');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 8. Delivery Zones Table
        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->string('zone_name');
            $table->enum('zone_type', ['radius', 'pincode'])->default('radius');
            $table->decimal('radius_km', 5, 2)->default(5.00);
            $table->json('pincodes')->nullable();
            $table->decimal('base_delivery_fee', 10, 2)->default(15.00);
            $table->decimal('min_order_amount', 10, 2)->default(0.00);
            $table->decimal('free_delivery_threshold', 10, 2)->default(299.00);
            $table->decimal('per_km_fee', 10, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 9. Inventory Alerts Table
        Schema::create('inventory_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->enum('alert_type', ['low_stock', 'critical_stock', 'out_of_stock']);
            $table->integer('current_stock');
            $table->integer('threshold');
            $table->boolean('is_resolved')->default(false);
            $table->string('notification_channel')->default('dashboard'); // dashboard, email, push
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_alerts');
        Schema::dropIfExists('delivery_zones');
        Schema::dropIfExists('deliveries');
        Schema::dropIfExists('delivery_partners');
        Schema::dropIfExists('customer_addresses');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attributes');

        Schema::table('store_product_inventories', function (Blueprint $table) {
            $table->dropColumn(['custom_reserved_stock', 'custom_low_stock_threshold', 'custom_critical_stock_threshold']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['reserved_stock', 'low_stock_threshold', 'critical_stock_threshold', 'has_variants', 'brand']);
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn([
                'status', 'opening_time', 'closing_time', 'operating_hours', 'holidays',
                'temporary_closure_reason', 'vacation_mode', 'min_order_amount', 'delivery_fee',
                'free_delivery_threshold', 'estimated_delivery_time_mins', 'prep_time_mins',
                'store_phone', 'store_email'
            ]);
        });
    }
};
