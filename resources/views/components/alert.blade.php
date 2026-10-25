@props([
    'variant' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $variants = [
        'success' => ['border-green-200', 'bg-green-50', 'text-green-800', 'badge-check'],
        'warning' => ['border-amber-200', 'bg-amber-50', 'text-amber-900', 'alert-circle'],
        'danger' => ['border-red-200', 'bg-red-50', 'text-red-800', 'alert-circle'],
        'info' => ['border-blue-200', 'bg-blue-50', 'text-blue-800', 'info'],
    ];
    [$border, $bg, $text, $icon] = $variants[$variant] ?? $variants['info'];
@endphp

<div {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-xl border $border $bg px-4 py-3 text-sm $text"]) }} role="{{ $variant === 'danger' ? 'alert' : 'status' }}">
    <x-icon :name="$icon" class="mt-0.5 h-5 w-5 shrink-0" />
    <div class="min-w-0 flex-1">
        @if($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <div @if($title) class="mt-0.5" @endif>{{ $slot }}</div>
    </div>
    @if($dismissible)
        <button type="button" data-dismiss-alert class="rounded p-1 hover:bg-black/5" aria-label="Dismiss message">
            <x-icon name="x" class="h-4 w-4" />
        </button>
    @endif
</div>
