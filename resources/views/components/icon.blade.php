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
        @case('shield-check')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 2.75 4.5 5.5v6c0 4.4 3.05 8.06 7.5 9.75 4.45-1.69 7.5-5.35 7.5-9.75v-6L12 2.75Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4" />
            @break
        @case('external-link')
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 5.25h5.25V10.5M18.75 5.25 10.5 13.5" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 13.5v4.25a1.75 1.75 0 0 1-1.75 1.75H6.25a1.75 1.75 0 0 1-1.75-1.75V7a1.75 1.75 0 0 1 1.75-1.75H10.5" />
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
        @case('map-pin')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s6.75-6.15 6.75-11.25a6.75 6.75 0 1 0-13.5 0C5.25 14.85 12 21 12 21Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" />
            @break
        @case('clock')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5V12l3 1.5" />
            @break
        @case('calendar')
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 3v3M16.5 3v3M3.75 8.25h16.5M4.5 5.25h15A.75.75 0 0 1 20.25 6v13.5a.75.75 0 0 1-.75.75h-15a.75.75 0 0 1-.75-.75V6a.75.75 0 0 1 .75-.75Z" />
            @break
        @case('check')
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 5.25 5.25L19.5 6" />
            @break
        @case('x')
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            @break
        @case('alert-circle')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18ZM12 8.25v4.5M12 16.5h.008v.008H12V16.5Z" />
            @break
        @case('info')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18ZM12 10.5V16.5M12 7.5h.008v.008H12V7.5Z" />
            @break
        @case('chevron-left')
            <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
            @break
        @case('chevron-right')
            <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6" />
            @break
        @case('chevron-down')
            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
            @break
        @case('chevron-up')
            <path stroke-linecap="round" stroke-linejoin="round" d="m6 15 6-6 6 6" />
            @break
        @case('arrow-right')
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0-6-6m6 6-6 6" />
            @break
        @case('mail')
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5v10.5H3.75V6.75Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 7.5 8.25 6 8.25-6" />
            @break
        @case('phone')
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25A2.25 2.25 0 0 1 6.75 3h1.5c.4 0 .75.26.87.64l1.1 3.28a.9.9 0 0 1-.21.94L8.7 9.18a12.5 12.5 0 0 0 6.12 6.12l1.32-1.31a.9.9 0 0 1 .94-.21l3.28 1.1c.38.12.64.47.64.87v1.5A2.25 2.25 0 0 1 18.75 19.5h-.75A14.25 14.25 0 0 1 4.5 5.25Z" />
            @break
        @case('download')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4.5-4.5M12 15l-4.5-4.5M4.5 19.5h15" />
            @break
        @case('trash')
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15M9.75 3.75h4.5M6.75 6.75l.75 13.5h9l.75-13.5M10.5 10.5v6M13.5 10.5v6" />
            @break
        @case('eye')
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
            @break
        @case('send')
            <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 3.75 17 8.25-17 8.25 2.25-6.75L15 12 5.99 9.75l-2.24-6Z" />
            @break
        @case('sparkles')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3M12 18v3M3 12h3M18 12h3M5.64 5.64l2.12 2.12M16.24 16.24l2.12 2.12M18.36 5.64l-2.12 2.12M7.76 16.24l-2.12 2.12" />
            @break
        @case('filter')
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5l-6.75 8.25v6l-3-1.5v-4.5L3.75 5.25Z" />
            @break
        @case('help-circle')
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.6 9a2.4 2.4 0 1 1 3.4 2.16c-.6.3-1 .9-1 1.59V13.5M12 16.5h.008v.008H12V16.5Z" />
            @break
        @case('panel-left')
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 5.25h16.5v13.5H3.75V5.25ZM9.75 5.25v13.5" />
            @break
        @default
            <circle cx="12" cy="12" r="9" />
    @endswitch
</svg>
