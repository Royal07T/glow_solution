@extends('layouts.portal')

@section('title', 'Application details')
@section('subtitle', $application->job->title)

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-stone-200 bg-white p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-xl font-semibold text-slate-900">{{ $application->job->title }}</h2>
                        <p class="text-sm text-slate-600">{{ $application->job->employerProfile->company_name ?? 'Employer' }} · {{ $application->job->location }}</p>
                    </div>
                    <x-badge :variant="$application->statusVariant()">{{ $application->statusLabel() }}</x-badge>
                </div>

                <dl class="mt-5 grid gap-3 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Applied</dt>
                        <dd class="text-sm font-medium text-slate-800">{{ ($application->applied_at ?? $application->created_at)->format('M j, Y g:ia') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Last update</dt>
                        <dd class="text-sm font-medium text-slate-800">{{ $application->status_updated_at?->diffForHumans() ?? 'No updates yet' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">CV attached</dt>
                        <dd class="text-sm font-medium text-slate-800">
                            @if($application->cv)
                                <a href="{{ route('cvs.download', $application->cv) }}" class="text-[#062B52 hover:underline">{{ $application->cv->name }}</a>
                            @else
                                None
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Employment</dt>
                        <dd class="text-sm font-medium text-slate-800">{{ ucfirst(str_replace('_', ' ', $application->job->employment_type)) }}</dd>
                    </div>
                </dl>

                @if($application->cover_letter)
                    <div class="mt-6 border-t border-stone-200 pt-5">
                        <h3 class="font-display text-base font-semibold text-slate-900">Your cover letter</h3>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $application->cover_letter }}</p>
                    </div>
                @endif

                @if($application->employer_notes)
                    <div class="mt-6 border-t border-stone-200 pt-5">
                        <h3 class="font-display text-base font-semibold text-slate-900">Message from the employer</h3>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $application->employer_notes }}</p>
                    </div>
                @endif

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('jobs.show', $application->job) }}" class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-stone-50">View job</a>
                    <a href="{{ route('applications.index') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-stone-100">Back to applications</a>
                    @if($application->canBeWithdrawn())
                        <form method="POST" action="{{ route('applications.withdraw', $application) }}">
                            @csrf
                            <button type="submit" data-confirm="Withdraw this application?" class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-danger hover:bg-red-50">Withdraw application</button>
                        </form>
                    @endif
                </div>
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-stone-200 bg-white p-5">
                <h3 class="font-display text-base font-semibold text-slate-900">What happens next</h3>
                <ol class="mt-3 space-y-3 text-sm text-slate-600">
                    <li><span class="font-semibold text-slate-800">Submitted</span> — the employer reviews your profile and CV.</li>
                    <li><span class="font-semibold text-slate-800">Shortlisted</span> — you may be invited to an interview.</li>
                    <li><span class="font-semibold text-slate-800">Decision</span> — the employer updates the status here.</li>
                </ol>
            </section>
        </aside>
    </div>
@endsection
