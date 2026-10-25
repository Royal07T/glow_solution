@extends('layouts.portal')

@section('title', 'Job details')
@section('subtitle', $job->title)

@section('content')
    <div class="space-y-6">
        <section class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="font-display text-xl font-semibold text-slate-900">{{ $job->title }}</h2>
                    <p class="text-sm text-slate-600">{{ $job->location }}{{ $job->city ? ' · '.$job->city : '' }}{{ $job->state ? ', '.$job->state : '' }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <x-badge :variant="$job->statusVariant()">{{ $job->statusLabel() }}</x-badge>
                    <a href="{{ route('employer.jobs.edit', $job) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit listing</a>
                </div>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <x-badge variant="brand">{{ ucfirst($job->type) }}</x-badge>
                <x-badge variant="neutral">{{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</x-badge>
                @if($job->category)
                    <x-badge variant="info">{{ $job->category->name }}</x-badge>
                @endif
                @if($job->salary_min)
                    <x-badge variant="gold">{{ $job->salary_currency }} {{ number_format((float) $job->salary_min) }}{{ $job->salary_max ? ' - '.number_format((float) $job->salary_max) : '' }}</x-badge>
                @endif
            </div>

            @if($job->status === 'rejected' && $job->admin_notes)
                <div class="mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-semibold">Changes required</p>
                    <p class="mt-1">{{ $job->admin_notes }}</p>
                </div>
            @elseif($job->status === 'pending')
                <div class="mt-5 rounded-lg border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-yellow-900">
                    This listing is awaiting administrator review. You can still edit it before it is approved.
                </div>
            @endif

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
                @if($job->benefits)
                    <div>
                        <h3 class="font-display text-base font-semibold text-slate-900">Benefits</h3>
                        <p class="mt-1 whitespace-pre-line">{{ $job->benefits }}</p>
                    </div>
                @endif
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="font-display text-base font-semibold text-slate-900">Applicants</h2>
                    <p class="text-sm text-slate-600">{{ $job->applications_count }} candidate{{ $job->applications_count === 1 ? '' : 's' }} have applied.</p>
                </div>
                @if($job->applications_count > 0)
                    <a href="{{ route('employer.applications.index', ['job' => $job->id]) }}" class="text-sm font-semibold text-brand-700 hover:underline">View all applicants</a>
                @endif
            </div>

            @if($recentApplications->isEmpty())
                <p class="mt-4 rounded-lg border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm text-slate-600">No applications have been submitted for this listing yet.</p>
            @else
                <div class="mt-4 divide-y divide-slate-100">
                    @foreach($recentApplications as $application)
                        <div class="flex flex-wrap items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-900">{{ $application->jobSeeker->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $application->jobSeeker->email }} · Applied {{ ($application->applied_at ?? $application->created_at)->format('M j, Y') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <x-badge :variant="$application->statusVariant()">{{ $application->statusLabel() }}</x-badge>
                                <a href="{{ route('employer.applications.show', $application) }}" class="text-sm font-semibold text-brand-700 hover:underline">Review</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('employer.jobs.index') }}" class="inline-flex items-center justify-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Back to my jobs</a>
            <div class="flex flex-wrap gap-2">
                @if($job->isApproved() && ! $job->isExpired())
                    <a href="{{ route('jobs.show', $job) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">View public listing</a>
                @endif
                <a href="{{ route('employer.jobs.edit', $job) }}" class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Edit job</a>
                @if($job->status !== 'approved')
                    <form method="POST" action="{{ route('employer.jobs.destroy', $job) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" data-confirm="Delete this job listing?" class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-danger hover:bg-red-50">Delete</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
