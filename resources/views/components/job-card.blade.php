@props([
    'job',
    'applied' => false,
    'saved' => false,
])

@php
    $company = $job->employerProfile->company_name
        ?? $job->employerProfile->contact_person
        ?? 'Glow employer';
    $initials = \Illuminate\Support\Str::of($company)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))
        ->implode('');
    $employment = ucfirst(str_replace('_', ' ', $job->employment_type ?? ''));
    $salary = null;
    if ($job->salary_min || $job->salary_max) {
        $salary = trim(($job->salary_currency ?? 'NGN').' '.($job->salary_min ? number_format((float) $job->salary_min) : '').($job->salary_min && $job->salary_max ? ' – ' : '').($job->salary_max ? number_format((float) $job->salary_max) : ''));
    }
    $posted = $job->published_at ?? $job->created_at;
    $isJobSeeker = auth()->check() && auth()->user()->isJobSeeker();
@endphp

<article class="group flex flex-col rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-brand-200 hover:shadow-md">
    <div class="flex items-start gap-4">
        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-brand-50 font-display text-sm font-bold text-brand-700 ring-1 ring-brand-100" aria-hidden="true">
            {{ $initials ?: 'G' }}
        </span>

        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-start justify-between gap-2">
                <h3 class="font-display text-lg font-semibold leading-tight text-brand-900">
                    <a href="{{ route('jobs.show', $job) }}" class="hover:text-brand-700 focus-visible:underline">{{ $job->title }}</a>
                </h3>
                <x-badge :variant="$job->type === 'domestic' ? 'gold' : 'brand'">{{ ucfirst($job->type ?? 'role') }}</x-badge>
            </div>
            <p class="mt-1 text-sm font-medium text-slate-600">{{ $company }}</p>
            <ul class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                <li class="inline-flex items-center gap-1"><x-icon name="map-pin" class="h-3.5 w-3.5" />{{ $job->location }}</li>
                @if($employment)
                    <li class="inline-flex items-center gap-1"><x-icon name="briefcase" class="h-3.5 w-3.5" />{{ $employment }}</li>
                @endif
                @if($posted)
                    <li class="inline-flex items-center gap-1"><x-icon name="clock" class="h-3.5 w-3.5" />{{ $posted->diffForHumans() }}</li>
                @endif
            </ul>
        </div>
    </div>

    <p class="mt-4 line-clamp-2 text-sm leading-relaxed text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags($job->description), 190) }}</p>

    @if(! empty($job->match_reasons))
        <p class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-gold-700">
            <x-icon name="sparkles" class="h-3.5 w-3.5" />
            {{ $job->match_reasons[0] }}@if(count($job->match_reasons) > 1) · +{{ count($job->match_reasons) - 1 }} more match{{ count($job->match_reasons) > 2 ? 'es' : '' }}@endif
        </p>
    @endif

    <div class="mt-4 flex flex-wrap items-center gap-2">
        @if($salary)
            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-semibold text-green-700">
                {{ $salary }}
            </span>
        @endif
        @if($job->salary_negotiable)
            <x-badge variant="neutral">Negotiable</x-badge>
        @endif
        @if($job->application_deadline)
            <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                <x-icon name="calendar" class="h-3.5 w-3.5" />
                Apply by {{ $job->application_deadline->format('M j, Y') }}
            </span>
        @endif
    </div>

    <div class="mt-5 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">
        <a href="{{ route('jobs.show', $job) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
            View details
        </a>

        @if($isJobSeeker)
            @if($applied)
                <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600">
                    <x-icon name="check" class="h-4 w-4" /> Applied
                </span>
            @else
                <a href="{{ route('applications.create', $job) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-700 px-3 py-2 text-sm font-semibold text-white transition hover:bg-brand-800">
                    Apply now
                </a>
            @endif

            <form method="POST" action="{{ route('jobs.save', $job) }}" class="ml-auto">
                @csrf
                <button type="submit" aria-pressed="{{ $saved ? 'true' : 'false' }}" class="inline-flex items-center gap-1.5 rounded-lg border px-3 py-2 text-sm font-semibold transition {{ $saved ? 'border-gold-300 bg-gold-50 text-gold-700' : 'border-slate-300 text-slate-600 hover:bg-slate-50' }}">
                    <x-icon name="bookmark" class="h-4 w-4" />
                    {{ $saved ? 'Saved' : 'Save' }}
                </button>
            </form>
        @elseif(auth()->guest())
            <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-700 px-3 py-2 text-sm font-semibold text-white transition hover:bg-brand-800">
                Sign in to apply
            </a>
        @endif
    </div>
</article>
