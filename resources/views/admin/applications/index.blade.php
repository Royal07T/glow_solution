@extends('layouts.portal')

@section('title', 'Applications')
@section('subtitle', 'Review candidates across all job listings.')

@section('content')
    <form method="GET" action="{{ route('admin.applications.index') }}" class="mb-5 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-3">
        <div>
            <label for="job" class="mb-1 block text-xs font-medium text-slate-600">Job</label>
            <select id="job" name="job" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All jobs</option>
                @foreach($jobs as $job)
                    <option value="{{ $job->id }}" @selected(request('job') == $job->id)>{{ $job->title }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status" class="mb-1 block text-xs font-medium text-slate-600">Status</label>
            <select id="status" name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Any status</option>
                @foreach(['submitted' => 'Submitted', 'under_review' => 'Under review', 'shortlisted' => 'Shortlisted', 'interview' => 'Interview', 'accepted' => 'Accepted', 'rejected' => 'Rejected', 'withdrawn' => 'Withdrawn'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Filter</button>
            <a href="{{ route('admin.applications.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    @if($applications->isEmpty())
        <x-empty-state icon="users" title="No applications found" message="Applications submitted to live listings will appear here." />
    @else
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-medium">Candidate</th>
                            <th class="px-5 py-3 font-medium">Role</th>
                            <th class="px-5 py-3 font-medium">Employer</th>
                            <th class="px-5 py-3 font-medium">Status</th>
                            <th class="px-5 py-3 font-medium">Applied</th>
                            <th class="px-5 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($applications as $application)
                            <tr>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-900">{{ $application->jobSeeker->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $application->jobSeeker->email }}</p>
                                </td>
                                <td class="px-5 py-4 text-slate-600">{{ $application->job->title }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $application->job->employerProfile->company_name ?? $application->job->employerProfile->user->name ?? 'Employer' }}</td>
                                <td class="px-5 py-4"><x-badge :variant="$application->statusVariant()">{{ $application->statusLabel() }}</x-badge></td>
                                <td class="px-5 py-4 text-slate-500">{{ ($application->applied_at ?? $application->created_at)->format('M j, Y') }}</td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.applications.show', $application) }}" class="font-semibold text-brand-700 hover:underline">Review</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6">{{ $applications->links() }}</div>
    @endif
@endsection
