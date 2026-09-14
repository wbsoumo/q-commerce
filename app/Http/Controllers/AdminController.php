<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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
                'message' => 'Database tables successfully imported & seeded!',
                'timestamp' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database import failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // Admin LTE 3 Dashboard View
    public function dashboard()
    {
        $totalOrders = DB::table('orders')->count();
        $totalProducts = DB::table('products')->count();
        $totalCategories = DB::table('categories')->count();
        $recentOrders = DB::table('orders')->orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('totalOrders', 'totalProducts', 'totalCategories', 'recentOrders'));
    }

    // Products List View
    public function products()
    {
        $products = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.*', 'categories.name as category_name')
            ->orderBy('products.id', 'desc')
            ->get();

        return view('admin.products', compact('products'));
    }
}
