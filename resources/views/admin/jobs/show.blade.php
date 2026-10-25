@extends('layouts.portal')

@section('title', 'Review listing')
@section('subtitle', $job->title)

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-xl font-semibold text-slate-900">{{ $job->title }}</h2>
                        <p class="text-sm text-slate-600">{{ $job->employerProfile->company_name ?? $job->employerProfile->user->name ?? 'Employer' }} · {{ $job->location }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-badge :variant="$job->statusVariant()">{{ $job->statusLabel() }}</x-badge>
                        <a href="{{ route('admin.jobs.edit', $job) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit listing</a>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    <x-badge variant="brand">{{ ucfirst($job->type) }}</x-badge>
                    <x-badge variant="neutral">{{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</x-badge>
                    @if($job->salary_min)
                        <x-badge variant="gold">{{ $job->salary_currency }} {{ number_format((float) $job->salary_min) }}{{ $job->salary_max ? ' - '.number_format((float) $job->salary_max) : '' }}</x-badge>
                    @endif
                </div>

                <div class="mt-6 space-y-5 text-sm text-slate-700">
                    <div>
                        <h3 class="font-display text-base font-semibold text-slate-900">Description</h3>
                        <p class="mt-1 whitespace-pre-line">{{ $job->description }}</p>
                    </div>
                    @if($job->responsibilities)
                        <div>
                            <h3 class="font-display text-base font-semibold text-slate-900">Responsibilities</h3>
                            <p class="mt-1 whitespace-pre-line">{{ $job->responsibilities }}</p>
                        </div>
                    @endif
                    @if($job->qualifications)
                        <div>
                            <h3 class="font-display text-base font-semibold text-slate-900">Qualifications</h3>
                            <p class="mt-1 whitespace-pre-line">{{ $job->qualifications }}</p>
                        </div>
                    @endif
                    @if($job->admin_notes)
                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Previous moderation note</p>
                            <p class="mt-1">{{ $job->admin_notes }}</p>
                        </div>
                    @endif
                </div>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="font-display text-base font-semibold text-slate-900">Applications</h2>
                        <p class="text-sm text-slate-600">{{ $job->applications_count }} candidate{{ $job->applications_count === 1 ? '' : 's' }} have applied.</p>
                    </div>
                    @if($job->applications_count > 0)
                        <a href="{{ route('admin.applications.index', ['job' => $job->id]) }}" class="text-sm font-semibold text-brand-700 hover:underline">View all applications</a>
                    @endif
                </div>

                @if($applications->isEmpty())
                    <p class="mt-4 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-600">No applications have been submitted for this listing yet.</p>
                @else
                    <div class="mt-4 divide-y divide-slate-100">
                        @foreach($applications as $application)
                            <div class="flex flex-wrap items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                                <div class="min-w-0">
                                    <a href="{{ route('admin.applications.show', $application) }}" class="font-semibold text-brand-900 hover:underline">{{ $application->jobSeeker->name }}</a>
                                    <p class="truncate text-xs text-slate-500">{{ $application->jobSeeker->email }} · Applied {{ ($application->applied_at ?? $application->created_at)->format('M j, Y') }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <x-badge :variant="$application->statusVariant()">{{ $application->statusLabel() }}</x-badge>
                                    <a href="{{ route('admin.applications.show', $application) }}" class="text-sm font-semibold text-brand-700 hover:underline">Review</a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">{{ $applications->links() }}</div>
                @endif
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="font-display text-base font-semibold text-slate-900">Moderation decision</h3>
                <form method="POST" action="{{ route('admin.jobs.approve', $job) }}" class="mt-3 space-y-2">
                    @csrf
                    <button type="submit" class="w-full rounded-lg bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">Approve listing</button>
                </form>
                <form method="POST" action="{{ route('admin.jobs.reject', $job) }}" class="mt-4 space-y-2">
                    @csrf
                    <label for="admin_notes" class="block text-sm font-medium text-slate-700">Rejection reason</label>
                    <textarea id="admin_notes" name="admin_notes" rows="3" required maxlength="1000" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
                    <button type="submit" data-confirm="Reject this listing?" class="w-full rounded-lg bg-danger px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Reject listing</button>
                </form>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="font-display text-base font-semibold text-slate-900">Employer</h3>
                <a href="{{ route('admin.users.show', $job->employerProfile->user) }}" class="mt-2 block text-sm font-semibold text-brand-700 hover:underline">
                    {{ $job->employerProfile->user->name }}
                </a>
                <p class="text-xs text-slate-500">{{ $job->employerProfile->user->email }}</p>
                <p class="mt-2 text-xs text-slate-500">Verification: {{ ucfirst($job->employerProfile->verification_status) }}</p>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5">
                <h3 class="font-display text-base font-semibold text-slate-900">Applications</h3>
                <p class="mt-2 text-sm text-slate-600">{{ $job->applications_count }} candidate{{ $job->applications_count === 1 ? '' : 's' }} have applied.</p>
                @if($job->applications_count > 0)
                    <a href="{{ route('admin.applications.index', ['job' => $job->id]) }}" class="mt-3 inline-flex w-full items-center justify-center rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">View applicants</a>
                @endif
            </section>

            <a href="{{ route('admin.jobs.index') }}" class="inline-flex w-full items-center justify-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Back to moderation</a>
        </aside>
    </div>
@endsection
