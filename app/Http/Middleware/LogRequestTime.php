<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequestTime
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);
        $response = $next($request);
        $duration = round((microtime(true) - $start) * 1000); // ms
        $path = $request->method() . ' ' . $request->path();
        Log::info("API {$path} took {$duration}ms");
        return $response;
    }
}
