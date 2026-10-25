@extends('layouts.portal')

@section('title', 'Application details')
@section('subtitle', $application->job->title)

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-xl font-semibold text-brand-900">{{ $application->job->title }}</h2>
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
                                <a href="{{ route('cvs.download', $application->cv) }}" class="font-semibold text-brand-700 hover:underline">{{ $application->cv->name }}</a>
                            @else
                                None
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Employment</dt>
                        <dd class="text-sm font-medium text-slate-800">{{ ucfirst(str_replace('_', ' ', $application->job->employment_type ?? 'Not specified')) }}</dd>
                    </div>
                    @if($application->job->application_deadline)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Applications close</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $application->job->application_deadline->format('M j, Y') }}</dd>
                        </div>
                    @endif
                    @if($application->job->salary_min || $application->job->salary_max)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Salary</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $application->job->salary_currency }} {{ number_format((float) $application->job->salary_min) }}{{ $application->job->salary_max ? ' – '.number_format((float) $application->job->salary_max) : '' }}</dd>
                        </div>
                    @endif
                </dl>

                @if($application->status === \App\Enums\ApplicationStatus::INTERVIEW)
                    <div class="mt-5 rounded-lg border border-gold-200 bg-gold-50 px-4 py-3 text-sm text-gold-700">
                        Your application has reached the interview stage. The employer will contact you directly with the interview arrangements.
                    </div>
                @endif

                @if($application->cover_letter)
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <h3 class="font-display text-base font-semibold text-brand-900">Your cover letter</h3>
                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $application->cover_letter }}</p>
                    </div>
                @endif

                @if($application->employer_notes)
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <h3 class="font-display text-base font-semibold text-brand-900">Message from the employer</h3>
                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $application->employer_notes }}</p>
                    </div>
                @endif

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('jobs.show', $application->job) }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">View job</a>
                    <a href="{{ route('applications.index') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-100">Back to applications</a>
                    @if($application->canBeWithdrawn())
                        <form method="POST" action="{{ route('applications.withdraw', $application) }}" data-confirm="Withdraw this application? This cannot be undone.">
                            @csrf
                            <button type="submit" class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-danger hover:bg-red-50">Withdraw application</button>
                        </form>
                    @endif
                </div>
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-display text-base font-semibold text-brand-900">Application progress</h3>
                @php
                    $steps = [
                        ['label' => 'Submitted', 'status' => \App\Enums\ApplicationStatus::SUBMITTED],
                        ['label' => 'Under review', 'status' => \App\Enums\ApplicationStatus::UNDER_REVIEW],
                        ['label' => 'Shortlisted', 'status' => \App\Enums\ApplicationStatus::SHORTLISTED],
                        ['label' => 'Interview', 'status' => \App\Enums\ApplicationStatus::INTERVIEW],
                        ['label' => 'Decision', 'status' => \App\Enums\ApplicationStatus::ACCEPTED],
                    ];
                    $order = array_search($application->status, array_column($steps, 'status'), true);
                    $isClosed = in_array($application->status, [\App\Enums\ApplicationStatus::REJECTED, \App\Enums\ApplicationStatus::WITHDRAWN], true);
                @endphp
                <ol class="mt-3 space-y-3 text-sm">
                    @foreach($steps as $index => $step)
                        @php
                            $reached = $order !== false && $index <= $order;
                        @endphp
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full {{ $reached ? 'bg-brand-700 text-white' : 'bg-slate-100 text-slate-400' }}" aria-hidden="true">
                                @if($reached)
                                    <x-icon name="check" class="h-3 w-3" />
                                @else
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                @endif
                            </span>
                            <span class="{{ $reached ? 'font-medium text-slate-800' : 'text-slate-500' }}">{{ $step['label'] }}</span>
                        </li>
                    @endforeach
                </ol>
                @if($isClosed)
                    <p class="mt-3 rounded-lg bg-slate-50 px-3 py-2 text-xs text-slate-600">
                        This application is {{ $application->status->label() === 'Withdrawn' ? 'withdrawn' : 'closed' }}.
                    </p>
                @endif
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-display text-base font-semibold text-brand-900">What happens next</h3>
                <p class="mt-2 text-sm text-slate-600">Employers review applications and update the status here. Check back regularly and keep your contact details current.</p>
                <a href="{{ route('jobseeker.interviews') }}" class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-brand-700 hover:underline">
                    <x-icon name="calendar" class="h-4 w-4" /> Interview-stage applications
                </a>
            </section>
        </aside>
    </div>
@endsection
