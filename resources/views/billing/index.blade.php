@extends('layouts.portal')

@section('title', 'Subscription & billing')
@section('subtitle', 'Plans, entitlements and payment history.')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-stone-200 bg-white p-6">
                <h2 class="font-display text-base font-semibold text-slate-900">Current plan</h2>
                @if($subscription)
                    <div class="mt-3 flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <p class="font-display text-2xl font-bold text-slate-900">{{ $subscription->plan->name }}</p>
                            <p class="text-sm text-slate-600">{{ $subscription->plan->description }}</p>
                        </div>
                        <x-badge variant="success">Active</x-badge>
                    </div>
                    <dl class="mt-4 grid gap-3 sm:grid-cols-3">
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Price</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $subscription->plan->is_free ? 'Free' : $subscription->plan->currency.' '.number_format((float) $subscription->plan->price) }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Started</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $subscription->starts_at?->format('M j, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-slate-500">Renews</dt>
                            <dd class="text-sm font-medium text-slate-800">{{ $subscription->ends_at?->format('M j, Y') }}</dd>
                        </div>
                    </dl>
                @else
                    <p class="mt-2 text-sm text-slate-600">You do not have an active subscription.</p>
                    <p class="mt-1 text-sm text-slate-600">Activate a free plan below or contact support to purchase a paid plan.</p>
                @endif

                @if($subscription?->plan?->entitlements)
                    <div class="mt-5 border-t border-stone-200 pt-4">
                        <h3 class="text-xs font-semibold uppercase tracking-wide text-slate-500">Included</h3>
                        <ul class="mt-2 grid gap-2 sm:grid-cols-2">
                            @foreach($subscription->plan->entitlements as $entitlement => $value)
                                <li class="flex items-center gap-2 text-sm text-slate-700">
                                    <x-icon name="badge-check" class="h-4 w-4 text-green-700" />
                                    <span>
                                        {{ ucfirst(str_replace('_', ' ', $entitlement)) }}:
                                        <span class="font-semibold">
                                            {{ is_bool($value) ? ($value ? 'Yes' : 'No') : ($value == -1 ? 'Unlimited' : $value) }}
                                        </span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </section>

            <section>
                <h2 class="font-display text-base font-semibold text-slate-900">Available plans</h2>
                <div class="mt-3 grid gap-4 sm:grid-cols-2">
                    @forelse($plans as $plan)
                        <div class="flex flex-col rounded-xl border p-5 {{ $subscription?->subscription_plan_id === $plan->id ? 'border-[#062B52 bg-[#F5F3FF' : 'border-stone-200 bg-white' }}">
                            <div class="flex items-start justify-between gap-2">
                                <h3 class="font-display text-lg font-semibold text-slate-900">{{ $plan->name }}</h3>
                                @if($plan->is_free)
                                    <x-badge variant="gold">Free</x-badge>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-slate-600">{{ $plan->description }}</p>
                            <p class="mt-3 font-display text-2xl font-bold text-slate-900">
                                {{ $plan->is_free ? 'Free' : $plan->currency.' '.number_format((float) $plan->price) }}
                                <span class="text-sm font-normal text-slate-500">/ {{ $plan->interval }}</span>
                            </p>

                            <div class="mt-4 flex-1">
                                <form method="POST" action="{{ route('subscriptions.activate', $plan) }}">
                                    @csrf
                                    <button type="submit"
                                        @disabled($plan->is_free && $subscription)
                                        class="w-full rounded-lg px-4 py-2 text-sm font-semibold {{ $plan->is_free && ! $subscription ? 'bg-[#062B52 text-white hover:bg-[#5B21B6' : 'cursor-not-allowed bg-stone-100 text-slate-400' }}">
                                        {{ $subscription ? ($subscription->subscription_plan_id === $plan->id ? 'Current plan' : 'Plan active') : ($plan->is_free ? 'Activate free plan' : 'Contact support') }}
                                    </button>
                                </form>
                                @unless($plan->is_free)
                                    <p class="mt-2 text-xs text-slate-500">Card payments are not enabled in this environment yet.</p>
                                @endunless
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-600">No plans are configured yet.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-stone-200 bg-white">
                <div class="border-b border-stone-200 px-5 py-4">
                    <h3 class="font-display text-base font-semibold text-slate-900">Payment history</h3>
                </div>
                @forelse($payments as $payment)
                    <div class="border-b border-stone-100 px-5 py-3 last:border-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-semibold text-slate-800">{{ $payment->currency }} {{ number_format((float) $payment->amount) }}</span>
                            <x-badge :variant="$payment->status === 'success' ? 'success' : ($payment->status === 'failed' ? 'danger' : 'warning')">{{ ucfirst($payment->status) }}</x-badge>
                        </div>
                        <p class="mt-1 text-xs text-slate-500">{{ $payment->paid_at?->format('M j, Y') ?? $payment->created_at->format('M j, Y') }} · {{ $payment->reference }}</p>
                    </div>
                @empty
                    <p class="px-5 py-6 text-sm text-slate-600">No payments recorded.</p>
                @endforelse
            </section>

            <section class="rounded-xl border border-stone-200 bg-white p-5">
                <h3 class="font-display text-base font-semibold text-slate-900">Billing help</h3>
                <p class="mt-2 text-sm text-slate-600">
                    For invoices, upgrades or refunds contact the Glow Recruitment team. Plan changes are applied server-side only.
                </p>
            </section>
        </aside>
    </div>
@endsection
