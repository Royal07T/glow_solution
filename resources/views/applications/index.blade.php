@extends('layouts.portal')

@section('title', 'My applications')
@section('subtitle', 'Track every application and its current status.')

@section('content')
    <div class="space-y-5">
        <div class="-mx-1 flex gap-2 overflow-x-auto px-1 pb-1">
            <a href="{{ route('applications.index') }}" @class([
                'inline-flex shrink-0 items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition',
                'border-brand-700 bg-brand-700 text-white' => ! request('status'),
                'border-slate-300 bg-white text-slate-700 hover:bg-slate-50' => (bool) request('status'),
            ])>
                All
                <span class="rounded-full bg-black/10 px-2 py-0.5 text-xs">{{ $counts->sum() }}</span>
            </a>
            @foreach($statuses as $status)
                <a href="{{ route('applications.index', ['status' => $status->value]) }}" @class([
                    'inline-flex shrink-0 items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition',
                    'border-brand-700 bg-brand-700 text-white' => request('status') === $status->value,
                    'border-slate-300 bg-white text-slate-700 hover:bg-slate-50' => request('status') !== $status->value,
                ])>
                    {{ $status->label() }}
                    <span class="rounded-full bg-black/10 px-2 py-0.5 text-xs">{{ $counts[$status->value] ?? 0 }}</span>
                </a>
            @endforeach
        </div>

        @if($applications->isEmpty())
            <x-empty-state icon="clipboard" title="{{ request('status') ? 'No applications with this status' : 'No applications yet' }}" message="{{ request('status') ? 'Try another status filter or view all applications.' : 'Browse live vacancies and submit your first application.' }}">
                @if(request('status'))
                    <a href="{{ route('applications.index') }}" class="inline-flex rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">View all applications</a>
                @endif
                <a href="{{ route('jobs.index') }}" class="inline-flex rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Find jobs</a>
            </x-empty-state>
        @else
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th scope="col" class="px-5 py-3 font-medium">Role</th>
                                <th scope="col" class="px-5 py-3 font-medium">Employer</th>
                                <th scope="col" class="px-5 py-3 font-medium">Applied</th>
                                <th scope="col" class="px-5 py-3 font-medium">Status</th>
                                <th scope="col" class="px-5 py-3 text-right font-medium">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($applications as $application)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-5 py-4">
                                        <a href="{{ route('jobs.show', $application->job) }}" class="font-semibold text-brand-900 hover:underline">{{ $application->job->title }}</a>
                                        <p class="text-xs text-slate-500">{{ $application->job->location }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">{{ $application->job->employerProfile->company_name ?? 'Employer' }}</td>
                                    <td class="px-5 py-4 text-slate-500">{{ ($application->applied_at ?? $application->created_at)->format('M j, Y') }}</td>
                                    <td class="px-5 py-4"><x-badge :variant="$application->statusVariant()">{{ $application->statusLabel() }}</x-badge></td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap items-center justify-end gap-3">
                                            <a href="{{ route('applications.show', $application) }}" class="font-semibold text-brand-700 hover:underline">View details</a>
                                            @if($application->canBeWithdrawn())
                                                <form method="POST" action="{{ route('applications.withdraw', $application) }}" data-confirm="Withdraw this application? This cannot be undone.">
                                                    @csrf
                                                    <button type="submit" class="text-sm font-medium text-danger hover:underline">Withdraw</button>
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

            <div>{{ $applications->links() }}</div>
        @endif
    </div>
@endsection
