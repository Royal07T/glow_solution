@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md px-4 py-16 sm:px-6">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <h1 class="font-display text-2xl font-bold text-slate-900">Choose a new password</h1>

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email address</label>
                    <input id="email" name="email" type="email" required value="{{ old('email', $request->email) }}" class="w-full rounded-lg border border-slate-300 px-3 py-2">
                    @error('email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <x-password-input
                    label="New password"
                    name="password"
                    id="password"
                    autocomplete="new-password"
                    help="At least 12 characters with upper and lower case and a number."
                    :required="true"
                />

                <x-password-input
                    label="Confirm new password"
                    name="password_confirmation"
                    id="password_confirmation"
                    autocomplete="new-password"
                    :required="true"
                />

                <button type="submit" class="w-full rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">
                    Reset password
                </button>
            </form>
        </div>
    </div>
@endsection
