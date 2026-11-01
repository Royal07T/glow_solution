@extends('layouts.portal')

@section('title', 'Employer verification')
@section('subtitle', 'Upload your CAC certificate so an administrator can verify your business.')

@section('content')
    @php
        $status = $profile->verification_status ?? 'pending';
        $latest = $verifications->first();
        $hasPendingSubmission = $verifications->contains(fn ($verification) => $verification->status === 'pending');
        $displayStatus = match (true) {
            $hasPendingSubmission => 'submitted',
            $status === 'pending' && $verifications->isEmpty() => 'unsubmitted',
            default => $status,
        };
        $canSubmit = $status !== 'approved' && $status !== 'submitted' && ! $hasPendingSubmission;
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-base font-semibold text-brand-900">Verification status</h2>
                        <p class="mt-1 text-sm text-slate-600">Verified employers can publish jobs and receive applications.</p>
                    </div>
                    <x-badge :variant="match($displayStatus) { 'approved' => 'success', 'rejected' => 'danger', 'submitted' => 'info', 'unsubmitted' => 'neutral', default => 'warning' }">
                        {{ match($displayStatus) { 'submitted' => 'Under review', 'unsubmitted' => 'Not submitted', default => ucfirst($displayStatus) } }}
                    </x-badge>
                </div>

                @if($status === 'approved')
                    <p class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        Your employer account is verified. You can post and manage jobs from your dashboard.
                    </p>
                @elseif($hasPendingSubmission)
                    <p class="mt-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                        Your CAC certificate is awaiting review. We will notify you once an administrator has checked it.
                    </p>
                @elseif($status === 'rejected')
                    <p class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        Your previous submission was rejected. Review the reason below, then upload a clear copy of your CAC certificate.
                    </p>
                    @if($latest?->admin_notes)
                        <p class="mt-3 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ $latest->admin_notes }}</p>
                    @endif
                @else
                    <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        Your account is not verified yet. Upload your CAC certificate below to start the review.
                    </p>
                @endif
            </section>

            @if($canSubmit)
                <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="font-display text-base font-semibold text-brand-900">Submit CAC certificate</h2>
                    <p class="mt-1 text-sm text-slate-600">
                        Upload a clear copy of your Corporate Affairs Commission (CAC) certificate. The document is stored privately and only visible to administrators.
                    </p>

                    <form method="POST" action="{{ route('employer.verification.store') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                        @csrf

                        <div>
                            <label for="cac_certificate" class="mb-1 block text-sm font-medium text-slate-700">CAC certificate</label>
                            <input id="cac_certificate" type="file" name="cac_certificate" accept=".pdf,.jpg,.jpeg,.png" required
                                   class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-brand-700">
                            <p class="mt-1 text-xs text-slate-500">PDF, JPG or PNG. Maximum 5MB.</p>
                            @error('cac_certificate') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="notes" class="mb-1 block text-sm font-medium text-slate-700">Notes <span class="text-slate-400">(optional)</span></label>
                            <textarea id="notes" name="notes" rows="3" maxlength="1000" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Anything the reviewer should know about your business.">{{ old('notes') }}</textarea>
                            @error('notes') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">
                            <x-icon name="document" class="h-4 w-4" /> Submit for verification
                        </button>
                    </form>
                </section>
            @endif

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">What you need</h2>
                <ul class="mt-3 space-y-3 text-sm text-slate-600">
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> A valid Corporate Affairs Commission (CAC) certificate showing your business registration.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> A clear scan or photo where the certificate number and company name are readable.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> Accurate company details in your employer profile.</li>
                </ul>
                <a href="{{ route('employer.profile.edit') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                    <x-icon name="building" class="h-4 w-4" /> Update company profile
                </a>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="font-display text-base font-semibold text-brand-900">Submission history</h2>
                </div>
                @forelse($verifications as $verification)
                    <div class="border-b border-slate-100 px-5 py-4 last:border-0">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="font-medium text-slate-800">{{ $verification->documentTypeLabel() }}</p>
                            <x-badge :variant="match($verification->status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }">{{ ucfirst($verification->status) }}</x-badge>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Submitted {{ $verification->created_at->format('M j, Y') }}{{ $verification->reviewed_at ? ' · reviewed '.$verification->reviewed_at->format('M j, Y') : '' }}</p>
                        @if($verification->notes)
                            <p class="mt-2 text-sm text-slate-600">{{ $verification->notes }}</p>
                        @endif
                        @if($verification->admin_notes)
                            <p class="mt-2 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ $verification->admin_notes }}</p>
                        @endif
                    </div>
                @empty
                    <div class="p-5">
                        <x-empty-state icon="shield-check" title="No verification submissions" message="Upload your CAC certificate above to start your verification." />
                    </div>
                @endforelse
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Company details</h2>
                <dl class="mt-3 space-y-2 text-sm">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Company</dt>
                        <dd class="font-medium text-slate-800">{{ $profile->company_name ?? $profile->contact_person ?? 'Not set' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Location</dt>
                        <dd class="font-medium text-slate-800">{{ $profile->city ?? '—' }}{{ $profile->state ? ', '.$profile->state : '' }}</dd>
                    </div>
                </dl>
                <a href="{{ route('employer.profile.edit') }}" class="mt-3 inline-flex text-sm font-semibold text-brand-700 hover:underline">Edit company profile</a>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Need help?</h2>
                <p class="mt-2 text-sm text-slate-600">The Glow Recruitment team can guide you through verification.</p>
                <a href="mailto:info@glowrecruitment.com" class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-brand-700 hover:underline">
                    <x-icon name="mail" class="h-4 w-4" /> info@glowrecruitment.com
                </a>
            </section>
        </aside>
    </div>
@endsection
