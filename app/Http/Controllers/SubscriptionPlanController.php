<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\SubscriptionPlan;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $role = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->when(in_array($role, ['job_seeker', 'employer'], true), function ($query) use ($role) {
                $query->where('target_role', $role);
            })
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        $subscription = $user->activeSubscription()->with('plan')->first()
            ?? $user->subscriptions()->with('plan')->latest()->first();

        $payments = $user->payments()->latest()->take(10)->get();

        return view('billing.index', compact('plans', 'subscription', 'payments'));
    }
}
