@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md px-4 py-16 sm:px-6">
        <div class="rounded-2xl border border-stone-200 bg-white p-8 shadow-sm">
            <h1 class="font-display text-2xl font-bold text-slate-900">Reset your password</h1>
            <p class="mt-1 text-sm text-slate-600">Enter your account email and we will send a secure reset link.</p>

            @if(session('status'))
                <p class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email address</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                    @error('email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                    Send reset link
                </button>
            </form>

            <p class="mt-6 text-center text-sm">
                <a href="{{ route('login') }}" class="font-semibold text-brand-800 hover:underline">Back to sign in</a>
            </p>
        </div>
    </div>
@endsection
