@extends('layouts.portal')

@section('title', 'Edit job')
@section('subtitle', $job->title)

@section('content')
    <form method="POST" action="{{ route('admin.jobs.update', $job) }}">
        @csrf
        @method('PUT')
        @include('employer.jobs.partials.form', ['job' => $job, 'categories' => $categories])

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">Save changes</button>
            <a href="{{ route('admin.jobs.show', $job) }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
@endsection
