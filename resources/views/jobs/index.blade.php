@extends('layouts.app')

@section('title', 'Find jobs')
@section('description', 'Browse live domestic and professional vacancies across Nigeria.')

@section('content')
    <section class="bg-brand-800">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
            <div class="max-w-3xl">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gold-300">Live vacancies</p>
                <h1 class="mt-2 font-display text-3xl font-bold text-white sm:text-4xl">Find work that fits your life</h1>
                <p class="mt-2 text-sm text-brand-100 sm:text-base">Search trusted domestic and professional roles from verified employers.</p>
            </div>

            <form method="GET" action="{{ route('jobs.index') }}" class="mt-6 rounded-xl bg-white p-4 shadow-lg">
                <div class="grid gap-3 md:grid-cols-12">
                    <div class="md:col-span-4">
                        <label for="keyword" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Keyword</label>
                        <input id="keyword" name="keyword" type="search" value="{{ request('keyword') }}" placeholder="Job title, company or skill" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                    </div>
                    <div class="md:col-span-3">
                        <label for="location" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Location</label>
                        <input id="location" name="location" type="text" value="{{ request('location') }}" placeholder="City or state" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                    </div>
                    <div class="md:col-span-3">
                        <label for="category" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Category</label>
                        <select id="category" name="category" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                            <option value="">All categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end md:col-span-2">
                        <button type="submit" class="inline-flex w-full min-h-[44px] items-center justify-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">
                            <x-icon name="search" class="h-4 w-4" /> Search
                        </button>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <div>
                        <label for="type" class="sr-only">Job type</label>
                        <select id="type" name="type" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            <option value="">Any job type</option>
                            <option value="domestic" @selected(request('type') === 'domestic')>Domestic</option>
                            <option value="professional" @selected(request('type') === 'professional')>Professional</option>
                        </select>
                    </div>
                    <div>
                        <label for="employment_type" class="sr-only">Employment type</label>
                        <select id="employment_type" name="employment_type" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                            <option value="">Any employment type</option>
                            @foreach(['full_time' => 'Full time', 'part_time' => 'Part time', 'contract' => 'Contract', 'temporary' => 'Temporary', 'live_in' => 'Live in', 'live_out' => 'Live out'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('employment_type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if(request()->hasAny(['keyword', 'location', 'category', 'type', 'employment_type']))
                        <a href="{{ route('jobs.index') }}" class="text-sm font-medium text-slate-600 hover:text-brand-700">Clear filters</a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="font-display text-xl font-semibold text-brand-900">Open roles</h2>
                <p class="text-sm text-slate-500">
                    {{ $jobs->total() }} {{ \Illuminate\Support\Str::plural('vacancy', $jobs->total()) }} found
                    @if(request('keyword')) for “{{ request('keyword') }}” @endif
                </p>
            </div>
            @if($jobs->total() > 0)
                <p class="text-xs text-slate-400">Showing page {{ $jobs->currentPage() }} of {{ $jobs->lastPage() }}</p>
            @endif
        </div>

        @if($jobs->isEmpty())
            <x-empty-state icon="search" title="No vacancies match your search" message="Try fewer filters, a different location or a broader keyword. New roles are added regularly.">
                <a href="{{ route('jobs.index') }}" class="inline-flex rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Show all jobs</a>
            </x-empty-state>
        @else
            <div class="grid gap-4 lg:grid-cols-2">
                @foreach($jobs as $job)
                    <x-job-card :job="$job" :applied="in_array($job->id, $appliedJobIds)" :saved="in_array($job->id, $savedJobIds)" />
                @endforeach
            </div>

            <div class="mt-8">{{ $jobs->links() }}</div>
        @endif
    </div>
@endsection
