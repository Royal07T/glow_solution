@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md px-4 py-16 sm:px-6">
        <div class="rounded-2xl border border-stone-200 bg-white p-8 shadow-sm">
            <h1 class="font-display text-2xl font-bold text-slate-900">Confirm your password</h1>
            <p class="mt-1 text-sm text-slate-600">This is a protected area. Please confirm your password to continue.</p>

            <form method="POST" action="{{ route('password.confirm.store') }}" class="mt-6 space-y-5">
                @csrf
                <div>
                    <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                    @error('password') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full rounded-lg bg-[#062B52 px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#5B21B6">Confirm</button>
            </form>
        </div>
    </div>
@endsection
