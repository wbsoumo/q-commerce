<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QCommerceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Store
        DB::table('stores')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'Blinkit Krishnanagar Dark Store',
                'code' => 'KNGR-DS01',
                'address' => 'RATANR FLAT, 11E Krishnanagar Main Hub',
                'latitude' => 23.4013,
                'longitude' => 88.5010,
                'delivery_radius_km' => 5.00,
                'city' => 'Krishnanagar',
                'pincode' => '741101',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 2. Seed Categories with web images
        $categories = [
            [
                'name' => 'Vegetables & Fruits',
                'slug' => 'vegetables-fruits',
                'image' => 'http://images.unsplash.com/photo-1610832958506-aa56368176cf?w=500&q=80',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Atta, Dal & Rice',
                'slug' => 'atta-dal-rice',
                'image' => 'http://images.unsplash.com/photo-1586201375761-83865001e31c?w=500&q=80',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Oil, Ghee & Masala',
                'slug' => 'oil-ghee-masala',
                'image' => 'http://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=500&q=80',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Dairy, Bread & Milk',
                'slug' => 'dairy-bread-milk',
                'image' => 'http://images.unsplash.com/photo-1550583724-b2692b85b150?w=500&q=80',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Biscuits & Bakery',
                'slug' => 'biscuits-bakery',
                'image' => 'http://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=500&q=80',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Lights, Diyas & Candles',
                'slug' => 'lights-diyas-candles',
                'image' => 'http://images.unsplash.com/photo-1602874801007-bd458bb1b8b6?w=500&q=80',
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Electronics & Gadgets',
                'slug' => 'electronics-gadgets',
                'image' => 'http://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80',
                'display_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Beauty & Cosmetics',
                'slug' => 'beauty-cosmetics',
                'image' => 'http://images.unsplash.com/photo-1586495777744-4413f21062fa?w=500&q=80',
                'display_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(['slug' => $cat['slug']], array_merge($cat, [
                'updated_at' => now(),
            ]));
        }

        // 3. Seed Products with web image URLs
        $catMap = DB::table('categories')->pluck('id', 'slug');

        $products = [
            [
                'category_id' => $catMap['lights-diyas-candles'] ?? 1,
                'name' => 'Golden Glass Wooden Lid Candle (Oudh)',
                'sku' => 'CNDL-001',
                'unit' => '1 unit',
                'price' => 79.00,
                'mrp' => 120.00,
                'stock' => 150,
                'image' => 'http://images.unsplash.com/photo-1602874801007-bd458bb1b8b6?w=500&q=80',
                'description' => 'Aromatic luxury candle in glass jar with wooden lid.',
                'scope' => 'global',
                'is_featured' => true,
                'is_bestseller' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catMap['biscuits-bakery'] ?? 1,
                'name' => 'Royal Gulab Jamun By Bikano',
                'sku' => 'SWT-001',
                'unit' => '500 g',
                'price' => 149.00,
                'mrp' => 199.00,
                'stock' => 80,
                'image' => 'http://images.unsplash.com/photo-1599785209707-a456fc1337cc?w=500&q=80',
                'description' => 'Soft & delicious royal gulab jamuns.',
                'scope' => 'global',
                'is_featured' => true,
                'is_bestseller' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catMap['vegetables-fruits'] ?? 1,
                'name' => 'Fresh Hybrid Tomatoes',
                'sku' => 'VEG-001',
                'unit' => '1 kg',
                'price' => 49.00,
                'mrp' => 65.00,
                'stock' => 200,
                'image' => 'http://images.unsplash.com/photo-1610832958506-aa56368176cf?w=500&q=80',
                'description' => 'Farm fresh juicy red tomatoes.',
                'scope' => 'global',
                'is_featured' => false,
                'is_bestseller' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catMap['atta-dal-rice'] ?? 1,
                'name' => 'Chakki Fresh Atta 5kg',
                'sku' => 'GRC-001',
                'unit' => '5 kg',
                'price' => 199.00,
                'mrp' => 245.00,
                'stock' => 90,
                'image' => 'http://images.unsplash.com/photo-1586201375761-83865001e31c?w=500&q=80',
                'description' => '100% whole wheat grain chakki fresh atta.',
                'scope' => 'global',
                'is_featured' => false,
                'is_bestseller' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catMap['oil-ghee-masala'] ?? 1,
                'name' => 'Fortune Kachi Ghani Mustard Oil 1L',
                'sku' => 'OIL-001',
                'unit' => '1 L',
                'price' => 165.00,
                'mrp' => 190.00,
                'stock' => 120,
                'image' => 'http://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=500&q=80',
                'description' => 'Pure and pungent mustard oil.',
                'scope' => 'global',
                'is_featured' => false,
                'is_bestseller' => false,
                'is_active' => true,
            ],
            [
                'category_id' => $catMap['dairy-bread-milk'] ?? 1,
                'name' => 'Amul Taaza Toned Milk 1L',
                'sku' => 'DRY-001',
                'unit' => '1 L',
                'price' => 33.00,
                'mrp' => 35.00,
                'stock' => 300,
                'image' => 'http://images.unsplash.com/photo-1550583724-b2692b85b150?w=500&q=80',
                'description' => 'Fresh pasteurized toned milk.',
                'scope' => 'global',
                'is_featured' => false,
                'is_bestseller' => true,
                'is_active' => true,
            ],
            [
                'category_id' => $catMap['electronics-gadgets'] ?? 1,
                'name' => 'Wireless Noise Cancelling Headphones',
                'sku' => 'ELC-001',
                'unit' => '1 unit',
                'price' => 899.00,
                'mrp' => 1499.00,
                'stock' => 45,
                'image' => 'http://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80',
                'description' => 'High bass wireless stereo headphones.',
                'scope' => 'global',
                'is_featured' => true,
                'is_bestseller' => false,
                'is_active' => true,
            ],
            [
                'category_id' => $catMap['beauty-cosmetics'] ?? 1,
                'name' => 'Matte Velvet Lipstick',
                'sku' => 'BTY-001',
                'unit' => '1 pc',
                'price' => 249.00,
                'mrp' => 350.00,
                'stock' => 110,
                'image' => 'http://images.unsplash.com/photo-1586495777744-4413f21062fa?w=500&q=80',
                'description' => 'Long lasting rich matte velvet lipstick.',
                'scope' => 'global',
                'is_featured' => false,
                'is_bestseller' => true,
                'is_active' => true,
            ],
        ];

        foreach ($products as $prod) {
            DB::table('products')->updateOrInsert(['sku' => $prod['sku']], array_merge($prod, [
                'updated_at' => now(),
            ]));
        }
    }
}
