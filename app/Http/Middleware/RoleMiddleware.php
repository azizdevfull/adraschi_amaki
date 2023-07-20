<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if ($request->user() && $request->user()->role == $role) {
            return $next($request);
        }

        return response()->json([
            'message' => 'You are not authorized to access this page.'
        ], 403);
    }
}
