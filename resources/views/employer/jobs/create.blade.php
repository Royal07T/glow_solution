@extends('layouts.portal')

@section('title', 'Post a job')
@section('subtitle', 'New and edited listings are reviewed before they go live.')

@section('content')
    <form method="POST" action="{{ route('employer.jobs.store') }}">
        @csrf
        @include('employer.jobs.partials.form', ['job' => null, 'categories' => $categories])

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="rounded-lg bg-[#062B52] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#07335c]">Submit for approval</button>
            <a href="{{ route('employer.jobs.index') }}" class="rounded-lg border border-stone-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-stone-50">Cancel</a>
        </div>
    </form>
@endsection
