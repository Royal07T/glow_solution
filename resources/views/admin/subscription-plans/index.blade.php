@extends('layouts.portal')

@section('title', 'Subscription plans')
@section('subtitle', 'Manage pricing and entitlements for job seekers and employers.')

@section('content')
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-slate-600">Plans and prices are stored in the database and shown to users on the billing page.</p>
        <a href="{{ route('admin.subscription-plans.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">
            <x-icon name="plus" class="h-4 w-4" />
            Add plan
        </a>
    </div>

    @if($plans->isEmpty())
        <x-empty-state icon="card" title="No plans configured" message="Create a plan to start offering subscriptions to job seekers and employers." />
    @else
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th scope="col" class="px-5 py-3 font-medium">Plan</th>
                            <th scope="col" class="px-5 py-3 font-medium">Audience</th>
                            <th scope="col" class="px-5 py-3 font-medium">Price</th>
                            <th scope="col" class="px-5 py-3 font-medium">Entitlements</th>
                            <th scope="col" class="px-5 py-3 font-medium">Subscribers</th>
                            <th scope="col" class="px-5 py-3 font-medium">Status</th>
                            <th scope="col" class="px-5 py-3 text-right font-medium">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($plans as $plan)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-brand-900">{{ $plan->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $plan->slug }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <x-badge variant="neutral">{{ ucfirst(str_replace('_', ' ', $plan->target_role ?? 'All roles')) }}</x-badge>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-800">{{ $plan->is_free ? 'Free' : $plan->currency.' '.number_format((float) $plan->price) }}</p>
                                    <p class="text-xs text-slate-500">per {{ $plan->interval }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    @if(is_array($plan->entitlements) && count($plan->entitlements))
                                        <ul class="space-y-0.5 text-xs text-slate-600">
                                            @foreach($plan->entitlements as $key => $value)
                                                <li>{{ ucfirst(str_replace('_', ' ', $key)) }}: <span class="font-semibold">{{ is_bool($value) ? ($value ? 'Yes' : 'No') : ($value == -1 ? 'Unlimited' : $value) }}</span></li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-xs text-slate-400">None</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-slate-700">{{ $plan->subscriptions_count }}</td>
                                <td class="px-5 py-4">
                                    <x-badge :variant="$plan->is_active ? 'success' : 'secondary'">{{ $plan->is_active ? 'Active' : 'Inactive' }}</x-badge>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('admin.subscription-plans.edit', $plan) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        <x-icon name="settings" class="h-3.5 w-3.5" />
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
