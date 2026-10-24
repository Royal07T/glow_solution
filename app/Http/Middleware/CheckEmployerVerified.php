<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckEmployerVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isEmployer()) {
            abort(403);
        }

        $profile = $user->employerProfile;

        if (!$profile || !$profile->isVerified()) {
            return redirect()->route('employer.verification')
                ->with('error', 'Your employer account must be verified before you can perform this action.');
        }

        return $next($request);
    }
}
