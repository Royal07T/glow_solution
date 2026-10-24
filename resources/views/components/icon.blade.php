@props([
    'name' => 'circle',
    'class' => 'h-5 w-5',
])

<svg {{ $attributes->merge(['class' => $class, 'fill' => 'none', 'viewBox' => '0 0 24 24', 'stroke-width' => 1.8, 'stroke' => 'currentColor', 'aria-hidden' => 'true']) }}>
    @switch($name)
        @case('home')
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5M5.25 9.75V20.25h5.25v-5.25h3v5.25h5.25V9.75" />
            @break
        @case('search')
            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
            @break
        @case('briefcase')
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8.25A1.75 1.75 0 0 1 4.75 6.5h14.5A1.75 1.75 0 0 1 21 8.25v9.5a1.75 1.75 0 0 1-1.75 1.75H4.75A1.75 1.75 0 0 1 3 17.75v-9.5Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.75 6.5V5a1.75 1.75 0 0 1 1.75-1.75h3A1.75 1.75 0 0 1 15.25 5v1.5M3 12.25h18" />
            @break
        @case('clipboard')
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5h6M9 4.5V3.75A1.75 1.75 0 0 1 10.75 2h2.5A1.75 1.75 0 0 1 15 3.75V4.5M9 4.5H6.75A1.75 1.75 0 0 0 5 6.25v13A1.75 1.75 0 0 0 6.75 21h10.5A1.75 1.75 0 0 0 19 19.25v-13A1.75 1.75 0 0 0 17.25 4.5H15" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 11h7M8.5 14.5h7M8.5 18h4" />
            @break
        @case('bookmark')
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.75h10.5A.75.75 0 0 1 18 4.5v16.25l-6-3.75-6 3.75V4.5a.75.75 0 0 1 .75-.75Z" />
            @break
        @case('document')
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.25 2.25H7.5A1.75 1.75 0 0 0 5.75 4v16A1.75 1.75 0 0 0 7.5 21.75h9A1.75 1.75 0 0 0 18.25 20V6.25l-4-4Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M14 2.5v4.25h4.25M8.75 12h6.5M8.75 15.5h6.5" />
            @break
        @case('user')
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 21a7.5 7.5 0 0 1 15 0" />
            @break
        @case('users')
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 8.25a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM3.75 20.25a7.5 7.5 0 0 1 12 0M17.25 5.75a3 3 0 0 1 0 5.9M18.75 20.25a6.7 6.7 0 0 0-1.6-4.35" />
            @break
        @case('badge-check')
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.6 12.4 2.15 2.15 4.65-4.65" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2.75c1.7 0 3.06 1.2 3.63 2.62 1.5.28 2.72 1.5 3 3 1.42.57 2.62 1.93 2.62 3.63s-1.2 3.06-2.62 3.63c-.28 1.5-1.5 2.72-3 3-.57 1.42-1.93 2.62-3.63 2.62s-3.06-1.2-3.63-2.62c-1.5-.28-2.72-1.5-3-3-1.42-.57-2.62-1.93-2.62-3.63s1.2-3.06 2.62-3.63c.28-1.5 1.5-2.72 3-3C8.94 3.95 10.3 2.75 12 2.75Z" />
            @break
        @case('building')
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M5.25 21V5.25A1.5 1.5 0 0 1 6.75 3.75h6a1.5 1.5 0 0 1 1.5 1.5V21M14.25 9h3A1.5 1.5 0 0 1 18.75 10.5V21M8.25 7.5h3M8.25 11.25h3M8.25 15h3M8.25 18.75h3" />
            @break
        @case('adjustments')
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6.75h9M4.5 6.75h2.25M10.5 17.25h9M4.5 17.25h2.25M6.75 4.5v4.5M17.25 15v4.5M14.25 12h5.25M4.5 12h5.25" />
            @break
        @case('shield')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2.75 4.5 5.5v6c0 4.4 3.05 8.06 7.5 9.75 4.45-1.69 7.5-5.35 7.5-9.75v-6L12 2.75Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4" />
            @break
        @case('card')
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.75 7.25A1.75 1.75 0 0 1 4.5 5.5h15a1.75 1.75 0 0 1 1.75 1.75v9.5a1.75 1.75 0 0 1-1.75 1.75h-15a1.75 1.75 0 0 1-1.75-1.75v-9.5Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.75 9.5h18.5M6.25 15h3.5" />
            @break
        @case('chart')
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 20.25h16.5M6.75 20.25V13.5M12 20.25V4.5M17.25 20.25v-9" />
            @break
        @case('plus')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15M4.5 12h15" />
            @break
        @case('settings')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.6 3.6a2.4 2.4 0 0 1 4.8 0l.2.9c.9.3 1.7.8 2.4 1.4l.9-.3a2.4 2.4 0 0 1 2.4 4.2l-.6.7c.1.5.1 1 0 1.5l.6.7a2.4 2.4 0 0 1-2.4 4.2l-.9-.3c-.7.6-1.5 1.1-2.4 1.4l-.2.9a2.4 2.4 0 0 1-4.8 0l-.2-.9c-.9-.3-1.7-.8-2.4-1.4l-.9.3a2.4 2.4 0 0 1-2.4-4.2l.6-.7a6.7 6.7 0 0 1 0-1.5l-.6-.7A2.4 2.4 0 0 1 5.5 6.2l.9.3c.7-.6 1.5-1.1 2.4-1.4l.2-.9Z" />
            @break
        @case('logout')
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A1.75 1.75 0 0 0 14 3.5H5.75A1.75 1.75 0 0 0 4 5.25v13.5a1.75 1.75 0 0 0 1.75 1.75H14a1.75 1.75 0 0 0 1.75-1.75V15M9.75 12h10.5m0 0-3-3m3 3-3 3" />
            @break
        @case('menu')
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
            @break
        @default
            <circle cx="12" cy="12" r="9" />
    @endswitch
</svg>
