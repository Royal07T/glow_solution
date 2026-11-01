@extends('layouts.portal')

@section('title', 'Create administrator')
@section('subtitle', 'Add another administrator with full access to the admin dashboard.')

@section('breadcrumbs')
    <a href="{{ route('admin.users.index') }}" class="hover:text-brand-700">Users</a>
    <span aria-hidden="true">/</span>
    <span aria-current="page">Create administrator</span>
@endsection

@section('content')
    <div class="mx-auto max-w-3xl">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Administrator details</h2>
                <p class="mt-1 text-sm text-slate-600">The new administrator can sign in immediately with the password you set here.</p>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="mb-1 block text-sm font-medium text-slate-700">First name <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="first_name" type="text" name="first_name" required maxlength="100" autocomplete="given-name" value="{{ old('first_name') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        @error('first_name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="last_name" class="mb-1 block text-sm font-medium text-slate-700">Last name <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="last_name" type="text" name="last_name" required maxlength="100" autocomplete="family-name" value="{{ old('last_name') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        @error('last_name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email address <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="email" type="email" name="email" required maxlength="255" autocomplete="email" value="{{ old('email') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        @error('email') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="mb-1 block text-sm font-medium text-slate-700">Phone number</label>
                        <input id="phone" type="text" name="phone" maxlength="20" autocomplete="tel" value="{{ old('phone') }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        @error('phone') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        <p class="mt-1 text-xs text-slate-500">At least 8 characters with upper and lower case letters, a number and a symbol.</p>
                        @error('password') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Confirm password <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        @error('password_confirmation') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800">Create administrator</button>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
            </div>
        </form>
    </div>
@endsection
