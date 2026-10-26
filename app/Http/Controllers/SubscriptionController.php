<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function activate(Request $request, SubscriptionPlan $plan)
    {
        $user = auth()->user();
        $role = $user->role;
        if ($role instanceof UserRole) {
            $isEmployer = $role === UserRole::EMPLOYER;
            $isJobSeeker = $role === UserRole::JOB_SEEKER;
            $roleStr = $role->value;
        } else {
            $roleStr = is_string($role) ? $role : (string) $role;
            $isEmployer = in_array($roleStr, ['employer', 'EMPLOYER', UserRole::EMPLOYER->value ?? 'employer']);
            $isJobSeeker = in_array($roleStr, ['job_seeker', 'job-seeker', 'JOB_SEEKER', UserRole::JOB_SEEKER->value ?? 'job_seeker']);
        }
        if ($isEmployer && ($plan->target_role && $plan->target_role !== $roleStr && $plan->target_role !== 'employer')) {
            abort(403);
        }
        if ($isJobSeeker && ($plan->target_role && $plan->target_role !== $roleStr && $plan->target_role !== 'job_seeker')) {
            abort(403);
        }

        $isFree = (bool) ($plan->is_free ?? ((float) $plan->price <= 0));

        if ($isFree && ($paidSubscription = $user->activePaidSubscription())) {
            return redirect()->route('billing.index')->with(
                'error',
                'Your '.$paidSubscription->plan->name.' plan is active until '.$paidSubscription->ends_at->format('M j, Y').'. You can switch to a free plan after it expires.'
            );
        }

        if ($isEmployer) {
            $profile = $user->employerProfile;
            if (! $profile) {
                return redirect()->route('employer.dashboard')->with('error', 'Employer profile required.');
            }
            $subscription = $user->subscriptions()->first();
            if (! $isFree) {
                abort(403);
            }
            if ($subscription) {
                $subscription->update([
                    'subscription_plan_id' => $plan->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => $plan->endsAt(),
                ]);
            } else {
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'status' => 'active',
                    'starts_at' => now(),
                    'ends_at' => $plan->endsAt(),
                ]);
            }
            try {
                AuditLog::record('subscription.activated', $subscription, [], ['subscription_plan_id' => $plan->id, 'status' => $subscription->status]);
            } catch (\Throwable $e) {
            }

            $user->notify(new SystemNotification(
                'Subscription activated',
                'Your '.$plan->name.' plan is now active.',
                route('billing.index', absolute: false),
                'success',
            ));

            return redirect()->route('employer.dashboard')->with('success', 'Plan activated.');
        }

        if ($isJobSeeker) {
            $profile = $user->jobSeekerProfile;
            if (! $profile) {
                return redirect()->route('jobseeker.dashboard')->with('error', 'Profile required.');
            }
            $subscription = $user->subscriptions()->first();
            if ($subscription) {
                $subscription->update([
                    'subscription_plan_id' => $plan->id,
                    'status' => $isFree ? 'active' : 'pending',
                    'starts_at' => now(),
                    'ends_at' => $plan->endsAt(),
                ]);
            } else {
                $subscription = Subscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'status' => $isFree ? 'active' : 'pending',
                    'starts_at' => now(),
                    'ends_at' => $plan->endsAt(),
                ]);
            }
            try {
                AuditLog::record('subscription.activated', $subscription, [], ['subscription_plan_id' => $plan->id, 'status' => $subscription->status]);
            } catch (\Throwable $e) {
            }

            $user->notify($isFree
                ? new SystemNotification(
                    'Subscription activated',
                    'Your '.$plan->name.' plan is now active.',
                    route('billing.index', absolute: false),
                    'success',
                )
                : new SystemNotification(
                    'Subscription pending payment',
                    'Your '.$plan->name.' plan is pending activation. Complete payment to unlock it.',
                    route('billing.index', absolute: false),
                    'warning',
                ));

            if ($isFree) {
                return redirect()->route('jobseeker.dashboard')->with('success', 'Plan activated.');
            }

            return redirect()->route('subscriptions.plans')->with('error', 'This plan requires payment.');
        }

        abort(403);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect()->route('billing.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscription)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscription $subscription)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        //
    }
}
