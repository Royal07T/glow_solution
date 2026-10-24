@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'loading' => false,
])

@php
$baseClasses = 'inline-flex items-center justify-center px-6 py-3 font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed min-h-[44px]';

$variants = [
    'primary' => 'bg-brand-800 hover:bg-brand-700 text-white focus:ring-brand-500',
    'gold' => 'bg-gold-500 hover:bg-gold-600 text-brand-950 focus:ring-gold-400',
    'secondary' => 'bg-white hover:bg-gray-50 text-slate-900 border border-stone-300 focus:ring-brand-500',
    'ghost' => 'bg-transparent hover:bg-gray-100 text-slate-700 focus:ring-brand-500',
    'danger' => 'bg-danger hover:bg-red-700 text-white focus:ring-red-500',
];
@endphp

@if($href && !$disabled)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $baseClasses . ' ' . $variants[$variant]]) }}>
        @if($loading)
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $baseClasses . ' ' . $variants[$variant]]) }}>
        @if($loading)
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        @endif
        {{ $slot }}
    </button>
@endif
