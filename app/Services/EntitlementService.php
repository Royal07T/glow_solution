<?php

namespace App\Services;

use App\Models\User;

class EntitlementService
{
    public function canAccessFeature(User $user, string $feature): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $subscription = $user->activeSubscription;
        if (!$subscription || !$subscription->isActive()) {
            return false;
        }

        $entitlements = $subscription->plan->entitlements ?? [];
        return $entitlements[$feature] ?? false;
    }

    public function getLimit(User $user, string $limit): int
    {
        if ($user->isAdmin()) {
            return -1; // unlimited
        }

        $subscription = $user->activeSubscription;
        if (!$subscription || !$subscription->isActive()) {
            return 0;
        }

        $entitlements = $subscription->plan->entitlements ?? [];
        return $entitlements[$limit] ?? 0;
    }
}
