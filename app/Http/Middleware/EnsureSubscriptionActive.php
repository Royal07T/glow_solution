<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSubscriptionActive
{
    public function handle(Request $request, Closure $next, string $feature = null): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        // Admins bypass subscription checks
        if ($user->isAdmin()) {
            return $next($request);
        }

        $subscription = $user->activeSubscription;

        if (!$subscription || !$subscription->isActive()) {
            return redirect()->route('subscriptions.plans')
                ->with('error', 'An active subscription is required to access this feature.');
        }

        // Check entitlements if feature specified
        if ($feature && $subscription->plan) {
            $entitlements = $subscription->plan->entitlements ?? [];
            if (isset($entitlements[$feature]) && !$entitlements[$feature]) {
                return redirect()->route('subscriptions.plans')
                    ->with('error', 'Your subscription plan does not include this feature.');
            }
        }

        return $next($request);
    }
}
