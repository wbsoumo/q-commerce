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

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->role !== 'admin') {
                return redirect()->back()->with('error', 'Unauthorized access! You must be a Super Admin to log in here.');
            }

            Auth::login($user);
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

    // Show Temp Admin Register Page
    public function showAdminRegister()
    {
        return view('auth.admin_register');
    }

    // Process Temp Admin Registration/Password Reset
    public function processAdminRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:4',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user) {
            $user->update([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'role' => 'admin',
            ]);
        } else {
            \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
            ]);
        }

        return redirect('/admin/login')->with('success', 'Admin account registered/updated successfully! You can now log in.');
    }

    // Show Store Manager Login Page
    public function showManagerLogin()
    {
        if (Auth::check() && Auth::user()->role === 'store_manager') {
            return redirect('/manager/dashboard');
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

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->role !== 'store_manager' && $user->role !== 'admin') {
                return redirect()->back()->with('error', 'Unauthorized access! Account is not assigned as Store Manager.');
            }

            Auth::login($user);
            session([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
                'store_id' => $user->store_id ?? 1,
            ]);

            return redirect('/manager/dashboard')->with('success', 'Store Manager authenticated successfully.');
        }

        return redirect()->back()->with('error', 'Invalid store manager credentials entered.');
    }

    // Logout User Session
    public function logout()
    {
        $isManager = Auth::check() && Auth::user()->role === 'store_manager';
        Auth::logout();
        session()->flush();

        if ($isManager) {
            return redirect('/manager/login')->with('success', 'Successfully logged out.');
        }
        return redirect('/admin/login')->with('success', 'Successfully logged out.');
    }
}
