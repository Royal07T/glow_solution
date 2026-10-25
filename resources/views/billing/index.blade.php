@extends('layouts.portal')

@section('title', 'Subscription & billing')
@section('subtitle', 'Your plan, entitlements and payment history.')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Current plan</h2>
                @if($subscription)
                    <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <p class="font-display text-2xl font-bold text-brand-900">{{ $subscription->plan->name ?? 'Plan' }}</p>
                            <p class="text-sm text-slate-600">{{ $subscription->plan->description }}</p>
                        </div>
                        <x-badge :variant="$subscription->isActive() ? 'success' : 'warning'">{{ $subscription->isActive() ? 'Active' : ucfirst($subscription->status) }}</x-badge>
                    </div>
                    <dl class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Price</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $subscription->plan->is_free ? 'Free' : $subscription->plan->currency.' '.number_format((float) $subscription->plan->price) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Started</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $subscription->starts_at?->format('M j, Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Renews / ends</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $subscription->ends_at?->format('M j, Y') ?? '—' }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="mt-2 text-sm text-slate-600">You do not have a subscription yet.</p>
                    <p class="mt-1 text-sm text-slate-600">Activate a free plan below. Paid plans are arranged with the Glow Recruitment team.</p>
                @endif

                @if($subscription?->plan?->entitlements)
                    <div class="mt-5 border-t border-slate-100 pt-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Included in your plan</h3>
                        <ul class="mt-2 grid gap-2 sm:grid-cols-2">
                            @foreach($subscription->plan->entitlements as $entitlement => $value)
                                <li class="flex items-center gap-2 text-sm text-slate-700">
                                    <x-icon name="badge-check" class="h-4 w-4 text-green-700" />
                                    <span>
                                        {{ ucfirst(str_replace('_', ' ', $entitlement)) }}:
                                        <span class="font-semibold">{{ is_bool($value) ? ($value ? 'Yes' : 'No') : ($value == -1 ? 'Unlimited' : $value) }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </section>

            <section>
                <h2 class="font-display text-base font-semibold text-brand-900">Available plans</h2>
                <div class="mt-3 grid gap-4 sm:grid-cols-2">
                    @forelse($plans as $plan)
                        @php
                            $isCurrent = $subscription?->subscription_plan_id === $plan->id && $subscription?->isActive();
                        @endphp
                        <div class="flex flex-col rounded-xl border p-5 shadow-sm {{ $isCurrent ? 'border-brand-700 bg-brand-50' : 'border-slate-200 bg-white' }}">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-display text-lg font-semibold text-brand-900">{{ $plan->name }}</h3>
                                <div class="flex gap-1">
                                    @if($plan->is_free)
                                        <x-badge variant="gold">Free</x-badge>
                                    @endif
                                    @if($isCurrent)
                                        <x-badge variant="success">Current</x-badge>
                                    @endif
                                </div>
                            </div>
                            <p class="mt-1 text-sm text-slate-600">{{ $plan->description }}</p>
                            <p class="mt-3 font-display text-2xl font-bold text-brand-900">
                                {{ $plan->is_free ? 'Free' : $plan->currency.' '.number_format((float) $plan->price) }}
                                <span class="text-sm font-normal text-slate-500">/ {{ $plan->interval }}</span>
                            </p>

                            @if($plan->is_free)
                                @if($isCurrent)
                                    <span class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-green-100 px-4 py-2 text-sm font-semibold text-green-700">Your current plan</span>
                                @else
                                    <form method="POST" action="{{ route('subscriptions.activate', $plan) }}" class="mt-4">
                                        @csrf
                                        <button type="submit" class="w-full rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-800">Activate free plan</button>
                                    </form>
                                @endif
                            @else
                                <button type="button" disabled class="mt-4 w-full cursor-not-allowed rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-500" title="Paid plans are activated by the Glow team">Contact support to purchase</button>
                                <p class="mt-2 text-xs text-slate-500">Card payments for this plan are not enabled in this environment.</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-600">No plans are configured yet.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-4">
            <section id="payments" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h3 class="font-display text-base font-semibold text-brand-900">Payment history</h3>
                </div>
                @forelse($payments as $payment)
                    <div class="border-b border-slate-100 px-5 py-3 last:border-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-semibold text-slate-800">{{ $payment->currency }} {{ number_format((float) $payment->amount) }}</span>
                            <x-badge :variant="$payment->status === 'success' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning')">{{ ucfirst($payment->status) }}</x-badge>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ $payment->paid_at?->format('M j, Y') ?? $payment->created_at->format('M j, Y') }}{{ $payment->reference ? ' · '.$payment->reference : '' }}</p>
                    </div>
                @empty
                    <p class="px-5 py-6 text-sm text-slate-600">No payments recorded.</p>
                @endforelse
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-display text-base font-semibold text-brand-900">Billing help</h3>
                <p class="mt-2 text-sm text-slate-600">For invoices, upgrades or refunds contact the Glow Recruitment team. Plan changes are applied by our staff.</p>
                <a href="{{ route('settings.edit') }}" class="mt-3 inline-flex rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Account settings</a>
            </section>
        </aside>
    </div>
@endsection
