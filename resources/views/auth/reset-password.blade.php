@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md px-4 py-16 sm:px-6">
        <div class="rounded-2xl border border-stone-200 bg-white p-8 shadow-sm">
            <h1 class="font-display text-2xl font-bold text-slate-900">Choose a new password</h1>

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email address</label>
                    <input id="email" name="email" type="email" required value="{{ old('email', $request->email) }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                    @error('email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">New password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                    <p class="mt-1 text-xs text-slate-500">At least 12 characters with upper and lower case and a number.</p>
                    @error('password') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Confirm new password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                </div>

                <button type="submit" class="w-full rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                    Reset password
                </button>
            </form>
        </div>
    </div>
@endsection
