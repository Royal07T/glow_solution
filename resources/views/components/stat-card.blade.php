@props([
    'label' => null,
    'value' => null,
    'icon' => 'chart',
    'href' => null,
    'hint' => null,
    'tone' => 'brand',
])

@php
    $tones = [
        'brand' => 'bg-brand-50 text-brand-700',
        'gold' => 'bg-gold-100 text-gold-700',
        'success' => 'bg-green-50 text-green-700',
        'info' => 'bg-blue-50 text-blue-700',
        'warning' => 'bg-amber-50 text-amber-700',
        'danger' => 'bg-red-50 text-red-700',
    ];
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @endif
    {{ $attributes->merge(['class' => 'group flex items-start gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-brand-200 hover:shadow-md focus-visible:border-brand-300']) }}
>
    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg {{ $tones[$tone] ?? $tones['brand'] }}" aria-hidden="true">
        <x-icon :name="$icon" class="h-5 w-5" />
    </span>
    <span class="min-w-0 flex-1">
        <span class="block text-sm font-medium text-slate-600">{{ $label }}</span>
        <span class="mt-1 block font-display text-2xl font-bold text-brand-900">
            {{ $value ?? $slot }}
        </span>
        @if($hint)
            <span class="mt-1 block text-xs text-slate-500">{{ $hint }}</span>
        @endif
    </span>
</{{ $tag }}>
