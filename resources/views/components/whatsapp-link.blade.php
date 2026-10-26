@props(['message' => null])

<a
    href="{{ \App\Support\Company::whatsappUrl($message) }}"
    target="_blank"
    rel="noopener noreferrer"
    {{ $attributes->merge(['class' => 'inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700']) }}
>
    <x-icon name="phone" class="h-4 w-4" />
    <span>{{ $slot }}</span>
</a>
