@props([
    'icon' => 'briefcase',
    'title' => 'Nothing here yet',
    'message' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center']) }}>
    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-700" aria-hidden="true">
        <x-icon :name="$icon" class="h-6 w-6" />
    </span>
    <h3 class="mt-4 font-display text-base font-semibold text-brand-900">{{ $title }}</h3>
    @if($message)
        <p class="mt-1 max-w-md text-sm text-slate-600">{{ $message }}</p>
    @endif
    @if(trim($slot) !== '')
        <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
