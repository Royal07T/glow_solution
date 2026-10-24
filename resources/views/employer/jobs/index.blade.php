@extends('layouts.portal')

@section('title', 'My jobs')
@section('subtitle', 'Manage listings and see how many candidates applied.')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-600">{{ $jobs->total() }} {{ Str::plural('listing', $jobs->total()) }}.</p>
        <a href="{{ route('employer.jobs.create') }}" class="rounded-lg bg-brand-800 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Post a job</a>
    </div>

    @if($jobs->isEmpty())
        <x-empty-state icon="briefcase" title="No job listings yet" message="Post your first role. It is reviewed by an administrator before going live.">
            <a href="{{ route('employer.jobs.create') }}" class="inline-flex rounded-lg bg-brand-800 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Post a job</a>
        </x-empty-state>
    @else
        <div class="overflow-hidden rounded-xl border border-stone-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-stone-200 text-sm">
                    <thead class="bg-stone-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Role</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Applicants</th>
                            <th class="px-5 py-3 font-medium">Created</th>
                            <th class="px-5 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @foreach($jobs as $job)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $job->title }}</p>
                                    <p class="text-xs text-slate-500">{{ $job->location }} · {{ ucfirst(str_replace('_', ' ', $job->employment_type)) }}</p>
                                    @if($job->status === 'rejected' && $job->admin_notes)
                                        <p class="mt-1 text-xs text-danger">Reason: {{ $job->admin_notes }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4"><x-badge :variant="$job->statusVariant()">{{ $job->statusLabel() }}</x-badge></td>
                                <td class="px-5 py-4 text-slate-600">{{ $job->applications_count }}</td>
                                <td class="px-5 py-4 text-slate-500">{{ $job->created_at->format('M j, Y') }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-3">
                                        @if($job->status === 'approved')
                                            <a href="{{ route('jobs.show', $job) }}" class="font-semibold text-slate-600 hover:text-brand-800">View</a>
                                        @endif
                                        <a href="{{ route('employer.jobs.edit', $job) }}" class="font-semibold text-brand-800 hover:underline">Edit</a>
                                        @if($job->status !== 'approved')
                                            <form method="POST" action="{{ route('employer.jobs.destroy', $job) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" data-confirm="Delete this job listing?" class="font-semibold text-danger hover:underline">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">{{ $jobs->links() }}</div>
    @endif
@endsection
