@extends('layouts.portal')

@php
    $type = old('type', $application ? 'application' : ($job ? 'job' : 'general'));
    $contextJob = $application?->job ?? $job;
@endphp

@section('title', 'Give feedback')
@section('subtitle', 'Tell us what is working and what we can improve.')

@section('breadcrumbs')
    <a href="{{ route('home.redirect') }}" class="hover:text-brand-700">Dashboard</a>
    <span aria-hidden="true">/</span>
    <span aria-current="page">Feedback</span>
@endsection

@section('content')
    <div class="mx-auto max-w-3xl">
        <form method="POST" action="{{ route('feedback.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">

            @if($type === 'application' && $application)
                <input type="hidden" name="application_id" value="{{ old('application_id', $application->id) }}">
            @elseif($type === 'job' && $job)
                <input type="hidden" name="job_id" value="{{ old('job_id', $job->id) }}">
            @endif

            @if($contextJob)
                <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        {{ $type === 'application' ? 'Feedback about your application' : 'Feedback about this job' }}
                    </p>
                    <p class="mt-1 font-display text-lg font-semibold text-brand-900">{{ $contextJob->title }}</p>
                    <p class="text-sm text-slate-600">
                        {{ $contextJob->employerProfile->company_name ?? 'Employer' }} · {{ $contextJob->location }}
                    </p>
                    @if($application)
                        <p class="mt-2 text-xs text-slate-500">Application status: {{ $application->statusLabel() }}</p>
                    @endif
                </section>
            @endif

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">
                    {{ $type === 'general' ? 'Share your feedback' : 'Tell us about your experience' }}
                </h2>
                <p class="mt-1 text-sm text-slate-600">
                    Your feedback helps us improve Glow Recruitment. Please do not include passwords or other sensitive information.
                </p>

                <div class="mt-5 space-y-4">
                    @if($type !== 'general')
                        <div>
                            <label for="rating" class="mb-1 block text-sm font-medium text-slate-700">Rating</label>
                            <select id="rating" name="rating" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 sm:max-w-xs">
                                <option value="">No rating</option>
                                @foreach([5 => '5 - Excellent', 4 => '4 - Good', 3 => '3 - Okay', 2 => '2 - Poor', 1 => '1 - Very poor'] as $value => $label)
                                    <option value="{{ $value }}" @selected((string) old('rating') === (string) $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('rating') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div>
                        <label for="subject" class="mb-1 block text-sm font-medium text-slate-700">Subject</label>
                        <input id="subject" type="text" name="subject" maxlength="150" value="{{ old('subject') }}" placeholder="A short summary" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        @error('subject') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="message" class="mb-1 block text-sm font-medium text-slate-700">Your feedback <span class="text-danger" aria-hidden="true">*</span></label>
                        <textarea id="message" name="message" rows="7" required minlength="5" maxlength="2000" placeholder="Tell us what happened or what we can do better." class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">{{ old('message') }}</textarea>
                        @error('message') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800">
                    <x-icon name="send" class="h-4 w-4" />
                    Submit feedback
                </button>
                <a href="{{ route('home.redirect') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
            </div>
        </form>
    </div>
@endsection
