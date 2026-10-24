@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md px-4 py-16 sm:px-6">
        <div class="rounded-2xl border border-stone-200 bg-white p-8 shadow-sm">
            <h1 class="font-display text-2xl font-bold text-slate-900">Two-factor authentication</h1>
            <p class="mt-1 text-sm text-slate-600">Enter the code from your authenticator app, or use one of your recovery codes.</p>

            <form method="POST" action="{{ route('two-factor.login.store') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="code" class="mb-1 block text-sm font-medium text-slate-700">Authentication code</label>
                    <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                    @error('code') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="recovery_code" class="mb-1 block text-sm font-medium text-slate-700">Or recovery code</label>
                    <input id="recovery_code" name="recovery_code" type="text" autocomplete="off" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                    @error('recovery_code') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full rounded-lg bg-brand-800 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Verify</button>
            </form>
        </div>
    </div>
@endsection
