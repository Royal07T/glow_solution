@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-purple-900 mb-8">Find Jobs</h1>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <form method="GET" action="{{ route('jobs.index') }}" class="grid md:grid-cols-5 gap-4">
            <input type="text" name="keyword" placeholder="Keyword" value="{{ request('keyword') }}" class="border border-gray-300 rounded-md px-4 py-2">
            <select name="category" class="border border-gray-300 rounded-md px-4 py-2">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="type" class="border border-gray-300 rounded-md px-4 py-2">
                <option value="">All Types</option>
                <option value="domestic" {{ request('type') == 'domestic' ? 'selected' : '' }}>Domestic</option>
                <option value="professional" {{ request('type') == 'professional' ? 'selected' : '' }}>Professional</option>
            </select>
            <select name="employment_type" class="border border-gray-300 rounded-md px-4 py-2">
                <option value="">Employment Type</option>
                <option value="full_time" {{ request('employment_type') == 'full_time' ? 'selected' : '' }}>Full Time</option>
                <option value="part_time" {{ request('part_time') == 'part_time' ? 'selected' : '' }}>Part Time</option>
                <option value="contract" {{ request('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
            </select>
            <button type="submit" class="bg-purple-900 text-white px-6 py-2 rounded-md hover:bg-purple-800">Filter</button>
        </form>
    </div>

    <!-- Jobs List -->
    <div class="space-y-4">
        @forelse($jobs as $job)
            <div class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $job->type === 'domestic' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800' }} mb-2">
                            {{ ucfirst($job->type) }}
                        </span>
                        <h2 class="text-xl font-semibold text-purple-900 mb-2">{{ $job->title }}</h2>
                        <p class="text-gray-600 mb-2">{{ $job->employerProfile->company_name ?? $job->employerProfile->contact_person }}</p>
                        <p class="text-gray-500 mb-2">📍 {{ $job->location }}</p>
                        <p class="text-gray-700 mb-3">{{ Str::limit(strip_tags($job->description), 150) }}</p>
                        @if($job->salary_min)
                            <p class="text-green-600 font-semibold">
                                {{ $job->salary_currency }} {{ number_format($job->salary_min) }}{{ $job->salary_max ? ' - ' . number_format($job->salary_max) : '' }}
                            </p>
                        @endif
                    </div>
                    <a href="{{ route('jobs.show', $job) }}" class="bg-purple-900 text-white px-6 py-2 rounded-md hover:bg-purple-800">View Details</a>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-lg">
                <p class="text-gray-500">No jobs found matching your criteria.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $jobs->links() }}
    </div>
</div>
@endsection
