@props([
    'title' => null,
    'description' => null,
    'padding' => 'p-6',
])

<section {{ $attributes->merge(['class' => 'rounded-xl border border-slate-200 bg-white shadow-sm '.$padding]) }}>
    @if($title || $description || isset($actions))
        <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
            <div>
                @if($title)
                    <h2 class="font-display text-base font-semibold text-brand-900">{{ $title }}</h2>
                @endif
                @if($description)
                    <p class="mt-1 text-sm text-slate-600">{{ $description }}</p>
                @endif
            </div>
            @if(isset($actions))
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endif
        </div>
    @endif
    {{ $slot }}
</section>
