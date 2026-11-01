@props([
    'value' => null,
    'label' => 'information',
    'empty' => '—',
])

@if(filled($value))
    <span data-sensitive class="inline-flex max-w-full items-center gap-1.5 align-middle">
        <span data-sensitive-masked aria-hidden="true" class="font-mono tracking-widest text-slate-500">********</span>
        <span data-sensitive-actual class="hidden break-all">{{ $value }}</span>
        <button
            type="button"
            data-sensitive-toggle
            data-sensitive-label="{{ $label }}"
            aria-label="Show {{ $label }}"
            aria-pressed="false"
            title="Show {{ $label }}"
            class="inline-flex h-6 w-6 shrink-0 items-center justify-center rounded text-slate-400 transition-colors hover:bg-slate-100 hover:text-brand-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold-400"
        >
            <x-icon name="eye" data-sensitive-eye-open class="h-4 w-4" />
            <x-icon name="eye-off" data-sensitive-eye-off class="hidden h-4 w-4" />
        </button>
    </span>
@else
    <span class="text-slate-600">{{ $empty }}</span>
@endif
