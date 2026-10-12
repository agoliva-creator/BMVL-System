<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access if the user is logged in AND their role is 'staff' or 'admin'
        if (auth()->check() && in_array(auth()->user()->role, ['staff', 'admin'])) {
            return $next($request);
        }

        // If they are an athlete trying to access staff pages, kick them out
        abort(403, 'Unauthorized Access: Staff Only');
    }
}