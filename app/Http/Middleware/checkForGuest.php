<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class checkForGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('api')->check()) {
            if (!$request->guest_email) return $next($request);
            else return response()->json(['message' => 'Unauthorized action'], 401);
        }
            
        if (!$request->guest_email) return response()->json(['message' => 'Unauthorized action'], 401);
        
        return $next($request);
    }
}
