<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceSessionTimeout
{
    public const STARTED_AT_KEY = 'auth_session_started_at';

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        $lifetime = max(0, (int) config('session.absolute_lifetime', 0));

        if ($lifetime === 0) {
            return $next($request);
        }

        $startedAt = (int) $request->session()->get(self::STARTED_AT_KEY, 0);

        if ($startedAt === 0) {
            $request->session()->put(self::STARTED_AT_KEY, now()->timestamp);

            return $next($request);
        }

        if (now()->timestamp - $startedAt < $lifetime * 60) {
            return $next($request);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your session has expired. Please sign in again.',
            ], 401);
        }

        return redirect()->route('login')
            ->with('error', 'Your session expired for security reasons. Please sign in again.');
    }
}
