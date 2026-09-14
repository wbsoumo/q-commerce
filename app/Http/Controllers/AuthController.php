<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show Admin Login Page
    public function showAdminLogin()
    {
        if (session()->has('user_id') && session()->get('user_role') === 'admin') {
            return redirect('/admin');
        }
        return view('auth.admin_login');
    }

    // Process Admin Login Form Submission
    public function processAdminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = DB::table('users')->where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->role !== 'admin') {
                return redirect()->back()->with('error', 'Unauthorized access! You must be a Super Admin to log in here.');
            }

            session([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'store_id' => $user->store_id,
            ]);

            return redirect('/admin')->with('success', 'Welcome back Super Admin!');
        }

        return redirect()->back()->with('error', 'Invalid admin credentials entered.');
    }

    // Show Store Manager Login Page
    public function showManagerLogin()
    {
        if (session()->has('user_id') && session()->get('user_role') === 'store_manager') {
            $storeId = session()->get('store_id', 1);
            return redirect('/admin/store-manager?store_id=' . $storeId);
        }
        return view('auth.manager_login');
    }

    // Process Store Manager Login Form Submission
    public function processManagerLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = DB::table('users')->where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->role !== 'store_manager' && $user->role !== 'admin') {
                return redirect()->back()->with('error', 'Unauthorized access! Account is not assigned as Store Manager.');
            }

            session([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'store_id' => $user->store_id ?? 1,
            ]);

            $storeId = $user->store_id ?? 1;
            return redirect('/admin/store-manager?store_id=' . $storeId)->with('success', 'Store Manager authenticated successfully.');
        }

        return redirect()->back()->with('error', 'Invalid store manager credentials entered.');
    }

    // Logout User Session
    public function logout()
    {
        session()->forget(['user_id', 'user_name', 'user_email', 'user_role', 'store_id']);
        return redirect('/admin/login')->with('success', 'Successfully logged out.');
    }
}
