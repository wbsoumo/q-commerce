<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnlyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('admin.login')->with('error', 'Please log in as Super Admin first.');
        }

        if ($user->role === 'store_manager') {
            // Redirect store managers to their dedicated manager portal
            return redirect('/manager/dashboard')->with('error', 'Access restricted to Store Manager Portal only.');
        }

        if ($user->role !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
