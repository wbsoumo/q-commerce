<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QCommerceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Stores
        DB::table('stores')->insert([
            [
                'name' => 'Krishnanagar Main Store',
                'address' => '11E Krishnanagar Main Road',
                'latitude' => 23.4013,
                'longitude' => 88.5010,
                'city' => 'Krishnanagar',
                'pincode' => '741101',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kolkata Hub Store',
                'address' => 'Salt Lake Sector V',
                'latitude' => 22.5726,
                'longitude' => 88.4339,
                'city' => 'Kolkata',
                'pincode' => '700091',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // 2. Seed Categories
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

            // Seed sample subcategories
            $subCatId = DB::table('sub_categories')->insertGetId([
                'category_id' => $catId,
                'name' => 'Popular Items',
                'slug' => 'popular-items',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Seed Products
            DB::table('products')->insert([
                [
                    'category_id' => $catId,
                    'sub_category_id' => $subCatId,
                    'name' => 'Premium ' . $catName . ' Special Pack',
                    'unit' => '1 pack',
                    'price' => rand(49, 499),
                    'mrp' => rand(595, 899),
                    'stock' => 50,
                    'image' => $img,
                    'description' => 'Fresh quality ' . $catName . ' delivered in 15 minutes.',
                    'is_featured' => true,
                    'is_bestseller' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }
}
