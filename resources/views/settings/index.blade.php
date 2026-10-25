@extends('layouts.portal')

@section('title', 'Account settings')
@section('subtitle', 'Manage your sign-in details and password.')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm" aria-labelledby="profile-heading">
                <h2 id="profile-heading" class="font-display text-base font-semibold text-brand-900">Profile information</h2>
                <p class="mt-1 text-sm text-slate-600">Your name is visible to employers you interact with.</p>

                <form method="POST" action="{{ route('user-profile-information.update') }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Full name <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="name" type="text" name="name" required maxlength="255" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        @error('name', 'updateProfileInformation') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email address <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="email" type="email" name="email" required maxlength="255" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        @error('email', 'updateProfileInformation') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-slate-500">Changing your email requires verification via the link sent to the new address.</p>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500">Save changes</button>
                </form>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm" aria-labelledby="password-heading">
                <h2 id="password-heading" class="font-display text-base font-semibold text-brand-900">Change password</h2>
                <p class="mt-1 text-sm text-slate-600">Use at least 12 characters with upper and lower case letters and a number.</p>

                <form method="POST" action="{{ route('user-password.update') }}" class="mt-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="current_password" class="mb-1 block text-sm font-medium text-slate-700">Current password <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="current_password" type="password" name="current_password" required autocomplete="current-password" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        @error('current_password', 'updatePassword') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="mb-1 block text-sm font-medium text-slate-700">New password <span class="text-danger" aria-hidden="true">*</span></label>
                            <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                            @error('password', 'updatePassword') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Confirm new password <span class="text-danger" aria-hidden="true">*</span></label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        </div>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500">Update password</button>
                </form>
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="account-heading">
                <h3 id="account-heading" class="font-display text-base font-semibold text-brand-900">Account</h3>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Role</dt>
                        <dd class="text-right font-medium text-slate-800">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Email verified</dt>
                        <dd class="text-right font-medium text-slate-800">{{ $user->email_verified_at ? 'Yes' : 'No' }}</dd>
                    </div>
                    @if($user->phone)
                        <div class="flex justify-between gap-3">
                            <dt class="text-slate-500">Phone</dt>
                            <dd class="text-right font-medium text-slate-800">{{ $user->phone }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Last login</dt>
                        <dd class="text-right font-medium text-slate-800">{{ $user->last_login_at?->diffForHumans() ?? '—' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm" aria-labelledby="security-heading">
                <h3 id="security-heading" class="font-display text-base font-semibold text-brand-900">Security</h3>
                <p class="mt-2 text-sm text-slate-600">Sessions are invalidated when you change your password. Signing out ends this session immediately.</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-500">Sign out</button>
                </form>
            </section>
        </aside>
    </div>
@endsection
