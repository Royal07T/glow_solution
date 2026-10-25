@extends('layouts.portal')

@section('title', 'Saved jobs')
@section('subtitle', 'Roles you bookmarked for later.')

@section('content')
    @if($savedJobs->isEmpty())
        <x-empty-state icon="bookmark" title="No saved jobs" message="Tap the save button on any listing to keep it here for later.">
            <a href="{{ route('jobs.index') }}" class="inline-flex rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Browse jobs</a>
        </x-empty-state>
    @else
        <div class="space-y-4">
            @foreach($savedJobs as $job)
                @php
                    $applied = in_array($job->id, $appliedJobIds);
                @endphp
                <article class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('jobs.show', $job) }}" class="font-display text-lg font-semibold text-brand-900 hover:underline">{{ $job->title }}</a>
                            <x-badge :variant="$job->type === 'domestic' ? 'gold' : 'brand'">{{ ucfirst($job->type ?? 'role') }}</x-badge>
                        </div>
                        <p class="text-sm text-slate-600">{{ $job->employerProfile->company_name ?? $job->employerProfile->contact_person ?? 'Employer' }}</p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $job->location }}
                            @if($job->employment_type) · {{ ucfirst(str_replace('_', ' ', $job->employment_type)) }} @endif
                            @if($job->application_deadline) · Apply by {{ $job->application_deadline->format('M j, Y') }} @endif
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if($applied)
                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600">
                                <x-icon name="check" class="h-4 w-4" /> Applied
                            </span>
                        @else
                            <a href="{{ route('applications.create', $job) }}" class="inline-flex rounded-lg bg-brand-700 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-800">Apply now</a>
                        @endif

                        <form method="POST" action="{{ route('saved-jobs.destroy', $job) }}" data-confirm="Remove this job from your saved list?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">
                                <x-icon name="trash" class="h-4 w-4" /> Remove
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6">{{ $savedJobs->links() }}</div>
    @endif
@endsection
