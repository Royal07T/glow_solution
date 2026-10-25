@props([
    'label' => null,
    'name' => null,
    'value' => null,
    'help' => null,
    'required' => false,
    'rows' => 4,
])

@php
    $id = $attributes->get('id') ?? $name;
    $hasError = $name && $errors->has($name);
@endphp

<div {{ $attributes->only('class')->merge(['class' => '']) }}>
    @if($label)
        <label for="{{ $id }}" class="mb-1 block text-sm font-medium text-slate-700">
            {{ $label }}
            @if($required)<span class="text-danger" aria-hidden="true">*</span>@endif
        </label>
    @endif
    <textarea
        @if($name) name="{{ $name }}" @endif
        id="{{ $id }}"
        rows="{{ $rows }}"
        @if($required) required @endif
        @if($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
        {{ $attributes->except(['class', 'id'])->merge(['class' => 'w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 '.($hasError ? 'border-red-400' : 'border-slate-300')]) }}
    >@if($name){{ old($name, $value) }}@else{{ $slot }}@endif</textarea>
    @if($help)
        <p class="mt-1 text-xs text-slate-500">{{ $help }}</p>
    @endif
    @if($hasError)
        <p id="{{ $id }}-error" class="mt-1 text-sm text-danger">{{ $errors->first($name) }}</p>
    @endif
</div>
