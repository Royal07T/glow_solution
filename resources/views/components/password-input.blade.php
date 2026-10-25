@props([
    'label' => null,
    'name',
    'id' => null,
    'value' => null,
    'autocomplete' => 'current-password',
    'required' => false,
    'help' => null,
    'errorBag' => null,
])

@php
    $id = $id ?? $name;
    $bag = $errorBag ? $errors->getBag($errorBag) : $errors;
    $hasError = $bag->has($name);
    $message = $bag->first($name);
@endphp

<div>
    @if($label)
        <label for="{{ $id }}" class="mb-1 block text-sm font-medium text-slate-700">
            {{ $label }}
            @if($required)<span class="text-danger" aria-hidden="true">*</span>@endif
        </label>
    @endif

    <div class="relative">
        <input
            type="password"
            @if($name) name="{{ $name }}" @endif
            id="{{ $id }}"
            @if($name) value="{{ old($name, $value) }}" @endif
            @if($required) required @endif
            autocomplete="{{ $autocomplete }}"
            @if($hasError) aria-invalid="true" aria-describedby="{{ $id }}-error" @endif
            {{ $attributes->merge(['class' => 'w-full rounded-lg border bg-white px-3 py-2 pr-11 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 '.($hasError ? 'border-red-400 focus:border-red-500 focus:ring-red-500/30' : 'border-slate-300 focus:border-brand-500 focus:ring-brand-500/30')]) }}
        >
        <button
            type="button"
            data-password-toggle="{{ $id }}"
            aria-label="Show password"
            aria-pressed="false"
            class="absolute inset-y-0 right-0 flex w-11 items-center justify-center rounded-r-lg text-slate-400 transition-colors hover:text-brand-700 focus-visible:text-brand-700"
        >
            <x-icon name="eye" data-eye-open class="h-5 w-5" />
            <x-icon name="eye-off" data-eye-off class="hidden h-5 w-5" />
        </button>
    </div>

    @if($help)
        <p class="mt-1 text-xs text-slate-500">{{ $help }}</p>
    @endif

    @if($hasError)
        <p id="{{ $id }}-error" class="mt-1 text-sm text-danger">{{ $message }}</p>
    @endif
</div>
