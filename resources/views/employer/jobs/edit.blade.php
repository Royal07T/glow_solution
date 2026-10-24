@extends('layouts.portal')

@section('title', 'Edit job')
@section('subtitle', $job->title)

@section('content')
    @if($job->status === 'approved')
        <div class="mb-6 rounded-xl border border-yellow-300 bg-yellow-50 px-5 py-4 text-sm text-yellow-900">
            This listing is live. Saving changes will unpublish it until an administrator approves the update.
        </div>
    @endif

    <form method="POST" action="{{ route('employer.jobs.update', $job) }}">
        @csrf
        @method('PUT')
        @include('employer.jobs.partials.form', ['job' => $job, 'categories' => $categories])

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Save changes</button>
            <a href="{{ route('employer.jobs.index') }}" class="rounded-lg border border-stone-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-stone-50">Cancel</a>
        </div>
    </form>
@endsection
