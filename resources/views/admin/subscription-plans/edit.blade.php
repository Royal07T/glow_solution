@extends('layouts.portal')

@section('title', 'Edit subscription plan')
@section('subtitle', 'Update pricing, availability and entitlements.')

@section('breadcrumbs')
    <a href="{{ route('admin.subscription-plans.index') }}" class="hover:text-brand-700">Subscription plans</a>
    <span aria-hidden="true">/</span>
    <span aria-current="page">{{ $plan->name }}</span>
@endsection

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        @include('admin.subscription-plans.partials.form', ['plan' => $plan])

        <section class="rounded-xl border border-red-200 bg-white p-6 shadow-sm">
            <h2 class="font-display text-base font-semibold text-brand-900">Delete plan</h2>
            <p class="mt-1 text-sm text-slate-600">
                Plans with subscribers cannot be deleted. Deactivate the plan instead to hide it from new sign-ups.
            </p>
            <form method="POST" action="{{ route('admin.subscription-plans.destroy', $plan) }}" class="mt-4" data-confirm="Delete this plan permanently?">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-red-300 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">
                    Delete plan
                </button>
            </form>
        </section>
    </div>
@endsection
