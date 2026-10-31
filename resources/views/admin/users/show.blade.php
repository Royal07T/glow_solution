@extends('layouts.portal')

@section('title', $user->name)
@section('subtitle', $user->email)

@section('content')
    @php($seekerProfile = $user->jobSeekerProfile)

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        @if($seekerProfile?->photo_path)
                            <img src="{{ route('jobseekers.photo', $user) }}" alt="" class="h-12 w-12 rounded-full object-cover">
                        @endif
                        <div>
                            <h2 class="font-display text-xl font-semibold text-slate-900">{{ $user->name }}</h2>
                            <p class="text-sm text-slate-600">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <x-badge variant="brand">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</x-badge>
                        <x-badge :variant="$user->is_suspended ? 'danger' : 'success'">{{ $user->is_suspended ? 'Suspended' : 'Active' }}</x-badge>
                    </div>
                </div>

                <dl class="mt-5 grid gap-3 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Joined</dt>
                        <dd class="text-sm font-medium text-slate-800">{{ $user->created_at->format('M j, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Email verified</dt>
                        <dd class="text-sm font-medium text-slate-800">{{ $user->email_verified_at?->format('M j, Y') ?? 'Not verified' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Last login</dt>
                        <dd class="text-sm font-medium text-slate-800">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Applications</dt>
                        <dd class="text-sm font-medium text-slate-800">{{ $applicationsCount }}</dd>
                    </div>
                </dl>

                @if($user->is_suspended)
                    <div class="mt-6 rounded-lg border border-red-200 bg-red-50 p-4">
                        <p class="text-sm font-semibold text-red-900">Suspended {{ $user->suspended_at?->format('M j, Y') }}</p>
                        <p class="mt-1 text-sm text-red-800">{{ $user->suspension_reason }}</p>
                    </div>
                @endif
            </section>

            @if($user->isEmployer() && $user->employerProfile)
                <section class="rounded-xl border border-slate-200 bg-white p-6">
                    <h2 class="font-display text-base font-semibold text-slate-900">Employer profile</h2>
                    <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Company</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $user->employerProfile->company_name ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Contact</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $user->employerProfile->contact_person ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Verification</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ ucfirst($user->employerProfile->verification_status) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Location</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ collect([$user->employerProfile->city, $user->employerProfile->country])->filter()->join(', ') ?: '—' }}</dd>
                        </div>
                    </dl>
                    @if($user->employerProfile->verification_status === 'submitted')
                        <a href="{{ route('admin.verifications.index', ['status' => 'pending']) }}" class="mt-4 inline-flex rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Review verification</a>
                    @endif
                </section>
            @endif

            @if($user->isJobSeeker() && $seekerProfile)
                <section class="rounded-xl border border-slate-200 bg-white p-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="font-display text-base font-semibold text-slate-900">Job seeker profile</h2>
                            <p class="mt-1 text-sm text-slate-600">Review the details submitted by this job seeker.</p>
                        </div>
                        <x-badge :variant="$seekerProfile->identityStatusBadgeVariant()">{{ $seekerProfile->identityStatusLabel() }}</x-badge>
                    </div>

                    <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Full name</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ collect([$seekerProfile->first_name, $seekerProfile->last_name])->filter()->join(' ') ?: $user->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Phone</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $user->phone ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Date of birth</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $seekerProfile->date_of_birth?->format('M j, Y') ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Gender</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $seekerProfile->gender ? ucfirst($seekerProfile->gender) : '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Location</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ collect([$seekerProfile->city, $seekerProfile->state, $seekerProfile->country])->filter()->join(', ') ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Address</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $seekerProfile->address ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Education</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $seekerProfile->education_level ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Experience</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $seekerProfile->experience_level ?: '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Work preference</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $seekerProfile->employment_preference ? ucfirst(str_replace('_', ' ', $seekerProfile->employment_preference)) : '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Available for work</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $seekerProfile->available_for_work ? 'Yes' : 'No' }}</dd>
                        </div>
                    </dl>

                    @if(filled($seekerProfile->bio))
                        <div class="mt-5">
                            <h3 class="text-xs uppercase tracking-wide text-slate-500">Professional summary</h3>
                            <p class="mt-1 whitespace-pre-line text-sm text-slate-700">{{ $seekerProfile->bio }}</p>
                        </div>
                    @endif

                    @if(! empty($seekerProfile->skills))
                        <div class="mt-5">
                            <h3 class="text-xs uppercase tracking-wide text-slate-500">Skills</h3>
                            <p class="mt-1 text-sm text-slate-700">{{ implode(', ', $seekerProfile->skills) }}</p>
                        </div>
                    @endif

                    <div class="mt-6 rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <h3 class="font-display text-sm font-semibold text-slate-900">Identity submission</h3>
                            <x-badge :variant="$seekerProfile->identityStatusBadgeVariant()">{{ $seekerProfile->identityStatusLabel() }}</x-badge>
                        </div>

                        <dl class="mt-3 grid gap-3 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-slate-500">ID type</dt>
                                <dd class="text-sm font-medium text-slate-800">{{ $seekerProfile->identificationTypeLabel() ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-slate-500">ID number</dt>
                                <dd class="text-sm font-medium text-slate-800">{{ $seekerProfile->identification_number ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-slate-500">Submitted</dt>
                                <dd class="text-sm font-medium text-slate-800">{{ $seekerProfile->identity_submitted_at?->format('M j, Y g:ia') ?: '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-slate-500">Reviewed</dt>
                                <dd class="text-sm font-medium text-slate-800">
                                    @if($seekerProfile->identity_reviewed_at)
                                        {{ $seekerProfile->identity_reviewed_at->format('M j, Y g:ia') }}
                                        @if($seekerProfile->identityReviewer)
                                            by {{ $seekerProfile->identityReviewer->name }}
                                        @endif
                                    @else
                                        —
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-4 border-t border-slate-200 pt-3">
                            <h4 class="text-xs uppercase tracking-wide text-slate-500">Guarantor</h4>
                            @if($seekerProfile->hasGuarantor())
                                <dl class="mt-2 grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-xs text-slate-500">Name</dt>
                                        <dd class="text-sm font-medium text-slate-800">{{ $seekerProfile->guarantor_name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-slate-500">Phone</dt>
                                        <dd class="text-sm font-medium text-slate-800">{{ $seekerProfile->guarantor_phone }}</dd>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <dt class="text-xs text-slate-500">Address</dt>
                                        <dd class="text-sm font-medium text-slate-800">{{ $seekerProfile->guarantor_address }}</dd>
                                    </div>
                                </dl>
                            @else
                                <p class="mt-2 text-sm text-slate-600">No guarantor details provided.</p>
                            @endif
                        </div>

                        @if($seekerProfile->identification_document_path)
                            <a href="{{ route('jobseekers.identification', $user) }}" target="_blank" rel="noopener" class="mt-3 inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                <x-icon name="document" class="h-4 w-4" /> View ID document
                            </a>
                        @else
                            <p class="mt-3 text-sm text-slate-600">No ID document uploaded.</p>
                        @endif

                        @if(filled($seekerProfile->identity_verification_notes))
                            <p class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm text-amber-800">{{ $seekerProfile->identity_verification_notes }}</p>
                        @endif
                    </div>

                    <div class="mt-6">
                        <h3 class="font-display text-sm font-semibold text-slate-900">CVs</h3>
                        @forelse($user->cvs as $cv)
                            <div class="mt-2 flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-200 px-3 py-2">
                                <div>
                                    <p class="text-sm font-medium text-slate-800">{{ $cv->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $cv->file_name }}{{ $cv->is_primary ? ' · Primary' : '' }}</p>
                                </div>
                                <a href="{{ route('cvs.download', $cv) }}" class="text-sm font-semibold text-brand-700 hover:underline">Download</a>
                            </div>
                        @empty
                            <p class="mt-2 text-sm text-slate-600">No CVs uploaded.</p>
                        @endforelse
                    </div>

                    @if($seekerProfile->identity_verification_status === 'pending')
                        <div class="mt-6 border-t border-slate-200 pt-5">
                            <h3 class="font-display text-sm font-semibold text-slate-900">Review this identity</h3>
                            <p class="mt-1 text-sm text-slate-600">Confirm the ID type, number and uploaded document match the job seeker before approving.</p>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <form method="POST" action="{{ route('admin.users.identity.approve', $user) }}">
                                    @csrf
                                    <button type="submit" data-confirm="Approve this job seeker's identity?" class="w-full rounded-lg bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">Approve identity</button>
                                </form>
                                <form method="POST" action="{{ route('admin.users.identity.reject', $user) }}" class="space-y-2">
                                    @csrf
                                    <label for="identity_verification_notes" class="sr-only">Rejection reason</label>
                                    <textarea id="identity_verification_notes" name="identity_verification_notes" rows="2" required maxlength="1000" placeholder="Reason for rejection" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
                                    <button type="submit" data-confirm="Reject this identity submission?" class="w-full rounded-lg bg-danger px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Reject identity</button>
                                </form>
                            </div>
                        </div>
                    @endif
                </section>
            @endif

            <section class="rounded-xl border border-slate-200 bg-white">
                <div class="border-b border-slate-200 px-6 py-4">
                    <h2 class="font-display text-base font-semibold text-slate-900">Recent activity</h2>
                </div>
                @forelse($logs as $log)
                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-6 py-3 last:border-0">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $log->action }}</p>
                            <p class="text-xs text-slate-500">{{ $log->created_at->format('M j, Y g:ia') }}</p>
                        </div>
                        <span class="text-xs text-slate-400">{{ $log->ip_address }}</span>
                    </div>
                @empty
                    <p class="px-6 py-6 text-sm text-slate-600">No activity recorded.</p>
                @endforelse
            </section>
        </div>

        <aside class="space-y-4">
            @if(! $user->isAdmin() && ! $user->is(auth()->user()))
                <section class="rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="font-display text-base font-semibold text-slate-900">
                        {{ $user->is_suspended ? 'Reinstate account' : 'Suspend account' }}
                    </h3>

                    @if($user->is_suspended)
                        <p class="mt-2 text-sm text-slate-600">Restoring access lets the user sign in again immediately.</p>
                        <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}" class="mt-4">
                            @csrf
                            <button type="submit" class="w-full rounded-lg bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">Unsuspend user</button>
                        </form>
                    @else
                        <p class="mt-2 text-sm text-slate-600">Suspension ends all active sessions on their next request.</p>
                        <form method="POST" action="{{ route('admin.users.suspend', $user) }}" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label for="suspension_reason" class="mb-1 block text-sm font-medium text-slate-700">Reason</label>
                                <textarea id="suspension_reason" name="suspension_reason" rows="3" required maxlength="500" class="w-full rounded-lg border border-slate-300 px-3 py-2"></textarea>
                            </div>
                            <button type="submit" data-confirm="Suspend this account?" class="w-full rounded-lg bg-danger px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Suspend user</button>
                        </form>
                    @endif
                </section>
            @endif

            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="font-display text-base font-semibold text-slate-900">Subscription</h3>
                @if($user->activeSubscription)
                    <p class="mt-2 text-sm text-slate-700">{{ $user->activeSubscription->plan->name }}</p>
                    <p class="text-xs text-slate-500">Renews {{ $user->activeSubscription->ends_at?->format('M j, Y') }}</p>
                @else
                    <p class="mt-2 text-sm text-slate-600">No active subscription.</p>
                @endif
            </section>

            <a href="{{ route('admin.users.index') }}" class="inline-flex w-full items-center justify-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Back to users</a>
        </aside>
    </div>
@endsection
