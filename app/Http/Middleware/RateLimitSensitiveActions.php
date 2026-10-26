<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class RateLimitSensitiveActions
{
    public function handle(Request $request, Closure $next, string $key = 'default', int $maxAttempts = 5, int $decaySeconds = 60): Response
    {
        $rateKey = $key.'|'.$request->ip();

        if ($request->user()) {
            $rateKey .= '|'.$request->user()->id;
        }

        if (RateLimiter::tooManyAttempts($rateKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($rateKey);
            $message = 'Too many attempts. Try again in '.$seconds.' seconds.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 429, ['Retry-After' => $seconds]);
            }

            return back()->with('error', $message);
        }

        RateLimiter::hit($rateKey, $decaySeconds);

        return $next($request);
    }
}
