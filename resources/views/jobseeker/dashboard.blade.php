@extends('layouts.portal')

@section('title', 'Dashboard')
@section('subtitle', 'Your job search at a glance.')

@section('content')
    @php
        $firstName = $user->first_name ?: \Illuminate\Support\Str::before($user->name, ' ');
        $initials = \Illuminate\Support\Str::of($user->name)->trim()->explode(' ')->filter()->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode('');
        $hour = (int) now()->format('G');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
        $subtitle = $profile && $profile->bio
            ? 'Keep the momentum going — your next opportunity is closer than you think.'
            : 'Complete your profile and let employers see what makes you exceptional.';
    @endphp

    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-6 p-6 sm:flex-row sm:items-center sm:justify-between sm:p-8">
                <div class="flex items-center gap-4">
                    <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-brand-700 font-display text-xl font-bold text-white" aria-hidden="true">
                        {{ $initials ?: 'G' }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gold-700">{{ $greeting }}</p>
                        <h2 class="mt-0.5 font-display text-2xl font-bold text-brand-900 sm:text-3xl">{{ $firstName }}</h2>
                        <p class="mt-1 max-w-xl text-sm text-slate-600">{{ $subtitle }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('jobs.index') }}" class="inline-flex min-h-[44px] items-center gap-2 rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800">
                        <x-icon name="search" class="h-4 w-4" /> Find jobs
                    </a>
                    <a href="{{ route('jobseeker.profile.edit') }}" class="inline-flex min-h-[44px] items-center gap-2 rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                        <x-icon name="user" class="h-4 w-4" /> Update profile
                    </a>
                </div>
            </div>
        </section>

        <section class="rounded-2xl bg-brand-700 p-5 text-white shadow-sm sm:p-6" aria-labelledby="job-search-heading">
            <div class="flex flex-wrap items-end justify-between gap-2">
                <div>
                    <h2 id="job-search-heading" class="font-display text-lg font-semibold">Find your next role</h2>
                    <p class="mt-0.5 text-sm text-white/70">Search live vacancies across domestic and professional roles.</p>
                </div>
                <a href="{{ route('jobseeker.recommended') }}" class="text-sm font-semibold text-gold-200 hover:text-gold-100">See recommendations →</a>
            </div>

            <form method="GET" action="{{ route('jobs.index') }}" class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                <div class="xl:col-span-2">
                    <label for="keyword" class="mb-1 block text-xs font-medium text-white/80">Keywords or job title</label>
                    <input id="keyword" name="keyword" type="search" placeholder="e.g. housekeeper, driver, accountant" class="w-full rounded-lg border border-white/20 bg-white/10 px-3 py-2.5 text-sm text-white placeholder:text-white/50 focus:border-gold-400 focus:outline-none focus:ring-2 focus:ring-gold-400/40">
                </div>
                <div>
                    <label for="location" class="mb-1 block text-xs font-medium text-white/80">Location</label>
                    <input id="location" name="location" type="text" placeholder="City or state" class="w-full rounded-lg border border-white/20 bg-white/10 px-3 py-2.5 text-sm text-white placeholder:text-white/50 focus:border-gold-400 focus:outline-none focus:ring-2 focus:ring-gold-400/40">
                </div>
                <div>
                    <label for="employment_type" class="mb-1 block text-xs font-medium text-white/80">Employment type</label>
                    <select id="employment_type" name="employment_type" class="w-full rounded-lg border border-white/20 bg-white/10 px-3 py-2.5 text-sm text-white focus:border-gold-400 focus:outline-none focus:ring-2 focus:ring-gold-400/40">
                        <option value="" class="text-slate-900">Any type</option>
                        @foreach(['full_time' => 'Full time', 'part_time' => 'Part time', 'contract' => 'Contract', 'temporary' => 'Temporary', 'live_in' => 'Live in', 'live_out' => 'Live out'] as $value => $label)
                            <option value="{{ $value }}" class="text-slate-900">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-lg bg-gold-500 px-4 py-2.5 text-sm font-bold text-brand-900 transition hover:bg-gold-400">
                        <x-icon name="search" class="h-4 w-4" /> Search jobs
                    </button>
                </div>
            </form>
        </section>

        <section aria-label="Your job search summary" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Total applications" :value="$stats['total_applications']" icon="send" :href="route('applications.index')" hint="All applications you have submitted" />
            <x-stat-card label="Under review" :value="$stats['under_review']" icon="eye" tone="warning" :href="route('applications.index', ['status' => 'under_review'])" hint="Employers are reviewing these" />
            <x-stat-card label="Interview stage" :value="$stats['interviews']" icon="calendar" tone="info" :href="route('jobseeker.interviews')" hint="Applications moved to interview" />
            <x-stat-card label="Saved jobs" :value="$stats['saved_jobs']" icon="bookmark" tone="gold" :href="route('saved-jobs.index')" hint="Roles you bookmarked" />
        </section>

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <section aria-labelledby="recommended-heading">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <h2 id="recommended-heading" class="font-display text-lg font-semibold text-brand-900">Recommended for you</h2>
                            <p class="text-sm text-slate-600">Matched from your skills, preferred categories and locations.</p>
                        </div>
                        <a href="{{ route('jobseeker.recommended') }}" class="text-sm font-semibold text-brand-700 hover:underline">View all</a>
                    </div>

                    @if($recommended->isEmpty())
                        <x-empty-state icon="sparkles" title="No recommendations yet" message="Add skills and career preferences to your profile, or browse all live jobs.">
                            <a href="{{ route('jobseeker.profile.edit') }}" class="inline-flex rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Update profile</a>
                            <a href="{{ route('jobs.index') }}" class="inline-flex rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Browse jobs</a>
                        </x-empty-state>
                    @else
                        <div class="grid gap-4 xl:grid-cols-2">
                            @foreach($recommended as $job)
                                <x-job-card :job="$job" :applied="in_array($job->id, $appliedJobIds)" :saved="in_array($job->id, $savedJobIds)" />
                            @endforeach
                        </div>
                    @endif
                </section>

                <section class="rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="recent-applications-heading">
                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4">
                        <h2 id="recent-applications-heading" class="font-display text-base font-semibold text-brand-900">Recent applications</h2>
                        <a href="{{ route('applications.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">View all applications</a>
                    </div>

                    @if($applications->isEmpty())
                        <div class="p-5">
                            <x-empty-state icon="clipboard" title="No applications yet" message="When you apply for a role it will appear here with its latest status.">
                                <a href="{{ route('jobs.index') }}" class="inline-flex rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Find jobs</a>
                            </x-empty-state>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 text-sm">
                                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th scope="col" class="px-5 py-3 font-medium">Role</th>
                                        <th scope="col" class="px-5 py-3 font-medium">Employer</th>
                                        <th scope="col" class="px-5 py-3 font-medium">Applied</th>
                                        <th scope="col" class="px-5 py-3 font-medium">Status</th>
                                        <th scope="col" class="px-5 py-3 text-right font-medium">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($applications as $application)
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-5 py-3 font-semibold text-brand-900">{{ $application->job->title }}</td>
                                            <td class="px-5 py-3 text-slate-600">{{ $application->job->employerProfile->company_name ?? 'Employer' }}</td>
                                            <td class="px-5 py-3 text-slate-500">{{ ($application->applied_at ?? $application->created_at)->format('M j, Y') }}</td>
                                            <td class="px-5 py-3"><x-badge :variant="$application->statusVariant()">{{ $application->statusLabel() }}</x-badge></td>
                                            <td class="px-5 py-3 text-right">
                                                <a href="{{ route('applications.show', $application) }}" class="font-semibold text-brand-700 hover:underline">View details</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            </div>

            <aside class="space-y-6">
                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="completion-heading">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 id="completion-heading" class="font-display text-base font-semibold text-brand-900">Profile completion</h2>
                            <p class="mt-0.5 text-sm text-slate-600">{{ $completion['completed'] }} of {{ $completion['total'] }} sections complete</p>
                        </div>
                        <span class="font-display text-2xl font-bold text-brand-900">{{ $completion['percent'] }}%</span>
                    </div>

                    <div class="mt-3 h-2.5 w-full overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-valuenow="{{ $completion['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="Profile completion">
                        <div class="h-full rounded-full bg-gold-500 transition-all" style="width: {{ $completion['percent'] }}%"></div>
                    </div>

                    @if(empty($completion['missing']))
                        <p class="mt-3 flex items-center gap-2 text-sm font-medium text-green-700">
                            <x-icon name="badge-check" class="h-4 w-4" /> Your profile is complete.
                        </p>
                    @else
                        <ul class="mt-4 space-y-2">
                            @foreach(array_slice($completion['missing'], 0, 4) as $item)
                                <li>
                                    <a href="{{ $item['url'] }}" class="group flex items-start gap-2 rounded-lg p-1 text-sm text-slate-600 hover:bg-slate-50">
                                        <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full border border-slate-300 text-slate-400 group-hover:border-gold-500 group-hover:text-gold-600" aria-hidden="true">
                                            <x-icon name="plus" class="h-3 w-3" />
                                        </span>
                                        <span>
                                            <span class="font-medium text-slate-800">{{ $item['label'] }}</span>
                                            <span class="block text-xs text-slate-500">{{ $item['message'] }}</span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('jobseeker.profile.edit') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Complete profile</a>
                    @endif
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="interviews-heading">
                    <div class="flex items-center justify-between gap-2">
                        <h2 id="interviews-heading" class="font-display text-base font-semibold text-brand-900">Interviews</h2>
                        <a href="{{ route('jobseeker.interviews') }}" class="text-sm font-semibold text-brand-700 hover:underline">View all</a>
                    </div>

                    @if($interviews->isEmpty())
                        <p class="mt-3 text-sm text-slate-600">No interviews yet. When an employer moves your application to the interview stage, it will appear here.</p>
                    @else
                        <ul class="mt-3 space-y-3">
                            @foreach($interviews as $application)
                                <li class="rounded-lg border border-slate-100 p-3">
                                    <a href="{{ route('applications.show', $application) }}" class="font-semibold text-brand-900 hover:underline">{{ $application->job->title }}</a>
                                    <p class="text-xs text-slate-500">{{ $application->job->employerProfile->company_name ?? 'Employer' }} · {{ $application->job->location }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Contact the employer for the time, format and location of your interview.</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="activity-heading">
                    <h2 id="activity-heading" class="font-display text-base font-semibold text-brand-900">Career activity</h2>

                    @if($activity->isEmpty())
                        <p class="mt-3 text-sm text-slate-600">Status changes on your applications will show up here.</p>
                    @else
                        <ol class="mt-3 space-y-3">
                            @foreach($activity as $application)
                                <li class="flex gap-3">
                                    <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-gold-500" aria-hidden="true"></span>
                                    <div class="min-w-0">
                                        <p class="text-sm text-slate-700">
                                            <a href="{{ route('applications.show', $application) }}" class="font-medium text-brand-900 hover:underline">{{ $application->job->title }}</a>
                                            is now <x-badge :variant="$application->statusVariant()">{{ $application->statusLabel() }}</x-badge>
                                        </p>
                                        <p class="text-xs text-slate-500">{{ $application->status_updated_at?->diffForHumans() }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @endif
                </section>
            </aside>
        </div>
    </div>
@endsection
