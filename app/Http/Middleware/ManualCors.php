<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ManualCors
{
    public function handle(Request $request, Closure $next)
    {
        // Set CORS headers
        return $next($request)
            ->header('Access-Control-Allow-Origin', 'http://localhost:3000') // Change to your allowed origin
            ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization') // Add other allowed headers as needed
            ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS') // Allow specific methods
            ->header('Access-Control-Allow-Credentials', 'true'); // Allow credentials if needed
    }
}
