@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-purple-900 mb-8">Admin Dashboard</h1>
    
    <div class="grid md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm text-gray-600">Total Users</p>
            <p class="text-2xl font-bold text-purple-900">{{ $stats['total_users'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm text-gray-600">Job Seekers</p>
            <p class="text-2xl font-bold text-purple-900">{{ $stats['job_seekers'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm text-gray-600">Employers</p>
            <p class="text-2xl font-bold text-purple-900">{{ $stats['employers'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <p class="text-sm text-gray-600">Pending Jobs</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_jobs'] }}</p>
        </div>
    </div>

    <div class="grid md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-purple-900">Pending Job Approvals</h2>
                <a href="{{ route('admin.jobs.index') }}" class="text-purple-700 hover:text-yellow-600">View All</a>
            </div>
            @forelse($pendingJobs as $job)
                <div class="border-b border-gray-200 py-3 last:border-0">
                    <h3 class="font-semibold">{{ $job->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $job->employerProfile->user->name ?? '' }}</p>
                    <div class="mt-2 flex gap-2">
                        <form method="POST" action="{{ route('admin.jobs.approve', $job) }}" class="inline">
                            @csrf
                            <button class="text-xs bg-green-600 text-white px-3 py-1 rounded">Approve</button>
                        </form>
                        <form method="POST" action="{{ route('admin.jobs.reject', $job) }}" class="inline">
                            @csrf
                            <input type="hidden" name="admin_notes" value="Does not meet requirements">
                            <button class="text-xs bg-red-600 text-white px-3 py-1 rounded">Reject</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No pending jobs.</p>
            @endforelse
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-purple-900">Recent Users</h2>
                <a href="{{ route('admin.users.index') }}" class="text-purple-700 hover:text-yellow-600">View All</a>
            </div>
            @forelse($recentUsers as $user)
                <div class="border-b border-gray-200 py-3 last:border-0">
                    <h3 class="font-semibold">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    <p class="text-xs text-gray-500 mt-1">Role: {{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
                </div>
            @empty
                <p class="text-gray-500">No users yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
