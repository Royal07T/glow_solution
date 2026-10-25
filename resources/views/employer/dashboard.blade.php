@extends('layouts.portal')

@section('title', 'Employer dashboard')
@section('subtitle', 'Overview of your hiring activity and job posts.')

@section('content')
    @if(! $profile || ! $profile->isVerified())
        <x-alert variant="warning" class="mb-6" title="Verification required">
            Your account must be verified before you can publish jobs.
            <a href="{{ route('employer.verification') }}" class="font-semibold underline">Submit verification documents</a>.
        </x-alert>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-stat-card label="Active jobs" :value="$stats['active_jobs']" icon="briefcase" tone="success" :href="route('employer.jobs.index')" />
        <x-stat-card label="Total applications" :value="$stats['total_applications']" icon="users" tone="brand" :href="route('employer.applications.index')" />
        <x-stat-card label="Awaiting review" :value="$stats['awaiting_review']" icon="clock" tone="warning" :href="route('employer.applications.index')" />
        <x-stat-card label="Shortlisted" :value="$stats['shortlisted']" icon="badge-check" tone="info" :href="route('employer.applications.index')" />
        <x-stat-card label="Interview stage" :value="$stats['interviews']" icon="calendar" tone="gold" :href="route('employer.applications.index')" />
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="font-display text-base font-semibold text-brand-900">Recent job posts</h2>
                        <p class="text-xs text-slate-500">Your five most recent listings.</p>
                    </div>
                    <a href="{{ route('employer.jobs.create') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-700 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                        <x-icon name="plus" class="h-4 w-4" /> Post a job
                    </a>
                </div>

                @if($jobs->isEmpty())
                    <div class="p-5">
                        <x-empty-state icon="briefcase" title="No jobs posted yet" message="Create your first listing to start receiving applications.">
                            <a href="{{ route('employer.jobs.create') }}" class="inline-flex rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Post a job</a>
                        </x-empty-state>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-sm">
                            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th scope="col" class="px-5 py-3 font-medium">Job</th>
                                    <th scope="col" class="px-5 py-3 font-medium">Status</th>
                                    <th scope="col" class="px-5 py-3 font-medium">Applications</th>
                                    <th scope="col" class="px-5 py-3 font-medium">Posted</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($jobs as $job)
                                    <tr class="hover:bg-slate-50/70">
                                        <td class="px-5 py-4">
                                            <a href="{{ route('employer.jobs.show', $job) }}" class="font-semibold text-brand-900 hover:underline">{{ $job->title }}</a>
                                            <p class="text-xs text-slate-500">{{ $job->location }}</p>
                                        </td>
                                        <td class="px-5 py-4"><x-badge :variant="$job->statusVariant()">{{ $job->statusLabel() }}</x-badge></td>
                                        <td class="px-5 py-4 text-slate-600">{{ $job->applications_count }}</td>
                                        <td class="px-5 py-4 text-slate-500">{{ $job->created_at->format('M j, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-slate-100 px-5 py-3">
                        <a href="{{ route('employer.jobs.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">View all jobs</a>
                    </div>
                @endif
            </section>

            <section class="mt-6 rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="font-display text-base font-semibold text-brand-900">Recent applicants</h2>
                        <p class="text-xs text-slate-500">The latest candidates who applied to your roles.</p>
                    </div>
                    <a href="{{ route('employer.applications.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">View all</a>
                </div>

                @if($recentApplications->isEmpty())
                    <div class="p-5">
                        <x-empty-state icon="users" title="No applications yet" message="Applications to your published jobs will appear here." />
                    </div>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach($recentApplications as $application)
                            <li class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">
                                <div class="min-w-0">
                                    <p class="font-semibold text-brand-900">{{ $application->jobSeeker->name ?? 'Applicant' }}</p>
                                    <p class="text-xs text-slate-500">{{ $application->job->title }} · applied {{ $application->applied_at?->diffForHumans() ?? $application->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <x-badge :variant="$application->statusVariant()">{{ $application->statusLabel() }}</x-badge>
                                    <a href="{{ route('employer.applications.show', $application) }}" class="text-sm font-semibold text-brand-700 hover:underline">Review</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Subscription</h2>
                @if($subscription)
                    <p class="mt-2 text-sm text-slate-600">
                        <span class="font-semibold text-brand-900">{{ $subscription->plan->name ?? 'Current plan' }}</span>
                        <x-badge variant="success" class="ml-1">Active</x-badge>
                    </p>
                    @if($subscription->ends_at)
                        <p class="mt-1 text-xs text-slate-500">Renews or ends {{ $subscription->ends_at->format('M j, Y') }}.</p>
                    @endif
                    <p class="mt-3 text-sm text-slate-600">Job listing allowance: <span class="font-semibold text-brand-900">{{ $jobLimit }}</span> active {{ \Illuminate\Support\Str::plural('listing', $jobLimit) }}.</p>
                    <a href="{{ route('billing.index') }}" class="mt-3 inline-flex rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Manage billing</a>
                @else
                    <p class="mt-2 text-sm text-slate-600">You do not have an active subscription. Choose a plan to publish jobs.</p>
                    <a href="{{ route('subscriptions.plans') }}" class="mt-3 inline-flex rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">View plans</a>
                @endif
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Quick actions</h2>
                <div class="mt-3 space-y-2">
                    <a href="{{ route('employer.jobs.create') }}" class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"><x-icon name="plus" class="h-4 w-4 text-brand-700" /> Post a new job</a>
                    <a href="{{ route('employer.applications.index') }}" class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"><x-icon name="users" class="h-4 w-4 text-brand-700" /> Review applications</a>
                    <a href="{{ route('employer.verification') }}" class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"><x-icon name="shield-check" class="h-4 w-4 text-brand-700" /> Verification</a>
                    <a href="{{ route('employer.profile.edit') }}" class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"><x-icon name="building" class="h-4 w-4 text-brand-700" /> Company profile</a>
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Company</h2>
                <p class="mt-2 font-medium text-slate-800">{{ $profile->company_name ?? $profile->contact_person ?? 'Not set' }}</p>
                <p class="text-sm text-slate-500">{{ $profile->city ?? '' }}{{ $profile->city && $profile->state ? ', ' : '' }}{{ $profile->state ?? '' }}</p>
                @if($profile?->isVerified())
                    <p class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-green-700"><x-icon name="badge-check" class="h-4 w-4" /> Verified employer</p>
                @endif
            </section>
        </aside>
    </div>
@endsection
