<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitSensitiveActions
{
    public function handle(Request $request, Closure $next, string $key = 'default'): Response
    {
        $key = $key . '|' . $request->ip();
        
        if ($request->user()) {
            $key .= '|' . $request->user()->id;
        }

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json(['message' => 'Too many attempts. Try again in ' . $seconds . ' seconds.'], 429);
        }

        RateLimiter::hit($key, 60);

        return $next($request);
    }
}
