@extends('layouts.portal')

@section('title', 'Account settings')
@section('subtitle', 'Manage your sign-in details and password.')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-stone-200 bg-white p-6">
                <h2 class="font-display text-base font-semibold text-slate-900">Profile information</h2>
                <p class="mt-1 text-sm text-slate-600">Your name is visible to employers you interact with.</p>

                <form method="POST" action="{{ route('user-profile-information.update') }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Full name</label>
                        <input id="name" type="text" name="name" required maxlength="255" value="{{ old('name', $user->name) }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                        @error('name', 'updateProfileInformation') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email address</label>
                        <input id="email" type="email" name="email" required maxlength="255" value="{{ old('email', $user->email) }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                        @error('email', 'updateProfileInformation') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-slate-500">
                            Changing your email requires verification via the link sent to the new address.
                        </p>
                    </div>
                    <button type="submit" class="rounded-lg bg-[#062B52 px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#5B21B6">Save changes</button>
                </form>
            </section>

            <section class="rounded-xl border border-stone-200 bg-white p-6">
                <h2 class="font-display text-base font-semibold text-slate-900">Change password</h2>
                <p class="mt-1 text-sm text-slate-600">Use at least 12 characters with upper and lower case letters and a number.</p>

                <form method="POST" action="{{ route('user-password.update') }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="current_password" class="mb-1 block text-sm font-medium text-slate-700">Current password</label>
                        <input id="current_password" type="password" name="current_password" required autocomplete="current-password" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                        @error('current_password', 'updatePassword') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="password" class="mb-1 block text-sm font-medium text-slate-700">New password</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                            @error('password', 'updatePassword') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Confirm new password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                        </div>
                    </div>
                    <button type="submit" class="rounded-lg bg-[#062B52 px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#5B21B6">Update password</button>
                </form>
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-stone-200 bg-white p-5">
                <h3 class="font-display text-base font-semibold text-slate-900">Account</h3>
                <dl class="mt-3 space-y-2 text-sm">
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Role</dt>
                        <dd class="text-right font-medium text-slate-800">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Email verified</dt>
                        <dd class="text-right font-medium text-slate-800">{{ $user->email_verified_at ? 'Yes' : 'No' }}</dd>
                    </div>
                    <div class="flex justify-between gap-3">
                        <dt class="text-slate-500">Last login</dt>
                        <dd class="text-right font-medium text-slate-800">{{ $user->last_login_at?->diffForHumans() ?? '—' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="rounded-xl border border-stone-200 bg-white p-5">
                <h3 class="font-display text-base font-semibold text-slate-900">Security</h3>
                <p class="mt-2 text-sm text-slate-600">
                    Sessions are invalidated when you change your password. Signing out ends this session immediately.
                </p>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full rounded-lg border border-stone-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-stone-50">Sign out</button>
                </form>
            </section>
        </div>
    </div>
@endsection
