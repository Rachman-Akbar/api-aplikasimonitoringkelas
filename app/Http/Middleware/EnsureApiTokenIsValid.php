<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // For API routes, ensure we're using token authentication
        // rather than session-based authentication

        // Check if this is an API request and not a web route
        if ($request->is('api/*')) {
            // If Authorization header is present, make sure Sanctum can process it
            if ($request->header('Authorization')) {
                // Laravel Sanctum should automatically handle Bearer tokens
                // but let's make sure it's properly configured to work without sessions
            }
        }

        return $next($request);
    }
}
