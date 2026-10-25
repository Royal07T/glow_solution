@extends('layouts.portal')

@section('title', 'Interviews')
@section('subtitle', 'Applications that have reached the interview stage.')

@section('content')
    @if($applications->isEmpty())
        <x-empty-state icon="calendar" title="No interviews yet" message="When an employer moves one of your applications to the interview stage, it will appear here. Keep your profile and CV up to date.">
            <a href="{{ route('applications.index') }}" class="inline-flex rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">View applications</a>
            <a href="{{ route('jobs.index') }}" class="inline-flex rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Find jobs</a>
        </x-empty-state>
    @else
        <div class="space-y-4">
            @foreach($applications as $application)
                <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="font-display text-lg font-semibold text-brand-900">{{ $application->job->title }}</h2>
                            <p class="text-sm text-slate-600">{{ $application->job->employerProfile->company_name ?? 'Employer' }} · {{ $application->job->location }}</p>
                        </div>
                        <x-badge :variant="$application->statusVariant()">{{ $application->statusLabel() }}</x-badge>
                    </div>

                    <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-3">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Format</dt>
                            <dd class="font-medium text-slate-800">Set by the employer</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Time and location</dt>
                            <dd class="font-medium text-slate-800">Contact the employer for details</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Last update</dt>
                            <dd class="font-medium text-slate-800">{{ $application->status_updated_at?->format('M j, Y g:ia') ?? '—' }}</dd>
                        </div>
                    </dl>

                    <p class="mt-3 rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-600">
                        Interview scheduling is arranged directly with the employer. Your application notes and CV are available on the application page.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('applications.show', $application) }}" class="inline-flex rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">View application</a>
                        <a href="{{ route('jobs.show', $application->job) }}" class="inline-flex rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">View job</a>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6">{{ $applications->links() }}</div>
    @endif
@endsection
