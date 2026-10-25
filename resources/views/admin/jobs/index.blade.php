@extends('layouts.portal')

@section('title', 'Job moderation')
@section('subtitle', $pendingCount.' listing'.($pendingCount === 1 ? '' : 's').' waiting for review.')

@section('content')
    <div class="space-y-5">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.jobs.index') }}" @class([
                'rounded-full border px-4 py-2 text-sm font-semibold',
                'border-brand-700 bg-brand-700 text-white' => ! request('status'),
                'border-slate-300 bg-white text-slate-700 hover:bg-slate-50' => (bool) request('status'),
            ])>All jobs</a>
            @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'closed' => 'Closed', 'expired' => 'Expired'] as $value => $label)
                <a href="{{ route('admin.jobs.index', ['status' => $value]) }}" @class([
                    'rounded-full border px-4 py-2 text-sm font-semibold',
                    'border-brand-700 bg-brand-700 text-white' => request('status') === $value,
                    'border-slate-300 bg-white text-slate-700 hover:bg-slate-50' => request('status') !== $value,
                ])>{{ $label }}</a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('admin.jobs.index') }}" class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-3">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <div>
                <label for="search" class="mb-1 block text-xs font-medium text-slate-600">Search title</label>
                <input id="search" type="search" name="search" value="{{ request('search') }}" placeholder="Job title" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
            </div>
            <div>
                <label for="status" class="mb-1 block text-xs font-medium text-slate-600">Status</label>
                <select id="status" name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                    <option value="">Any status</option>
                    @foreach(['draft' => 'Draft', 'pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected', 'closed' => 'Closed', 'expired' => 'Expired'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="inline-flex min-h-[40px] items-center gap-2 rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">
                    <x-icon name="filter" class="h-4 w-4" /> Filter
                </button>
                <a href="{{ route('admin.jobs.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
            </div>
        </form>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Listing</th>
                            <th class="px-5 py-3 font-medium">Employer</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Created</th>
                            <th class="px-5 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($jobs as $job)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <a href="{{ route('admin.jobs.show', $job) }}" class="font-semibold text-brand-900 hover:underline">{{ $job->title }}</a>
                                    <p class="text-xs text-slate-500">{{ $job->location }}</p>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $job->employerProfile->company_name ?? $job->employerProfile->user->name ?? 'Employer' }}</td>
                                <td class="px-5 py-4"><x-badge :variant="$job->statusVariant()">{{ $job->statusLabel() }}</x-badge></td>
                                <td class="px-5 py-4 text-slate-500">{{ $job->created_at->format('M j, Y') }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.jobs.show', $job) }}" class="font-semibold text-brand-700 hover:underline">Review</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center">
                                    <p class="text-sm text-slate-600">No listings match these filters.</p>
                                    <a href="{{ route('admin.jobs.index') }}" class="mt-2 inline-block text-sm font-semibold text-brand-700 hover:underline">Clear filters</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $jobs->links() }}</div>
    </div>
@endsection
