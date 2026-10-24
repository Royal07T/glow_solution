@extends('layouts.portal')

@section('title', 'Saved jobs')
@section('subtitle', 'Roles you bookmarked for later.')

@section('content')
    @if($savedJobs->isEmpty())
        <x-empty-state icon="bookmark" title="No saved jobs" message="Tap the save button on any listing to keep it here.">
            <a href="{{ route('jobs.index') }}" class="inline-flex rounded-lg bg-brand-800 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Browse jobs</a>
        </x-empty-state>
    @else
        <div class="space-y-4">
            @foreach($savedJobs as $job)
                <article class="flex flex-wrap items-start justify-between gap-4 rounded-xl border border-stone-200 bg-white p-5">
                    <div class="min-w-0">
                        <a href="{{ route('jobs.show', $job) }}" class="font-display text-lg font-semibold text-slate-900 hover:text-brand-800">{{ $job->title }}</a>
                        <p class="text-sm text-slate-600">{{ $job->employerProfile->company_name ?? 'Employer' }}</p>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $job->location }}
                            @if($job->category) · {{ $job->category->name }} @endif
                            · Saved {{ $job->pivot->created_at?->diffForHumans() }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-badge variant="brand">{{ ucfirst($job->type) }}</x-badge>
                        <form method="POST" action="{{ route('saved-jobs.destroy', $job) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-danger">Remove</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6">{{ $savedJobs->links() }}</div>
    @endif
@endsection
