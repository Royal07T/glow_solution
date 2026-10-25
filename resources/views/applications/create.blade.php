@extends('layouts.portal')

@section('title', 'Apply for this role')
@section('subtitle', $job->title)

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('applications.store', $job) }}" class="space-y-6">
                @csrf

                <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-display text-base font-semibold text-brand-900">Role summary</h2>
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        <x-badge :variant="$job->type === 'domestic' ? 'gold' : 'brand'">{{ ucfirst($job->type ?? 'Role') }}</x-badge>
                        @if($job->employment_type)
                            <x-badge variant="neutral">{{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</x-badge>
                        @endif
                    </div>
                    <h3 class="mt-2 font-display text-lg font-semibold text-brand-900">{{ $job->title }}</h3>
                    <p class="text-sm text-slate-600">{{ $job->employerProfile->company_name ?? $job->employerProfile->contact_person ?? 'Employer' }} · {{ $job->location }}</p>
                    @if($job->application_deadline)
                        <p class="mt-2 text-xs text-slate-500">Applications close {{ $job->application_deadline->format('M j, Y') }}.</p>
                    @endif
                </section>

                <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-display text-base font-semibold text-brand-900">Your application</h2>

                    <div class="mt-4">
                        <label for="cv_id" class="mb-1 block text-sm font-medium text-slate-700">Attach a CV</label>
                        @if($cvs->isEmpty())
                            <div class="rounded-lg border border-gold-200 bg-gold-50 px-4 py-3 text-sm text-gold-700">
                                You have not uploaded a CV yet.
                                <a href="{{ route('cvs.index') }}" class="font-semibold underline">Upload a CV</a> to attach it to this application.
                            </div>
                        @else
                            <select id="cv_id" name="cv_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                                <option value="">No CV</option>
                                @foreach($cvs as $cv)
                                    <option value="{{ $cv->id }}" @selected((string) old('cv_id') === (string) $cv->id)>
                                        {{ $cv->name }}{{ $cv->is_primary ? ' (primary)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-slate-500"><a href="{{ route('cvs.index') }}" class="font-medium text-brand-700 hover:underline">Manage your CVs</a></p>
                        @endif
                        @error('cv_id') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-5">
                        <label for="cover_letter" class="mb-1 block text-sm font-medium text-slate-700">Cover letter <span class="text-slate-400">(optional)</span></label>
                        <textarea id="cover_letter" name="cover_letter" rows="8" maxlength="2000" placeholder="Tell the employer why you are a good fit for this role…" class="w-full rounded-lg border px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('cover_letter') ? 'border-red-400' : 'border-slate-300' }}">{{ old('cover_letter') }}</textarea>
                        <p class="mt-1 text-xs text-slate-500">Up to 2,000 characters.</p>
                        @error('cover_letter') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                </section>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="inline-flex min-h-[44px] items-center gap-2 rounded-lg bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">
                        <x-icon name="send" class="h-4 w-4" /> Submit application
                    </button>
                    <a href="{{ route('jobs.show', $job) }}" class="text-sm font-medium text-slate-600 hover:text-brand-700">Cancel</a>
                </div>
            </form>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Before you apply</h2>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> Make sure your profile is up to date.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> Attach your most relevant CV.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> Keep your cover letter concise and specific.</li>
                </ul>
                <a href="{{ route('jobseeker.profile.edit') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <x-icon name="user" class="h-4 w-4" /> Review profile
                </a>
            </section>
        </aside>
    </div>
@endsection
