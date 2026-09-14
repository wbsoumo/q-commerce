<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    // Auto Database Import & Installer Route
    public function importDatabase()
    {
        try {
            Artisan::call('migrate:fresh', ['--force' => true]);
            Artisan::call('db:seed', [
                '--class' => 'Database\\Seeders\\QCommerceSeeder',
                '--force' => true
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Multi-Vendor Multi-Store Database tables successfully imported & seeded!',
                'timestamp' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Professional Dashboard View
    public function dashboard()
    {
        $totalStores = DB::table('stores')->count();
        $totalProducts = DB::table('products')->count();
        $totalCategories = DB::table('categories')->count();
        $totalOrders = DB::table('orders')->count();
        $totalRevenue = DB::table('orders')->where('status', '!=', 'Cancelled')->sum('grand_total');

        $stores = DB::table('stores')->get();
        $recentOrders = DB::table('orders')
            ->leftJoin('stores', 'orders.store_id', '=', 'stores.id')
            ->select('orders.*', 'stores.name as store_name')
            ->orderBy('orders.created_at', 'desc')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact('totalStores', 'totalProducts', 'totalCategories', 'totalOrders', 'totalRevenue', 'stores', 'recentOrders'));
    }

    // Stores List View
    public function stores()
    {
        $stores = DB::table('stores')
            ->leftJoin('users', function($join) {
                $join->on('stores.id', '=', 'users.store_id')
                     ->where('users.role', '=', 'store_manager');
            })
            ->select('stores.*', 'users.name as manager_name', 'users.email as manager_email')
            ->orderBy('stores.id', 'desc')
            ->get();

        return view('admin.stores.index', compact('stores'));
    }

    // Store Create Form
    public function createStore()
    {
        return view('admin.stores.create');
    }

    // Save Store & Automatically Assign/Create Store Manager
    public function storeStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|unique:stores,code',
            'address' => 'required|string',
            'city' => 'required|string',
            'pincode' => 'required|string',
            'manager_name' => 'required|string',
            'manager_email' => 'required|email|unique:users,email',
            'manager_password' => 'required|string|min:6',
        ]);

        $storeId = DB::table('stores')->insertGetId([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'address' => $validated['address'],
            'latitude' => $request->input('latitude', 23.4013),
            'longitude' => $request->input('longitude', 88.5010),
            'city' => $validated['city'],
            'pincode' => $validated['pincode'],
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Store Manager Account
        DB::table('users')->insert([
            'name' => $validated['manager_name'],
            'email' => $validated['manager_email'],
            'password' => Hash::make($validated['manager_password']),
            'role' => 'store_manager',
            'store_id' => $storeId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/stores')->with('success', 'Store and Store Manager created successfully!');
    }

    // Products List View (Global & Store Specific Filter)
    public function products(Request $request)
    {
        $query = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->leftJoin('stores', 'products.store_id', '=', 'stores.id')
            ->select('products.*', 'categories.name as category_name', 'stores.name as store_name');

        if ($request->has('scope') && in_array($request->scope, ['global', 'store_specific'])) {
            $query->where('products.scope', $request->scope);
        }

        if ($request->has('store_id') && $request->store_id) {
            $query->where('products.store_id', $request->store_id);
        }

        $products = $query->orderBy('products.id', 'desc')->get();
        $stores = DB::table('stores')->get();

        return view('admin.products.index', compact('products', 'stores'));
    }

    // Create Product Form
    public function createProduct()
    {
        $categories = DB::table('categories')->where('is_active', true)->get();
        $stores = DB::table('stores')->where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'stores'));
    }

    // Save Product with Scope (Global vs Store-Specific)
    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string',
            'sku' => 'required|string|unique:products,sku',
            'unit' => 'required|string',
            'price' => 'required|numeric',
            'mrp' => 'required|numeric',
            'stock' => 'required|integer',
            'scope' => 'required|in:global,store_specific',
            'store_id' => 'nullable|required_if:scope,store_specific|exists:stores,id',
        ]);

        DB::table('products')->insert([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'unit' => $validated['unit'],
            'price' => $validated['price'],
            'mrp' => $validated['mrp'],
            'stock' => $validated['stock'],
            'scope' => $validated['scope'],
            'store_id' => $validated['scope'] === 'store_specific' ? $validated['store_id'] : null,
            'image' => $request->input('image', 'image 41.png'),
            'description' => $request->input('description', ''),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/products')->with('success', 'Product created successfully!');
    }

    // STORE MANAGER PORTAL VIEW: Specific Store Inventory Management
    public function storeManagerPortal(Request $request)
    {
        $storeId = $request->query('store_id', 1);
        $store = DB::table('stores')->where('id', $storeId)->first();

        // Get all products (Global + Store Specific for this store)
        $products = DB::table('products')
            ->where(function($q) use ($storeId) {
                $q->where('scope', 'global')
                  ->orWhere('store_id', $storeId);
            })
            ->leftJoin('store_product_inventories', function($join) use ($storeId) {
                $join->on('products.id', '=', 'store_product_inventories.product_id')
                     ->where('store_product_inventories.store_id', '=', $storeId);
            })
            ->select(
                'products.*',
                'store_product_inventories.custom_price',
                'store_product_inventories.custom_mrp',
                'store_product_inventories.custom_stock',
                'store_product_inventories.is_available'
            )
            ->get();

        $stores = DB::table('stores')->get();

        return view('admin.manager.index', compact('store', 'products', 'stores'));
    }

    // Update Store Manager Inventory
    public function updateStoreInventory(Request $request)
    {
        $storeId = $request->input('store_id');
        $productId = $request->input('product_id');

        DB::table('store_product_inventories')->updateOrInsert(
            ['store_id' => $storeId, 'product_id' => $productId],
            [
                'custom_price' => $request->input('custom_price'),
                'custom_mrp' => $request->input('custom_mrp'),
                'custom_stock' => $request->input('custom_stock', 0),
                'is_available' => $request->has('is_available') ? true : false,
                'updated_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'Store inventory updated successfully!');
    }
}
