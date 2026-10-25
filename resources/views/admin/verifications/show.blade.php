@extends('layouts.portal')

@section('title', 'Review verification')
@section('subtitle', $verification->employerProfile->company_name ?? $verification->employerProfile->contact_person)

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-stone-200 bg-white p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-xl font-semibold text-slate-900">{{ $verification->employerProfile->company_name ?? $verification->employerProfile->contact_person }}</h2>
                        <p class="text-sm text-slate-600">{{ $verification->employerProfile->user->email }}</p>
                    </div>
                    <x-badge :variant="$verification->status === 'approved' ? 'success' : ($verification->status === 'rejected' ? 'danger' : 'warning')">
                        {{ ucfirst($verification->status) }}
                    </x-badge>
                </div>

                <dl class="mt-5 grid gap-3 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Document type</dt>
                        <dd class="text-sm font-medium text-slate-800">{{ ucfirst(str_replace('_', ' ', $verification->document_type)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Submitted</dt>
                        <dd class="text-sm font-medium text-slate-800">{{ $verification->created_at->format('M j, Y g:ia') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Business registration</dt>
                        <dd class="text-sm font-medium text-slate-800">{{ $verification->employerProfile->business_registration_number ?: 'Not provided' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500">Location</dt>
                        <dd class="text-sm font-medium text-slate-800">{{ collect([$verification->employerProfile->city, $verification->employerProfile->country])->filter()->join(', ') ?: 'Not provided' }}</dd>
                    </div>
                </dl>

                @if($verification->notes)
                    <div class="mt-5 rounded-lg bg-stone-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Employer notes</p>
                        <p class="mt-1 text-sm text-slate-700">{{ $verification->notes }}</p>
                    </div>
                @endif

                <div class="mt-5">
                    <a href="{{ route('admin.verifications.document', $verification) }}" class="inline-flex rounded-lg border border-stone-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-stone-50">
                        Download private document
                    </a>
                </div>
            </section>
        </div>

        <aside class="space-y-4">
            @if($verification->status === 'pending')
                <section class="rounded-xl border border-stone-200 bg-white p-5">
                    <h3 class="font-display text-base font-semibold text-slate-900">Decision</h3>
                    <form method="POST" action="{{ route('admin.verifications.approve', $verification) }}" class="mt-3">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">Approve employer</button>
                    </form>
                    <form method="POST" action="{{ route('admin.verifications.reject', $verification) }}" class="mt-4 space-y-2">
                        @csrf
                        <label for="admin_notes" class="block text-sm font-medium text-slate-700">Rejection reason</label>
                        <textarea id="admin_notes" name="admin_notes" rows="3" required maxlength="1000" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm"></textarea>
                        <button type="submit" data-confirm="Reject this verification?" class="w-full rounded-lg bg-danger px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Reject</button>
                    </form>
                </section>
            @else
                <section class="rounded-xl border border-stone-200 bg-white p-5">
                    <h3 class="font-display text-base font-semibold text-slate-900">Reviewed</h3>
                    <p class="mt-2 text-sm text-slate-600">
                        {{ $verification->reviewer->name ?? 'Administrator' }} · {{ $verification->reviewed_at?->format('M j, Y') }}
                    </p>
                    @if($verification->admin_notes)
                        <p class="mt-2 rounded-lg bg-stone-50 p-3 text-sm text-slate-700">{{ $verification->admin_notes }}</p>
                    @endif
                </section>
            @endif

            <section class="rounded-xl border border-stone-200 bg-white p-5">
                <h3 class="font-display text-base font-semibold text-slate-900">Employer account</h3>
                <a href="{{ route('admin.users.show', $verification->employerProfile->user) }}" class="mt-2 block text-sm font-semibold text-[#062B52] hover:underline">
                    View user profile
                </a>
            </section>

            <a href="{{ route('admin.verifications.index') }}" class="inline-flex w-full items-center justify-center rounded-lg bg-stone-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-stone-200">Back to queue</a>
        </aside>
    </div>
@endsection
