@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();
        $dashboardRoute = $user->isAdmin()
            ? 'admin.dashboard'
            : ($user->isEmployer() ? 'employer.dashboard' : 'jobseeker.dashboard');
    @endphp

    <div class="mx-auto max-w-lg px-4 py-16 sm:px-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-50 text-green-700">
                <x-icon name="badge-check" class="h-6 w-6" />
            </div>
            <h1 class="mt-4 font-display text-2xl font-bold text-slate-900">Email verified</h1>
            <p class="mt-2 text-sm leading-6 text-slate-600">
                Your email address <span class="font-semibold text-slate-900">{{ $user->email }}</span> has been verified successfully.
                You can now access all features available to your account.
            </p>

            <a href="{{ route($dashboardRoute) }}" class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-brand-600">
                Continue to dashboard
                <x-icon name="arrow-right" class="h-4 w-4" />
            </a>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Sign out</button>
            </form>
        </div>
    </div>
@endsection
