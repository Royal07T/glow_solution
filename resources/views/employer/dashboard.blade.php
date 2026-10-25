@extends('layouts.portal')

@section('title', 'Employer Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-purple-900 mb-8">Employer Dashboard</h1>
    
    @if(!$profile || !$profile->isVerified())
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
            <strong>Verification Required:</strong> Your account needs to be verified before you can post jobs. <a href="{{ route('employer.verification') }}" class="underline">Submit verification</a>
        </div>
    @endif

    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('employer.jobs.create') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition text-center">
            <div class="text-3xl mb-2">➕</div>
            <h3 class="font-semibold text-purple-900">Post Job</h3>
        </a>
        <a href="{{ route('employer.jobs.index') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition text-center">
            <div class="text-3xl mb-2">📋</div>
            <h3 class="font-semibold text-purple-900">My Jobs</h3>
        </a>
        <a href="{{ route('employer.applications.index') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition text-center">
            <div class="text-3xl mb-2">👥</div>
            <h3 class="font-semibold text-purple-900">Applications</h3>
        </a>
        <a href="{{ route('employer.verification') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition text-center">
            <div class="text-3xl mb-2">✅</div>
            <h3 class="font-semibold text-purple-900">Verification</h3>
        </a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold text-purple-900 mb-4">Recent Job Postings</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Job Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Applications</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Posted</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jobs as $job)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $job->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $job->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                       ($job->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       ($job->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($job->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $job->applications_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $job->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No jobs posted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
