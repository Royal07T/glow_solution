<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\User;
use App\Notifications\SystemNotification;
use App\Support\SearchTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'role' => ['nullable', Rule::enum(UserRole::class)],
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['active', 'suspended'])],
            'identity_status' => ['nullable', Rule::in(['unsubmitted', 'pending', 'approved', 'rejected'])],
        ]);

        $search = SearchTerm::clean($validated['search'] ?? null);

        $query = User::query()->with(['jobSeekerProfile', 'employerProfile']);

        if (filled($validated['role'] ?? null)) {
            $query->where('role', $validated['role']);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%');
            });
        }

        if (filled($validated['status'] ?? null)) {
            $query->where('is_suspended', $validated['status'] === 'suspended');
        }

        if (filled($validated['identity_status'] ?? null)) {
            $query->whereHas('jobSeekerProfile', function ($q) use ($validated) {
                $q->where('identity_verification_status', $validated['identity_status']);
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'phone.regex' => 'Please enter a valid phone number.',
        ]);

        $firstName = trim($validated['first_name']);
        $lastName = trim($validated['last_name']);

        $user = User::create([
            'name' => trim($firstName.' '.$lastName),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => UserRole::ADMIN->value,
        ]);

        $user->forceFill(['email_verified_at' => now()])->save();

        try {
            AuditLog::record(
                'admin.created',
                $user,
                [],
                ['role' => UserRole::ADMIN->value, 'email' => $user->email],
            );
        } catch (\Throwable $e) {
        }

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'Administrator account created successfully.');
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
            'suspension_reason' => 'required|string|max:1000',
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
