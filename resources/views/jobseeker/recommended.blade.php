@extends('layouts.portal')

@section('title', 'Recommended jobs')
@section('subtitle', 'Roles matched to your skills and career preferences.')

@section('content')
    <div class="space-y-6">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="font-display text-base font-semibold text-brand-900">How matching works</h2>
                    <p class="mt-1 max-w-2xl text-sm text-slate-600">
                        We rank live vacancies using the categories, locations, work type and skills saved on your profile.
                        Applications you have already submitted are excluded so every recommendation is actionable.
                    </p>
                </div>
                <a href="{{ route('jobseeker.profile.edit') }}" class="inline-flex shrink-0 items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <x-icon name="user" class="h-4 w-4" /> Refine preferences
                </a>
            </div>

            @if($completion['percent'] < 100)
                <div class="mt-4 rounded-lg border border-gold-200 bg-gold-50 px-4 py-3 text-sm text-gold-700">
                    Your profile is {{ $completion['percent'] }}% complete. Add more details to improve your matches.
                    <a href="{{ route('jobseeker.profile.edit') }}" class="font-semibold underline">Complete your profile</a>
                </div>
            @endif
        </section>

        @if($jobs->isEmpty())
            <x-empty-state icon="sparkles" title="No recommendations yet" message="There are no live roles matching your profile right now. Add a few skills or browse every open vacancy.">
                <a href="{{ route('jobseeker.profile.edit') }}" class="inline-flex rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Update preferences</a>
                <a href="{{ route('jobs.index') }}" class="inline-flex rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Browse all jobs</a>
            </x-empty-state>
        @else
            <div class="grid gap-4 xl:grid-cols-2">
                @foreach($jobs as $job)
                    <x-job-card :job="$job" :applied="in_array($job->id, $appliedJobIds)" :saved="in_array($job->id, $savedJobIds)" />
                @endforeach
            </div>

            <div>{{ $jobs->links() }}</div>
        @endif
    </div>
@endsection
