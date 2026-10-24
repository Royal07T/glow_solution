@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-lg px-4 py-16 sm:px-6">
        <div class="rounded-2xl border border-stone-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-800">
                <x-icon name="shield" class="h-6 w-6" />
            </div>
            <h1 class="mt-4 font-display text-2xl font-bold text-slate-900">Verify your email address</h1>
            <p class="mt-2 text-sm text-slate-600">
                We sent a verification link to <span class="font-semibold">{{ auth()->user()->email }}</span>.
                Please open it to activate your account.
            </p>

            @if(session('status') === 'verification-link-sent')
                <p class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">
                    A new verification link has been sent.
                </p>
            @endif

            <form method="POST" action="{{ route('verification.send') }}" class="mt-6">
                @csrf
                <button type="submit" class="w-full rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                    Resend verification email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="text-sm font-semibold text-slate-600 hover:text-slate-900">Sign out</button>
            </form>
        </div>
    </div>
@endsection
