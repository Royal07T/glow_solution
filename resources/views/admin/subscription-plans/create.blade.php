@extends('layouts.portal')

@section('title', 'New subscription plan')
@section('subtitle', 'Define pricing and entitlements for a new plan.')

@section('breadcrumbs')
    <a href="{{ route('admin.subscription-plans.index') }}" class="hover:text-brand-700">Subscription plans</a>
    <span aria-hidden="true">/</span>
    <span aria-current="page">New plan</span>
@endsection

@section('content')
    <div class="mx-auto max-w-4xl">
        @include('admin.subscription-plans.partials.form', ['plan' => $plan])
    </div>
@endsection
