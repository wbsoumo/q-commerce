<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StoreManagerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('manager.login')->with('error', 'Please log in first.');
        }

        if (!in_array($user->role, ['admin', 'store_manager'])) {
            return redirect()->route('manager.login')->with('error', 'Unauthorized access.');
        }

        return $next($request);
    }
}
