@extends('layouts.portal')

@section('title', 'Review application')
@section('subtitle', $application->job->title)

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-stone-200 bg-white p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-xl font-semibold text-slate-900">{{ $application->jobSeeker->name }}</h2>
                        <p class="text-sm text-slate-600">{{ $application->jobSeeker->email }}</p>
                    </div>
                    <x-badge :variant="$application->statusVariant()">{{ $application->statusLabel() }}</x-badge>
                </div>

                @php($profile = $application->jobSeeker->jobSeekerProfile)
                @if($profile)
                    <dl class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Location</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ collect([$profile->city, $profile->state, $profile->country])->filter()->join(', ') ?: 'Not provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Experience</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $profile->experience_level ?: 'Not provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Qualification</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $profile->education_level ?: 'Not provided' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Availability</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $profile->available_for_work ? 'Available' : 'Not available' }}</dd>
                        </div>
                    </dl>
                @endif

                @if($application->cover_letter)
                    <div class="mt-6 border-t border-stone-200 pt-5">
                        <h3 class="font-display text-base font-semibold text-slate-900">Cover letter</h3>
                        <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $application->cover_letter }}</p>
                    </div>
                @endif
            </section>

            <section class="rounded-xl border border-stone-200 bg-white p-6">
                <h2 class="font-display text-base font-semibold text-slate-900">Update status</h2>
                <form method="POST" action="{{ route('employer.applications.update-status', $application) }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PATCH')
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="status" class="mb-1 block text-sm font-medium text-slate-700">New status</label>
                            <select id="status" name="status" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                                @foreach(['under_review' => 'Under review', 'shortlisted' => 'Shortlisted', 'interview' => 'Interview', 'accepted' => 'Accepted', 'rejected' => 'Rejected'] as $value => $label)
                                    <option value="{{ $value }}" @selected($application->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="employer_notes" class="mb-1 block text-sm font-medium text-slate-700">Note to candidate <span class="text-slate-400">(optional)</span></label>
                            <input id="employer_notes" type="text" name="employer_notes" maxlength="2000" value="{{ $application->employer_notes }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                        </div>
                    </div>
                    <button type="submit" class="rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Save decision</button>
                </form>
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-stone-200 bg-white p-5">
                <h3 class="font-display text-base font-semibold text-slate-900">Documents</h3>
                @if($application->cv)
                    <p class="mt-2 text-sm text-slate-600">{{ $application->cv->name }}</p>
                    <a href="{{ route('employer.applications.cv', $application) }}" class="mt-3 inline-flex w-full items-center justify-center rounded-lg border border-stone-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-stone-50">
                        Download CV
                    </a>
                @else
                    <p class="mt-2 text-sm text-slate-600">No CV was attached to this application.</p>
                @endif
            </section>

            <section class="rounded-xl border border-stone-200 bg-white p-5">
                <h3 class="font-display text-base font-semibold text-slate-900">Application</h3>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Role</dt>
                        <dd class="text-right font-medium text-slate-800">{{ $application->job->title }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Applied</dt>
                        <dd class="text-right font-medium text-slate-800">{{ ($application->applied_at ?? $application->created_at)->format('M j, Y') }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Last update</dt>
                        <dd class="text-right font-medium text-slate-800">{{ $application->status_updated_at?->diffForHumans() ?? '—' }}</dd>
                    </div>
                </dl>
                <a href="{{ route('employer.applications.index') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-stone-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-stone-200">Back to inbox</a>
            </section>
        </aside>
    </div>
@endsection
