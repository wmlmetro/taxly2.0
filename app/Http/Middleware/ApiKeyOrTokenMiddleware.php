<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyOrTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check for Sanctum authentication first (using default guard)
        if ($request->user()) {
            return $next($request);
        }

        // Check for API key authentication
        $apiKey = $request->header('x-api-key') ?? $request->header('X-Api-Key');
        if ($apiKey && ApiKey::where('key', $apiKey)->where('active', true)->exists()) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
