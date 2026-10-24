@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow-md p-8">
        <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $job->type === 'domestic' ? 'bg-yellow-100 text-yellow-800' : 'bg-purple-100 text-purple-800' }} mb-4">
            {{ ucfirst($job->type) }}
        </span>
        <h1 class="text-3xl font-bold text-purple-900 mb-4">{{ $job->title }}</h1>
        <p class="text-xl text-gray-600 mb-4">{{ $job->employerProfile->company_name ?? $job->employerProfile->contact_person }}</p>
        <p class="text-gray-500 mb-6">📍 {{ $job->location }}</p>

        @if($job->salary_min)
            <div class="bg-green-50 p-4 rounded-lg mb-6">
                <p class="text-green-700 font-semibold text-lg">
                    Salary: {{ $job->salary_currency }} {{ number_format($job->salary_min) }}{{ $job->salary_max ? ' - ' . number_format($job->salary_max) : '' }}
                    {{ $job->salary_negotiable ? '(Negotiable)' : '' }}
                </p>
            </div>
        @endif

        <div class="mb-6">
            <h2 class="text-xl font-semibold text-purple-900 mb-3">Job Description</h2>
            <div class="prose max-w-none text-gray-700">
                {!! nl2br(e($job->description)) !!}
            </div>
        </div>

        @if($job->responsibilities)
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-purple-900 mb-3">Responsibilities</h2>
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($job->responsibilities)) !!}
                </div>
            </div>
        @endif

        @if($job->qualifications)
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-purple-900 mb-3">Qualifications</h2>
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($job->qualifications)) !!}
                </div>
            </div>
        @endif

        @if($job->benefits)
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-purple-900 mb-3">Benefits</h2>
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($job->benefits)) !!}
                </div>
            </div>
        @endif

        <div class="border-t pt-6">
            @auth
                @if(auth()->user()->isJobSeeker())
                    <a href="{{ route('applications.create', $job) }}" class="bg-purple-900 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-purple-800 inline-block">
                        Apply Now
                    </a>
                @else
                    <p class="text-gray-600">Only job seekers can apply for jobs.</p>
                @endif
            @else
                <a href="{{ route('login') }}" class="bg-purple-900 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-purple-800 inline-block">
                    Login to Apply
                </a>
            @endauth
        </div>
    </div>
</div>
@endsection
