<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class QCommerceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles
        DB::table('roles')->insert([
            ['name' => 'Admin'],
            ['name' => 'Store Manager'],
        ]);

        // 2. Seed Multi-Vendor Stores
        $store1Id = DB::table('stores')->insertGetId([
            'name' => 'Krishnanagar Main Store',
            'code' => 'STR-KRN-01',
            'address' => '11E Krishnanagar Main Road',
            'latitude' => 23.4013,
            'longitude' => 88.5010,
            'city' => 'Krishnanagar',
            'pincode' => '741101',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $store2Id = DB::table('stores')->insertGetId([
            'name' => 'Kolkata Hub Store',
            'code' => 'STR-CCU-02',
            'address' => 'Salt Lake Sector V, Block EP',
            'latitude' => 22.5726,
            'longitude' => 88.4339,
            'city' => 'Kolkata',
            'pincode' => '700091',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Seed Users (Admin & Store Managers)
        DB::table('users')->insert([
            [
                'name' => 'Super Admin',
                'email' => 'admin@blinkit.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'store_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager Krishnanagar',
                'email' => 'manager.krishnanagar@blinkit.com',
                'password' => Hash::make('manager123'),
                'role' => 'store_manager',
                'store_id' => $store1Id,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager Kolkata',
                'email' => 'manager.kolkata@blinkit.com',
                'password' => Hash::make('manager123'),
                'role' => 'store_manager',
                'store_id' => $store2Id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 4. Seed Categories
        $categories = [
            'Ganeshotsav' => 'image 50.png',
            'Electronics' => 'image 52.png',
            'Beauty & Cosmetics' => 'image 35.png',
            'Gifting' => 'image 51.png',
            'Pharmacy' => 'image 41.png',
            'Pet Care' => 'image 42.png',
            'Toys & Games' => 'image 53.png',
            'Vegetables & Fruits' => 'image 41.png',
            'Dairy, Bread & Milk' => 'image 44 (1).png',
        ];

        foreach ($categories as $catName => $img) {
            $catId = DB::table('categories')->insertGetId([
                'name' => $catName,
                'slug' => Str::slug($catName),
                'image' => $img,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Global Product
            $globalProdId = DB::table('products')->insertGetId([
                'category_id' => $catId,
                'name' => 'Global ' . $catName . ' Essential Pack',
                'sku' => 'GLOBAL-' . strtoupper(Str::slug($catName)),
                'unit' => '1 pack',
                'price' => rand(49, 199),
                'mrp' => rand(250, 399),
                'stock' => 100,
                'image' => $img,
                'description' => 'Available in all stores globally.',
                'scope' => 'global',
                'store_id' => null,
                'is_featured' => true,
                'is_bestseller' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Store Specific Product (For Krishnanagar Store)
            DB::table('products')->insert([
                'category_id' => $catId,
                'name' => 'Krishnanagar Special ' . $catName,
                'sku' => 'STR1-' . strtoupper(Str::slug($catName)),
                'unit' => '1 unit',
                'price' => rand(89, 299),
                'mrp' => rand(350, 499),
                'stock' => 35,
                'image' => $img,
                'description' => 'Exclusive item for Krishnanagar store.',
                'scope' => 'store_specific',
                'store_id' => $store1Id,
                'is_featured' => false,
                'is_bestseller' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Custom Store Manager Inventory Override for Global Product
            DB::table('store_product_inventories')->insert([
                'store_id' => $store1Id,
                'product_id' => $globalProdId,
                'custom_price' => 79.00,
                'custom_mrp' => 120.00,
                'custom_stock' => 45,
                'is_available' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
