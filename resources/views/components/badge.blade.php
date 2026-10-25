@props([
    'variant' => 'neutral',
])

@php
    $variants = [
        'brand' => 'bg-brand-100 text-brand-800',
        'primary' => 'bg-brand-100 text-brand-800',
        'gold' => 'bg-gold-100 text-gold-700',
        'success' => 'bg-green-100 text-green-800',
        'warning' => 'bg-amber-100 text-amber-800',
        'danger' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
        'neutral' => 'bg-slate-100 text-slate-700',
        'secondary' => 'bg-slate-100 text-slate-700',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold '.($variants[$variant] ?? $variants['neutral'])]) }}>
    {{ $slot }}
</span>
