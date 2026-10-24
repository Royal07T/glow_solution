@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-purple-900 mb-8">Job Seeker Dashboard</h1>
    
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <a href="{{ route('jobseeker.profile.edit') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition text-center">
            <div class="text-3xl mb-2">👤</div>
            <h3 class="font-semibold text-purple-900">My Profile</h3>
        </a>
        <a href="{{ route('cvs.index') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition text-center">
            <div class="text-3xl mb-2">📄</div>
            <h3 class="font-semibold text-purple-900">My CVs</h3>
        </a>
        <a href="{{ route('applications.index') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition text-center">
            <div class="text-3xl mb-2">📋</div>
            <h3 class="font-semibold text-purple-900">My Applications</h3>
        </a>
        <a href="{{ route('jobs.index') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition text-center">
            <div class="text-3xl mb-2">🔍</div>
            <h3 class="font-semibold text-purple-900">Browse Jobs</h3>
        </a>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-purple-900 mb-4">Recent Applications</h2>
            @forelse($applications as $application)
                <div class="border-b border-gray-200 py-3 last:border-0">
                    <h3 class="font-semibold">{{ $application->job->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $application->job->employerProfile->company_name ?? '' }}</p>
                    <p class="text-xs text-gray-500 mt-1">Status: <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $application->status)) }}</span></p>
                </div>
            @empty
                <p class="text-gray-500">No applications yet.</p>
            @endforelse
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold text-purple-900 mb-4">Saved Jobs</h2>
            @forelse($savedJobs as $job)
                <div class="border-b border-gray-200 py-3 last:border-0">
                    <h3 class="font-semibold">{{ $job->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $job->employerProfile->company_name ?? '' }}</p>
                    <a href="{{ route('jobs.show', $job) }}" class="text-xs text-purple-700 hover:text-yellow-600">View Job →</a>
                </div>
            @empty
                <p class="text-gray-500">No saved jobs yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
