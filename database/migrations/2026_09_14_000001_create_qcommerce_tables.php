<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Roles Table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Admin, Store Manager
            $table->timestamps();
        });

        // 2. Stores Table
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('address');
            $table->decimal('latitude', 10, 7)->default(23.4013);
            $table->decimal('longitude', 10, 7)->default(88.5010);
            $table->decimal('delivery_radius_km', 5, 2)->default(5.00);
            $table->string('city')->default('Krishnanagar');
            $table->string('pincode')->default('741101');
            $table->string('banner_title')->nullable()->default('Mega Diwali Sale');
            $table->string('banner_subtitle')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. User Store / Role Mapping
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('admin'); // admin, store_manager
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('set null');
        });

        // 4. Categories Table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Products Table (With Global vs Store-Specific Flag)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('sku')->unique();
            $table->string('unit')->default('1 pack');
            $table->decimal('price', 10, 2);
            $table->decimal('mrp', 10, 2);
            $table->integer('stock')->default(100);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->enum('scope', ['global', 'store_specific'])->default('global');
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('cascade'); // Legacy single store
            $table->json('store_ids')->nullable(); // Multiple assigned stores JSON array
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_bestseller')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Store Specific Product Inventory Override (For Store Managers)
        Schema::create('store_product_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('custom_price', 10, 2)->nullable();
            $table->decimal('custom_mrp', 10, 2)->nullable();
            $table->integer('custom_stock')->default(0);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->unique(['store_id', 'product_id']);
        });

        // 7. Orders Table
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('set null');
            $table->string('user_name');
            $table->string('user_phone');
            $table->text('delivery_address');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(15.00);
            $table->decimal('grand_total', 10, 2);
            $table->string('payment_method')->default('PhonePe UPI');
            $table->enum('order_type', ['delivery', 'pickup'])->default('delivery');
            $table->date('pickup_date')->nullable();
            $table->string('pickup_time')->nullable();
            $table->string('receiver_name')->nullable();
            $table->string('receiver_phone')->nullable();
            $table->boolean('is_for_someone_else')->default(false);
            $table->enum('status', ['Pending', 'Confirmed', 'Packing', 'Out for Delivery', 'Ready for Pickup', 'Delivered', 'Cancelled'])->default('Pending');
            $table->timestamps();
        });

        // 8. Order Items Table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->string('product_name');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->decimal('total', 10, 2);
            $table->timestamps();
        });

        // 9. User Saved Addresses Table
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->string('user_phone')->default('8016222991');
            $table->string('address_type')->default('Home'); // Home, Work, Other
            $table->string('custom_type_name')->nullable();
            $table->text('address_details');
            $table->string('receiver_name');
            $table->string('receiver_phone');
            $table->boolean('is_for_someone_else')->default(false);
            $table->decimal('latitude', 10, 7)->default(23.4126);
            $table->decimal('longitude', 10, 7)->default(88.4292);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // 10. Dedicated Store Pickup Orders Table
        Schema::create('store_pickup_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('store_id')->nullable()->constrained()->onDelete('set null');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->date('pickup_date');
            $table->string('pickup_slot_time');
            $table->string('store_opening_time')->default('06:00 AM');
            $table->string('store_closing_time')->default('11:00 PM');
            $table->enum('pickup_status', ['Scheduled', 'Ready for Pickup', 'Picked Up', 'Cancelled'])->default('Scheduled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_pickup_orders');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('store_product_inventories');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn(['role', 'store_id']);
        });
        Schema::dropIfExists('stores');
        Schema::dropIfExists('roles');
    }
};
