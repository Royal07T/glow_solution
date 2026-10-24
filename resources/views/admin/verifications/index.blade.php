@extends('layouts.portal')

@section('title', 'Employer verifications')
@section('subtitle', 'Review private documents before granting posting access.')

@section('content')
    <form method="GET" action="{{ route('admin.verifications.index') }}" class="mb-5 flex flex-wrap items-end gap-3 rounded-xl border border-stone-200 bg-white p-4">
        <div>
            <label for="status" class="mb-1 block text-xs font-medium text-slate-600">Status</label>
            <select id="status" name="status" class="rounded-lg border border-stone-300 px-3 py-2 text-sm">
                <option value="">Any status</option>
                @foreach(['pending', 'approved', 'rejected'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-lg bg-brand-800 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Filter</button>
        <a href="{{ route('admin.verifications.index') }}" class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-stone-50">Reset</a>
    </form>

    @if($verifications->isEmpty())
        <x-empty-state icon="badge-check" title="No verification requests" message="Employer document submissions will appear here." />
    @else
        <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-sm">
                    <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Employer</th>
                            <th class="px-5 py-3 font-medium">Document</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Submitted</th>
                            <th class="px-5 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($verifications as $verification)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $verification->employerProfile->company_name ?? $verification->employerProfile->contact_person }}</p>
                                    <p class="text-xs text-slate-500">{{ $verification->employerProfile->user->email }}</p>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ ucfirst(str_replace('_', ' ', $verification->document_type)) }}</td>
                                <td class="px-5 py-4">
                                    <x-badge :variant="$verification->status === 'approved' ? 'success' : ($verification->status === 'rejected' ? 'danger' : 'warning')">
                                        {{ ucfirst($verification->status) }}
                                    </x-badge>
                                </td>
                                <td class="px-5 py-4 text-slate-500">{{ $verification->created_at->format('M j, Y') }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.verifications.show', $verification) }}" class="font-semibold text-brand-800 hover:underline">Review</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">{{ $verifications->links() }}</div>
    @endif
@endsection
