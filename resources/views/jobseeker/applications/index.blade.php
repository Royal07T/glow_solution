@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-purple-900 mb-8">My Applications</h1>
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applied</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($applications as $application)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('jobs.show', $application->job) }}" class="text-purple-900 font-semibold hover:text-yellow-600">{{ $application->job->title }}</a>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $application->job->employerProfile->company_name ?? '' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $application->applied_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($application->status === 'submitted')
                                <form method="POST" action="{{ route('applications.withdraw', $application) }}" onsubmit="return confirm('Withdraw application?')">
                                    @csrf
                                    <button class="text-red-600 hover:text-red-900 text-sm">Withdraw</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No applications yet. <a href="{{ route('jobs.index') }}" class="text-purple-700">Browse jobs</a></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $applications->links() }}</div>
</div>
@endsection
