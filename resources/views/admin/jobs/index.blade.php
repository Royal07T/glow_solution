@extends('layouts.portal')

@section('title', 'Job moderation')
@section('subtitle', $pendingCount.' listings waiting for review.')

@section('content')
    <form method="GET" action="{{ route('admin.jobs.index') }}" class="mb-5 grid gap-3 rounded-xl border border-stone-200 bg-white p-4 sm:grid-cols-3">
        <div>
            <label for="search" class="mb-1 block text-xs font-medium text-slate-600">Search title</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="status" class="mb-1 block text-xs font-medium text-slate-600">Status</label>
            <select id="status" name="status" class="w-full rounded-lg border border-stone-300 px-3 py-2 text-sm">
                <option value="">Any status</option>
                @foreach(['pending', 'approved', 'rejected', 'closed', 'expired'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="rounded-lg bg-[#062B52 px-4 py-2 text-sm font-semibold text-white hover:bg-[#5B21B6">Filter</button>
            <a href="{{ route('admin.jobs.index') }}" class="rounded-lg border border-stone-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-stone-50">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-stone-200 text-sm">
                <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 font-medium">Listing</th>
                        <th class="px-5 py-3 font-medium">Employer</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Created</th>
                        <th class="px-5 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse($jobs as $job)
                        <tr>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ $job->title }}</p>
                                <p class="text-xs text-slate-500">{{ $job->location }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $job->employerProfile->company_name ?? $job->employerProfile->user->name ?? 'Employer' }}</td>
                            <td class="px-5 py-4"><x-badge :variant="$job->statusVariant()">{{ $job->statusLabel() }}</x-badge></td>
                            <td class="px-5 py-4 text-slate-500">{{ $job->created_at->format('M j, Y') }}</td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('admin.jobs.show', $job) }}" class="font-semibold text-[#062B52 hover:underline">Review</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-600">No listings match these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $jobs->links() }}</div>
@endsection
