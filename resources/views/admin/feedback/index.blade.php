@extends('layouts.portal')

@section('title', 'Feedback')
@section('subtitle', 'Review feedback submitted by job seekers, employers and other users.')

@section('content')
    <form method="GET" action="{{ route('admin.feedback.index') }}" class="mb-5 grid gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="sm:col-span-2">
            <label for="search" class="mb-1 block text-xs font-medium text-slate-600">Search</label>
            <input id="search" type="text" name="search" value="{{ request('search') }}" placeholder="Subject, message, name or email" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
        </div>
        <div>
            <label for="type" class="mb-1 block text-xs font-medium text-slate-600">Type</label>
            <select id="type" name="type" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">Any type</option>
                @foreach(\App\Models\Feedback::TYPES as $type)
                    <option value="{{ $type }}" @selected(request('type') === $type)>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">Filter</button>
            <a href="{{ route('admin.feedback.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <div class="space-y-4">
        @forelse($feedback as $item)
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-badge variant="brand">{{ $item->typeLabel() }}</x-badge>
                            @if($item->rating)
                                <x-badge variant="gold">{{ $item->rating }}/5</x-badge>
                            @endif
                        </div>
                        <h2 class="mt-2 font-display text-base font-semibold text-brand-900">{{ $item->subject ?: 'Untitled feedback' }}</h2>
                        <p class="mt-1 text-xs text-slate-500">
                            {{ $item->user->name }} · {{ $item->user->email }} · {{ $item->created_at->format('M j, Y g:ia') }}
                        </p>
                    </div>

                    @if($item->application)
                        <a href="{{ route('admin.applications.show', $item->application) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">View application</a>
                    @elseif($item->job)
                        <a href="{{ route('admin.jobs.show', $item->job) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">View job</a>
                    @endif
                </div>

                @if($item->job)
                    <p class="mt-3 text-sm text-slate-600">
                        <span class="font-medium text-slate-800">{{ $item->job->title }}</span>
                        · {{ $item->job->employerProfile->company_name ?? 'Employer' }}
                    </p>
                @endif

                <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-slate-700">{{ $item->message }}</p>
            </article>
        @empty
            <x-empty-state icon="send" title="No feedback yet" message="Feedback submitted by users will appear here." />
        @endforelse
    </div>

    <div class="mt-6">{{ $feedback->links() }}</div>
@endsection
