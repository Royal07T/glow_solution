<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->with(['jobSeekerProfile', 'employerProfile']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%')
                    ->orWhere('phone', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_suspended', $request->status === 'suspended');
        }

        if ($request->filled('identity_status')) {
            $query->whereHas('jobSeekerProfile', function ($q) use ($request) {
                $q->where('identity_verification_status', $request->identity_status);
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['jobSeekerProfile.identityReviewer', 'employerProfile', 'subscriptions.plan', 'cvs']);
        $applicationsCount = 0;
        if ($user->jobSeekerProfile) {
            $applicationsCount = $user->applications()->count();
        } elseif ($user->employerProfile) {
            $applicationsCount = Application::whereHas('job', function ($q) use ($user) {
                $q->where('employer_profile_id', $user->employerProfile->id);
            })->count();
        }
        $logs = AuditLog::where('user_id', $user->id)->latest()->limit(20)->get();

        return view('admin.users.show', compact('user', 'applicationsCount', 'logs'));
    }

    public function approveIdentity(Request $request, User $user)
    {
        $profile = $user->jobSeekerProfile;

        abort_unless($profile && $profile->hasCompleteIdentitySubmission(), 404);

        if ($profile->identity_verification_status !== 'pending') {
            return redirect()->back()->with('error', 'This identity submission is not awaiting review.');
        }

        $profile->forceFill([
            'identity_verification_status' => 'approved',
            'identity_reviewed_by' => $request->user()->id,
            'identity_reviewed_at' => now(),
            'identity_verification_notes' => null,
        ])->save();

        try {
            AuditLog::record(
                'job_seeker.identity_approved',
                $profile,
                ['identity_verification_status' => 'pending'],
                ['identity_verification_status' => 'approved'],
            );
        } catch (\Throwable $e) {
        }

        $user->notify(new SystemNotification(
            'Identity verified',
            'Your job seeker identity has been verified. Employers will now see the verified badge on your profile.',
            route('jobseeker.profile.edit'),
            'success',
        ));

        return redirect()->back()->with('success', 'Job seeker identity verified successfully.');
    }

    public function rejectIdentity(Request $request, User $user)
    {
        $profile = $user->jobSeekerProfile;

        abort_unless($profile && $profile->hasCompleteIdentitySubmission(), 404);

        if ($profile->identity_verification_status !== 'pending') {
            return redirect()->back()->with('error', 'This identity submission is not awaiting review.');
        }

        $validated = $request->validate([
            'identity_verification_notes' => ['required', 'string', 'max:1000'],
        ]);

        $profile->forceFill([
            'identity_verification_status' => 'rejected',
            'identity_reviewed_by' => $request->user()->id,
            'identity_reviewed_at' => now(),
            'identity_verification_notes' => $validated['identity_verification_notes'],
        ])->save();

        try {
            AuditLog::record(
                'job_seeker.identity_rejected',
                $profile,
                ['identity_verification_status' => 'pending'],
                ['identity_verification_status' => 'rejected'],
            );
        } catch (\Throwable $e) {
        }

        $user->notify(new SystemNotification(
            'Identity verification rejected',
            'Your identity verification was rejected. Reason: '.$validated['identity_verification_notes'],
            route('jobseeker.profile.edit'),
            'warning',
        ));

        return redirect()->back()->with('success', 'Job seeker identity submission rejected.');
    }

    public function suspend(Request $request, User $user)
    {
        $validated = $request->validate([
            'suspension_reason' => 'required|string',
        ]);

        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot suspend admin users.');
        }

        $user->update([
            'is_suspended' => true,
            'suspended_at' => now(),
            'suspension_reason' => $validated['suspension_reason'],
        ]);

        try {
            AuditLog::record('user.suspended', $user, ['is_suspended' => false], ['is_suspended' => true]);
        } catch (\Throwable $e) {
        }

        return redirect()->back()->with('success', 'User suspended successfully.');
    }

    public function unsuspend(User $user)
    {
        $user->update([
            'is_suspended' => false,
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        try {
            AuditLog::record('user.unsuspended', $user, ['is_suspended' => true], ['is_suspended' => false]);
        } catch (\Throwable $e) {
        }

        return redirect()->back()->with('success', 'User unsuspended successfully.');
    }
}
