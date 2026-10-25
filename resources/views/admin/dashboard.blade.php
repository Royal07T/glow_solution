@extends('layouts.portal')

@section('title', 'Admin dashboard')
@section('subtitle', 'Platform activity, approvals and recent account events.')

@section('content')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total users" :value="$stats['total_users']" icon="users" tone="brand" :href="route('admin.users.index')" />
        <x-stat-card label="Job seekers" :value="$stats['job_seekers']" icon="user" tone="info" hint="Registered candidates" />
        <x-stat-card label="Employers" :value="$stats['employers']" icon="building" tone="gold" hint="Registered employers" />
        <x-stat-card label="Pending jobs" :value="$stats['pending_jobs']" icon="clock" tone="warning" :href="route('admin.jobs.index', ['status' => 'pending'])" hint="Awaiting approval" />
        <x-stat-card label="Pending verifications" :value="$stats['pending_verifications']" icon="shield-check" tone="danger" :href="route('admin.verifications.index')" hint="Employer checks" />
        <x-stat-card label="Active jobs" :value="$stats['active_jobs']" icon="briefcase" tone="success" :href="route('admin.jobs.index', ['status' => 'approved'])" hint="Live listings" />
        <x-stat-card label="Applications" :value="$stats['total_applications']" icon="clipboard" tone="brand" hint="All time" />
        <x-stat-card label="Revenue" :value="'₦'.number_format((float) $stats['total_revenue'], 2)" icon="card" tone="success" hint="Successful payments" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="font-display text-base font-semibold text-brand-900">Pending job approvals</h2>
                    <p class="text-xs text-slate-500">Review new listings before they go live.</p>
                </div>
                <a href="{{ route('admin.jobs.index', ['status' => 'pending']) }}" class="text-sm font-semibold text-brand-700 hover:underline">View all</a>
            </div>

            @forelse($pendingJobs as $job)
                <div class="border-b border-slate-100 px-5 py-4 last:border-0">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div class="min-w-0">
                            <a href="{{ route('admin.jobs.show', $job) }}" class="font-semibold text-brand-900 hover:underline">{{ $job->title }}</a>
                            <p class="text-xs text-slate-500">{{ $job->employerProfile->company_name ?? $job->employerProfile->user->name ?? 'Employer' }} · {{ $job->created_at->diffForHumans() }}</p>
                        </div>
                        <x-badge :variant="$job->statusVariant()">{{ $job->statusLabel() }}</x-badge>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <form method="POST" action="{{ route('admin.jobs.approve', $job) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg bg-green-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-700">
                                <x-icon name="check" class="h-3.5 w-3.5" /> Approve
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.jobs.reject', $job) }}" data-confirm="Reject this job posting?">
                            @csrf
                            <input type="hidden" name="admin_notes" value="Does not meet requirements">
                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-red-200 px-3 py-1.5 text-xs font-semibold text-danger hover:bg-red-50">
                                <x-icon name="x" class="h-3.5 w-3.5" /> Reject
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-5">
                    <x-empty-state icon="check" title="No pending jobs" message="All job submissions have been reviewed." />
                </div>
            @endforelse
        </section>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="font-display text-base font-semibold text-brand-900">Pending verifications</h2>
                    <p class="text-xs text-slate-500">Employers waiting to be verified.</p>
                </div>
                <a href="{{ route('admin.verifications.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">View all</a>
            </div>

            @forelse($pendingVerifications as $verification)
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 last:border-0">
                    <div class="min-w-0">
                        <p class="font-semibold text-brand-900">{{ $verification->employerProfile->company_name ?? $verification->employerProfile->user->name ?? 'Employer' }}</p>
                        <p class="text-xs text-slate-500">{{ ucfirst($verification->document_type ?? 'Document') }} · submitted {{ $verification->created_at->diffForHumans() }}</p>
                    </div>
                    <a href="{{ route('admin.verifications.show', $verification) }}" class="text-sm font-semibold text-brand-700 hover:underline">Review</a>
                </div>
            @empty
                <div class="p-5">
                    <x-empty-state icon="shield-check" title="No pending verifications" message="Every employer verification has been processed." />
                </div>
            @endforelse
        </section>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="font-display text-base font-semibold text-brand-900">Recent users</h2>
                    <p class="text-xs text-slate-500">The latest accounts created.</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">View all</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse($recentUsers as $recentUser)
                    <li class="flex items-center justify-between gap-3 px-5 py-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-slate-800">{{ $recentUser->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $recentUser->email }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            <x-badge variant="neutral">{{ ucfirst(str_replace('_', ' ', $recentUser->role)) }}</x-badge>
                            @if($recentUser->is_suspended)
                                <x-badge variant="danger">Suspended</x-badge>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-4 text-sm text-slate-500">No users yet.</li>
                @endforelse
            </ul>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="font-display text-base font-semibold text-brand-900">Recent activity</h2>
                    <p class="text-xs text-slate-500">Audit log entries across the platform.</p>
                </div>
                <a href="{{ route('admin.audit-logs.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">View all</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse($recentAuditLogs as $log)
                    <li class="flex items-start justify-between gap-3 px-5 py-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-800">{{ ucfirst(str_replace(['.', '_'], [' ', ' '], $log->action)) }}</p>
                            <p class="text-xs text-slate-500">{{ $log->user->name ?? 'System' }} · {{ $log->created_at->diffForHumans() }}</p>
                        </div>
                        <a href="{{ route('admin.audit-logs.show', $log) }}" class="shrink-0 text-xs font-semibold text-brand-700 hover:underline">Details</a>
                    </li>
                @empty
                    <li class="px-5 py-4 text-sm text-slate-500">No audit activity recorded yet.</li>
                @endforelse
            </ul>
        </section>
    </div>
@endsection
