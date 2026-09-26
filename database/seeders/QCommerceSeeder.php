<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QCommerceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Multi-Region Dark Stores
        $stores = [
            [
                'id' => 1,
                'name' => 'Blinkit Krishnanagar Dark Store',
                'code' => 'KNGR-DS01',
                'address' => 'RATANR FLAT, 11E Krishnanagar Main Hub',
                'latitude' => 23.4013,
                'longitude' => 88.5010,
                'delivery_radius_km' => 15.00,
                'city' => 'Krishnanagar',
                'pincode' => '741101',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Blinkit Kolkata Central Dark Store',
                'code' => 'KOL-DS02',
                'address' => 'Park Street Express Delivery Hub, Kolkata',
                'latitude' => 22.5726,
                'longitude' => 88.3639,
                'delivery_radius_km' => 15.00,
                'city' => 'Kolkata',
                'pincode' => '700016',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Blinkit Siliguri City Dark Store',
                'code' => 'SLG-DS03',
                'address' => 'Hill Cart Road Main Hub, Siliguri',
                'latitude' => 26.7271,
                'longitude' => 88.3953,
                'delivery_radius_km' => 15.00,
                'city' => 'Siliguri',
                'pincode' => '734001',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Blinkit Kalyani Tech Park Hub',
                'code' => 'KLY-DS04',
                'address' => 'KGEC Campus Road, Block C, Kalyani',
                'latitude' => 22.9750,
                'longitude' => 88.4344,
                'delivery_radius_km' => 12.00,
                'city' => 'Kalyani',
                'pincode' => '741235',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($stores as $store) {
            DB::table('stores')->updateOrInsert(['id' => $store['id']], $store);
        }

        // 2. Seed Categories with local category icons
        $categories = [
            [
                'name' => 'Vegetables & Fruits',
                'slug' => 'vegetables-fruits',
                'image' => '/uploads/categories/01_vegetables_fruits.png',
                'display_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Dairy, Bread & Eggs',
                'slug' => 'dairy-bread-eggs',
                'image' => '/uploads/categories/02_dairy_bread_eggs.png',
                'display_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Snacks & Beverages',
                'slug' => 'snacks-beverages',
                'image' => '/uploads/categories/03_snacks_beverages.png',
                'display_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Personal Care',
                'slug' => 'personal-care',
                'image' => '/uploads/categories/04_personal_care.png',
                'display_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Home Care & Cleaning',
                'slug' => 'home-care-cleaning',
                'image' => '/uploads/categories/05_home_care_cleaning.png',
                'display_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Atta, Dal & Rice',
                'slug' => 'atta-dal-rice',
                'image' => '/uploads/categories/06_atta_dal_rice.png',
                'display_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Oil, Ghee & Masala',
                'slug' => 'oil-ghee-masala',
                'image' => '/uploads/categories/07_oil_ghee_masala.png',
                'display_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Instant Food',
                'slug' => 'instant-food',
                'image' => '/uploads/categories/08_instant_food.png',
                'display_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Beverages & Drinks',
                'slug' => 'beverages-drinks',
                'image' => '/uploads/categories/09_beverages.png',
                'display_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Baby Care',
                'slug' => 'baby-care',
                'image' => '/uploads/categories/10_baby_care.png',
                'display_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Pet Care',
                'slug' => 'pet-care',
                'image' => '/uploads/categories/11_pet_care.png',
                'display_order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'Frozen Food',
                'slug' => 'frozen-food',
                'image' => '/uploads/categories/12_frozen_food.png',
                'display_order' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Bakery & Sweets',
                'slug' => 'bakery-sweets',
                'image' => '/uploads/categories/13_bakery_sweets.png',
                'display_order' => 13,
                'is_active' => true,
            ],
            [
                'name' => 'Fresh Fruits',
                'slug' => 'fresh-fruits',
                'image' => '/uploads/categories/14_fresh_fruits.png',
                'display_order' => 14,
                'is_active' => true,
            ],
            [
                'name' => 'Kitchen & Household',
                'slug' => 'kitchen-household',
                'image' => '/uploads/categories/15_kitchen_household.png',
                'display_order' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Organic & Healthy Living',
                'slug' => 'organic-healthy-living',
                'image' => '/uploads/categories/16_organic_healthy_living.png',
                'display_order' => 16,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(['slug' => $cat['slug']], array_merge($cat, [
                'updated_at' => now(),
            ]));
        }

        // 3. Seed Products with web image URLs (HTTPS)
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
                'image' => 'https://images.unsplash.com/photo-1602874801007-bd458bb1b8b6?w=500&q=80',
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
                'image' => 'https://images.unsplash.com/photo-1599785209707-a456fc1337cc?w=500&q=80',
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
                'image' => 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=500&q=80',
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
                'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=500&q=80',
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
                'image' => 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?w=500&q=80',
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
                'image' => 'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=500&q=80',
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
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80',
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
                'image' => 'https://images.unsplash.com/photo-1586495777744-4413f21062fa?w=500&q=80',
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

        // 4. Seed Customer Profile for Soumojit Saha (8016222991)
        DB::table('customers')->updateOrInsert(
            ['phone' => '8016222991'],
            [
                'name' => 'Soumojit Saha',
                'email' => 'soumojit.saha@gmail.com',
                'status' => 'Active',
                'is_vip' => true,
                'total_orders' => 12,
                'total_spent' => 2850.00,
                'last_order_at' => now()->subHours(2),
                'notes' => 'VIP Customer - Prefers Cash on Delivery & Express delivery to 11E Krishnanagar',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 6. Seed Bank & Promotional Coupons
        if (DB::table('coupons')->count() === 0) {
            DB::table('coupons')->insert([
                [
                    'code' => 'ONECARD30',
                    'title' => 'Flat ₹30 Off',
                    'description' => 'Applicable on transactions using OneCard Credit Cards. Max discount ₹30.',
                    'discount_type' => 'flat',
                    'discount_value' => 30.00,
                    'min_cart_amount' => 149.00,
                    'allowed_order_type' => 'all',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'DIGISMART',
                    'title' => 'Get 10% OFF upto ₹200',
                    'description' => 'Applicable on Standard Chartered Digismart Card. Max discount ₹200.',
                    'discount_type' => 'percentage',
                    'discount_value' => 10.00,
                    'max_discount_amount' => 200.00,
                    'min_cart_amount' => 199.00,
                    'allowed_order_type' => 'all',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'FREEDEL',
                    'title' => 'Get FREE Delivery',
                    'description' => 'Waives standard delivery charges on Home Delivery orders.',
                    'discount_type' => 'free_delivery',
                    'discount_value' => 25.00,
                    'is_free_delivery' => true,
                    'min_cart_amount' => 99.00,
                    'allowed_order_type' => 'delivery',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'PAYTMNEW',
                    'title' => 'Flat ₹100 Cashback',
                    'description' => 'Applicable for users who are new to Paytm and transacting first time.',
                    'discount_type' => 'flat',
                    'discount_value' => 100.00,
                    'min_cart_amount' => 299.00,
                    'is_first_order_only' => true,
                    'allowed_order_type' => 'all',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }
}
