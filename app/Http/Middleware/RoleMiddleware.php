<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,...$roles): Response
    {
        if(!auth()->check()){
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $userRole = auth()->user()->role;
        if(!in_array($userRole,$roles)){
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return $next($request);
    }
}
