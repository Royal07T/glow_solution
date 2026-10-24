<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if ($user && $user->isAdmin() && !$user->two_factor_secret) {
            // In production, would enforce 2FA setup
            // return redirect()->route('two-factor.setup');
        }

        return $next($request);
    }
}
