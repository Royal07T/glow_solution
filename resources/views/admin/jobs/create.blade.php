@extends('layouts.portal')

@section('title', 'Post a job')
@section('subtitle', 'Publish a listing as Glow Recruitment Agency.')

@section('content')
    <form method="POST" action="{{ route('admin.jobs.store') }}">
        @csrf
        @include('employer.jobs.partials.form', ['job' => null, 'categories' => $categories])

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">Publish job</button>
            <a href="{{ route('admin.jobs.index') }}" class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
        </div>
    </form>
@endsection
