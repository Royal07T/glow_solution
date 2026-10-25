@extends('layouts.portal')

@section('title', 'Employer verification')
@section('subtitle', 'Verify your business so your job posts can go live.')

@section('content')
    @php
        $profile = auth()->user()->employerProfile;
        $status = $profile->verification_status ?? 'pending';
        $verifications = $profile?->verifications()->latest()->get() ?? collect();
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-base font-semibold text-brand-900">Verification status</h2>
                        <p class="mt-1 text-sm text-slate-600">Verified employers can publish jobs and receive applications.</p>
                    </div>
                    <x-badge :variant="match($status) { 'approved' => 'success', 'rejected' => 'danger', 'submitted' => 'info', default => 'warning' }">
                        {{ ucfirst($status) }}
                    </x-badge>
                </div>

                @if($status === 'approved')
                    <p class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        Your employer account is verified. You can post and manage jobs from your dashboard.
                    </p>
                @elseif($status === 'rejected')
                    <p class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        Your previous submission was rejected. Please review the notes below and contact the Glow team to resubmit.
                    </p>
                @else
                    <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                        Verification is pending. The Glow team reviews submitted documents before your account can publish jobs.
                    </p>
                @endif
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">What you need</h2>
                <ul class="mt-3 space-y-3 text-sm text-slate-600">
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> A valid business registration certificate or equivalent document.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> A government-issued ID for the business owner or authorised contact.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> Accurate company details in your employer profile.</li>
                </ul>
                <p class="mt-4 text-sm text-slate-600">
                    To submit documents or ask about the process, email
                    <a href="mailto:info@glowrecruitment.com" class="font-semibold text-brand-700 hover:underline">info@glowrecruitment.com</a>.
                </p>
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
                            <p class="font-medium text-slate-800">{{ ucfirst($verification->document_type ?? 'Document') }}</p>
                            <x-badge :variant="match($verification->status) { 'approved' => 'success', 'rejected' => 'danger', default => 'warning' }">{{ ucfirst($verification->status) }}</x-badge>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Submitted {{ $verification->created_at->format('M j, Y') }}{{ $verification->reviewed_at ? ' · reviewed '.$verification->reviewed_at->format('M j, Y') : '' }}</p>
                        @if($verification->admin_notes)
                            <p class="mt-2 rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ $verification->admin_notes }}</p>
                        @endif
                    </div>
                @empty
                    <div class="p-5">
                        <x-empty-state icon="shield-check" title="No verification submissions" message="Contact the Glow team to start your verification." />
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
