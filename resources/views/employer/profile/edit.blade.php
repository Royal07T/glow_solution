@extends('layouts.portal')

@section('title', 'Company profile')
@section('subtitle', 'These details are shown on your job listings.')

@section('content')
    <form method="POST" action="{{ route('employer.profile.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="font-display text-base font-semibold text-slate-900">Company details</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="type" class="mb-1 block text-sm font-medium text-slate-700">Account type</label>
                    <select id="type" name="type" required class="w-full rounded-lg border border-stone-300 px-3 py-2">
                        <option value="business" @selected(old('type', $profile->type ?? 'business') === 'business')>Business</option>
                        <option value="household" @selected(old('type', $profile->type) === 'household')>Household</option>
                    </select>
                </div>
                <div>
                    <label for="company_name" class="mb-1 block text-sm font-medium text-slate-700">Company name <span class="text-slate-400">(business)</span></label>
                    <input id="company_name" type="text" name="company_name" maxlength="255" value="{{ old('company_name', $profile->company_name) }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                </div>
                <div>
                    <label for="contact_person" class="mb-1 block text-sm font-medium text-slate-700">Contact person</label>
                    <input id="contact_person" type="text" name="contact_person" required maxlength="255" value="{{ old('contact_person', $profile->contact_person) }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                    @error('contact_person') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="business_registration_number" class="mb-1 block text-sm font-medium text-slate-700">Business registration number</label>
                    <input id="business_registration_number" type="text" name="business_registration_number" maxlength="100" value="{{ old('business_registration_number', $profile->business_registration_number) }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="font-display text-base font-semibold text-slate-900">Address</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="address" class="mb-1 block text-sm font-medium text-slate-700">Street address</label>
                    <input id="address" type="text" name="address" maxlength="255" value="{{ old('address', $profile->address) }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                </div>
                <div>
                    <label for="city" class="mb-1 block text-sm font-medium text-slate-700">City</label>
                    <input id="city" type="text" name="city" maxlength="100" value="{{ old('city', $profile->city) }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                </div>
                <div>
                    <label for="state" class="mb-1 block text-sm font-medium text-slate-700">State</label>
                    <input id="state" type="text" name="state" maxlength="100" value="{{ old('state', $profile->state) }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                </div>
                <div>
                    <label for="country" class="mb-1 block text-sm font-medium text-slate-700">Country</label>
                    <input id="country" type="text" name="country" maxlength="100" value="{{ old('country', $profile->country) }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="font-display text-base font-semibold text-slate-900">About the company</h2>
            <div class="mt-4">
                <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                <textarea id="description" name="description" rows="5" maxlength="2000" class="w-full rounded-lg border border-stone-300 px-3 py-2">{{ old('description', $profile->description) }}</textarea>
            </div>
        </section>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="rounded-lg bg-brand-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Save company profile</button>
            <a href="{{ route('employer.dashboard') }}" class="rounded-lg border border-stone-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-stone-50">Cancel</a>
        </div>
    </form>
@endsection
