<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['statusCode' => 401, 'message' => 'Unauthorized'], 401);
        }

        if (!$user->roles->pluck('name')->intersect($roles)->count()) {
            return response()->json(['statusCode' => 403, 'message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
