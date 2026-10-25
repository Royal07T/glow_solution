@extends('layouts.app')

@section('title', $job->title)
@section('description', \Illuminate\Support\Str::limit(strip_tags($job->description), 155))

@section('content')
    @php
        $company = $job->employerProfile->company_name ?? $job->employerProfile->contact_person ?? 'Glow employer';
        $employment = ucfirst(str_replace('_', ' ', $job->employment_type ?? ''));
        $salary = null;
        if ($job->salary_min || $job->salary_max) {
            $salary = trim(($job->salary_currency ?? 'NGN').' '.($job->salary_min ? number_format((float) $job->salary_min) : '').($job->salary_min && $job->salary_max ? ' – ' : '').($job->salary_max ? number_format((float) $job->salary_max) : ''));
        }
        $posted = $job->published_at ?? $job->created_at;
        $canApply = ! $deadlinePassed && ! $applied;
    @endphp

    <div class="bg-brand-800">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <nav aria-label="Breadcrumb" class="text-sm text-brand-200">
                <a href="{{ route('jobs.index') }}" class="hover:text-white">Jobs</a>
                <span aria-hidden="true" class="mx-2">/</span>
                <span class="text-white">{{ $job->title }}</span>
            </nav>

            <div class="mt-4 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-badge :variant="$job->type === 'domestic' ? 'gold' : 'brand'">{{ ucfirst($job->type ?? 'Role') }}</x-badge>
                        @if($job->category)
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-brand-100">{{ $job->category->name }}</span>
                        @endif
                        @if($employment)
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-brand-100">{{ $employment }}</span>
                        @endif
                    </div>
                    <h1 class="mt-3 font-display text-3xl font-bold text-white sm:text-4xl">{{ $job->title }}</h1>
                    <p class="mt-2 text-lg font-medium text-gold-300">{{ $company }}</p>
                    <ul class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-brand-100">
                        <li class="inline-flex items-center gap-1.5"><x-icon name="map-pin" class="h-4 w-4" />{{ $job->location }}</li>
                        @if($posted)
                            <li class="inline-flex items-center gap-1.5"><x-icon name="clock" class="h-4 w-4" />Posted {{ $posted->diffForHumans() }}</li>
                        @endif
                        @if($job->positions_available)
                            <li class="inline-flex items-center gap-1.5"><x-icon name="users" class="h-4 w-4" />{{ $job->positions_available }} {{ \Illuminate\Support\Str::plural('position', $job->positions_available) }}</li>
                        @endif
                    </ul>
                </div>

                <div class="flex shrink-0 flex-wrap items-center gap-2">
                    @auth
                        @if(auth()->user()->isJobSeeker())
                            <form method="POST" action="{{ route('jobs.save', $job) }}">
                                @csrf
                                <button type="submit" aria-pressed="{{ $saved ? 'true' : 'false' }}" class="inline-flex min-h-[44px] items-center gap-2 rounded-lg border px-4 py-2.5 text-sm font-semibold transition {{ $saved ? 'border-gold-300 bg-gold-50 text-gold-700' : 'border-white/30 text-white hover:bg-white/10' }}">
                                    <x-icon name="bookmark" class="h-4 w-4" /> {{ $saved ? 'Saved' : 'Save job' }}
                                </button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-display text-lg font-semibold text-brand-900">Job description</h2>
                    <div class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $job->description }}</div>

                    @if($job->responsibilities)
                        <h2 class="mt-6 font-display text-lg font-semibold text-brand-900">Responsibilities</h2>
                        <div class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $job->responsibilities }}</div>
                    @endif

                    @if($job->qualifications)
                        <h2 class="mt-6 font-display text-lg font-semibold text-brand-900">Qualifications</h2>
                        <div class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $job->qualifications }}</div>
                    @endif

                    @if($job->benefits)
                        <h2 class="mt-6 font-display text-lg font-semibold text-brand-900">Benefits</h2>
                        <div class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $job->benefits }}</div>
                    @endif
                </section>

                @if($relatedJobs->isNotEmpty())
                    <section>
                        <h2 class="mb-3 font-display text-lg font-semibold text-brand-900">Similar jobs</h2>
                        <div class="grid gap-4 sm:grid-cols-2">
                            @foreach($relatedJobs as $related)
                                <x-job-card :job="$related" />
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            <aside class="space-y-4">
                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="font-display text-base font-semibold text-brand-900">Job overview</h2>
                    <dl class="mt-3 space-y-3 text-sm">
                        @if($salary)
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-slate-500">Salary</dt>
                                <dd class="font-semibold text-green-700">{{ $salary }}{{ $job->salary_negotiable ? ' (negotiable)' : '' }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Location</dt>
                            <dd class="font-medium text-slate-800">{{ $job->location }}</dd>
                        </div>
                        @if($employment)
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-slate-500">Employment type</dt>
                                <dd class="font-medium text-slate-800">{{ $employment }}</dd>
                            </div>
                        @endif
                        @if($job->application_deadline)
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-slate-500">Application deadline</dt>
                                <dd class="font-medium {{ $deadlinePassed ? 'text-danger' : 'text-slate-800' }}">
                                    {{ $job->application_deadline->format('M j, Y') }}
                                    @if($deadlinePassed) · Closed @endif
                                </dd>
                            </div>
                        @endif
                    </dl>

                    <div class="mt-5 border-t border-slate-100 pt-4">
                        @auth
                            @if(auth()->user()->isJobSeeker())
                                @if($applied)
                                    <a href="{{ route('applications.index') }}" class="inline-flex w-full min-h-[44px] items-center justify-center gap-2 rounded-lg bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200">
                                        <x-icon name="check" class="h-4 w-4" /> Application submitted
                                    </a>
                                @elseif($deadlinePassed)
                                    <p class="rounded-lg bg-red-50 px-3 py-2 text-center text-sm font-medium text-danger">Applications are closed for this role.</p>
                                @else
                                    <a href="{{ route('applications.create', $job) }}" class="inline-flex w-full min-h-[44px] items-center justify-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">
                                        Apply now <x-icon name="arrow-right" class="h-4 w-4" />
                                    </a>
                                @endif
                            @else
                                <p class="rounded-lg bg-slate-50 px-3 py-2 text-center text-sm text-slate-600">Only job seekers can apply for roles.</p>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="inline-flex w-full min-h-[44px] items-center justify-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">
                                Sign in to apply
                            </a>
                        @endauth
                    </div>
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="font-display text-base font-semibold text-brand-900">About the employer</h2>
                    <p class="mt-2 font-medium text-slate-800">{{ $company }}</p>
                    @if($job->employerProfile?->description)
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ \Illuminate\Support\Str::limit($job->employerProfile->description, 260) }}</p>
                    @endif
                    <p class="mt-3 flex items-center gap-1.5 text-xs text-slate-500">
                        <x-icon name="shield-check" class="h-4 w-4 text-green-600" /> Listed on Glow Recruitment
                    </p>
                </section>
            </aside>
        </div>
    </div>
@endsection
