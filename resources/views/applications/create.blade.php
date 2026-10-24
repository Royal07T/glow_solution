@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-purple-900 mb-8">Apply for Job</h1>
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-semibold text-purple-900">{{ $job->title }}</h2>
        <p class="text-gray-600">{{ $job->employerProfile->company_name ?? '' }}</p>
        <p class="text-gray-500">📍 {{ $job->location }}</p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <form method="POST" action="{{ route('applications.store', $job) }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select CV</label>
                <select name="cv_id" class="w-full border border-gray-300 rounded-md px-4 py-2">
                    <option value="">No CV</option>
                    @foreach($cvs as $cv)
                        <option value="{{ $cv->id }}">{{ $cv->name }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1"><a href="{{ route('cvs.index') }}" class="text-purple-700">Upload a CV</a> if you haven't already</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Cover Letter (Optional)</label>
                <textarea name="cover_letter" rows="6" class="w-full border border-gray-300 rounded-md px-4 py-2" placeholder="Tell the employer why you're a good fit..."></textarea>
            </div>
            <button type="submit" class="bg-purple-900 text-white px-6 py-2 rounded-md hover:bg-purple-800">Submit Application</button>
            <a href="{{ route('jobs.show', $job) }}" class="ml-4 text-gray-600 hover:text-gray-800">Cancel</a>
        </form>
    </div>
</div>
@endsection
