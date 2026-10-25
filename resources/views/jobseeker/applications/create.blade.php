@extends('layouts.portal')

@section('title', 'Apply for a role')
@section('subtitle', $job->title)

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <section class="rounded-xl border border-stone-200 bg-white p-6">
                <h2 class="font-display text-xl font-semibold text-slate-900">{{ $job->title }}</h2>
                <p class="text-sm text-slate-600">{{ $job->employerProfile->company_name ?? 'Employer' }} · {{ $job->location }}</p>

                <div class="mt-4 flex flex-wrap gap-2">
                    <x-badge variant="brand">{{ ucfirst($job->type) }}</x-badge>
                    <x-badge variant="neutral">{{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</x-badge>
                    @if($job->salary_min)
                        <x-badge variant="gold">
                            {{ $job->salary_currency }} {{ number_format((float) $job->salary_min) }}{{ $job->salary_max ? ' - '.number_format((float) $job->salary_max) : '' }}
                        </x-badge>
                    @endif
                </div>

                <form method="POST" action="{{ route('applications.store', $job) }}" class="mt-6 space-y-5">
                    @csrf

                    <div>
                        <label for="cv_id" class="mb-1 block text-sm font-medium text-slate-700">CV to attach</label>
                        @if($cvs->isEmpty())
                            <div class="rounded-lg border border-yellow-300 bg-yellow-50 px-4 py-3 text-sm text-yellow-900">
                                You have not uploaded a CV yet.
                                <a href="{{ route('cvs.index') }}" class="font-semibold underline">Upload a CV</a>
                                before applying to make the best impression.
                            </div>
                            <select id="cv_id" name="cv_id" class="mt-2 w-full rounded-lg border border-stone-300 px-3 py-2">
                                <option value="">Apply without a CV</option>
                            </select>
                        @else
                            <select id="cv_id" name="cv_id" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                                <option value="">Use my profile only</option>
                                @foreach($cvs as $cv)
                                    <option value="{{ $cv->id }}" @selected(old('cv_id') == $cv->id)>
                                        {{ $cv->name }}{{ $cv->is_primary ? ' (primary)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-500">Your CV is only shared with this employer.</p>
                        @endif
                        @error('cv_id') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cover_letter" class="mb-1 block text-sm font-medium text-slate-700">Cover letter <span class="text-slate-400">(optional)</span></label>
                        <textarea id="cover_letter" name="cover_letter" rows="7" maxlength="2000" class="w-full rounded-lg border border-stone-300 px-3 py-2" placeholder="Tell the employer why you are a great fit.">{{ old('cover_letter') }}</textarea>
                        <p class="mt-1 text-xs text-slate-500">Maximum 2,000 characters.</p>
                        @error('cover_letter') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="rounded-lg bg-[#062B52] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#07335c]">Submit application</button>
                        <a href="{{ route('jobs.show', $job) }}" class="rounded-lg border border-stone-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-stone-50">Cancel</a>
                    </div>
                </form>
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-stone-200 bg-white p-5">
                <h3 class="font-display text-base font-semibold text-slate-900">Job overview</h3>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Location</dt>
                        <dd class="text-right font-medium text-slate-800">{{ $job->location }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Employment</dt>
                        <dd class="text-right font-medium text-slate-800">{{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Positions</dt>
                        <dd class="text-right font-medium text-slate-800">{{ $job->positions_available }}</dd>
                    </div>
                    @if($job->application_deadline)
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-500">Deadline</dt>
                            <dd class="text-right font-medium text-slate-800">{{ $job->application_deadline->format('M j, Y') }}</dd>
                        </div>
                    @endif
                </dl>
            </section>
        </aside>
    </div>
@endsection
