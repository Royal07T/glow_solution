@extends('layouts.portal')

@section('title', $user->name)
@section('subtitle', $user->email)

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-stone-200 bg-white p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-xl font-semibold text-slate-900">{{ $user->name }}</h2>
                        <p class="text-sm text-slate-600">{{ $user->email }}</p>
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
                <section class="rounded-xl border border-stone-200 bg-white p-6">
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
                        <a href="{{ route('admin.verifications.index', ['status' => 'pending']) }}" class="mt-4 inline-flex rounded-lg border border-stone-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-stone-50">Review verification</a>
                    @endif
                </section>
            @endif

            <section class="rounded-xl border border-stone-200 bg-white">
                <div class="border-b border-stone-200 px-6 py-4">
                    <h2 class="font-display text-base font-semibold text-slate-900">Recent activity</h2>
                </div>
                @forelse($logs as $log)
                    <div class="flex items-center justify-between gap-3 border-b border-stone-100 px-6 py-3 last:border-0">
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
                <section class="rounded-xl border border-stone-200 bg-white p-5">
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
                                <textarea id="suspension_reason" name="suspension_reason" rows="3" required maxlength="500" class="w-full rounded-lg border border-stone-300 px-3 py-2"></textarea>
                            </div>
                            <button type="submit" data-confirm="Suspend this account?" class="w-full rounded-lg bg-danger px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Suspend user</button>
                        </form>
                    @endif
                </section>
            @endif

            <section class="rounded-xl border border-stone-200 bg-white p-5">
                <h3 class="font-display text-base font-semibold text-slate-900">Subscription</h3>
                @if($user->activeSubscription)
                    <p class="mt-2 text-sm text-slate-700">{{ $user->activeSubscription->plan->name }}</p>
                    <p class="text-xs text-slate-500">Renews {{ $user->activeSubscription->ends_at?->format('M j, Y') }}</p>
                @else
                    <p class="mt-2 text-sm text-slate-600">No active subscription.</p>
                @endif
            </section>

            <a href="{{ route('admin.users.index') }}" class="inline-flex w-full items-center justify-center rounded-lg bg-stone-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-stone-200">Back to users</a>
        </aside>
    </div>
@endsection
