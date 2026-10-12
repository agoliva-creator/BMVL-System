<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Check if the user is logged in AND their role is exactly 'admin'
        if (Auth::check() && Auth::user()->role === 'admin') {
            // Let them pass through to the intended page
            return $next($request); 
        }

        // 2. If they are not an admin, crash the request with a 403 Forbidden error
        abort(403, 'UNAUTHORIZED: You do not have administrator privileges to view this page.');
    }
}