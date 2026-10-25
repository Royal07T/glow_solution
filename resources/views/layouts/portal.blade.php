<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="font-sans antialiased bg-stone-50">
    <a href="#portal-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-[60] focus:px-4 focus:py-2 focus:bg-[#062B52 focus:text-white focus:rounded-lg">Skip to content</a>

    @include('layouts.partials.sidebar')

    <div class="min-h-screen lg:pl-64">
        <header class="sticky top-0 z-30 border-b border-stone-200 bg-white/95 backdrop-blur">
            <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
                <button type="button" data-sidebar-toggle class="rounded-lg p-2 text-slate-600 hover:bg-stone-100 hover:text-[#062B52 lg:hidden" aria-label="Open navigation" aria-expanded="false">
                    <x-icon name="menu" class="h-6 w-6" />
                </button>

                <div class="min-w-0 flex-1">
                    <h1 class="truncate font-display text-lg font-bold text-slate-900">@yield('title', $title ?? 'Dashboard')</h1>
                    @hasSection('subtitle')
                        <p class="hidden truncate text-xs text-slate-500 sm:block">@yield('subtitle')</p>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    @php
                        $portalUser = auth()->user();
                        $roleLabel = $portalUser->isAdmin() ? 'Admin' : ($portalUser->isEmployer() ? 'Employer' : 'Job seeker');
                    @endphp
                    <span class="hidden rounded-full bg-[#F5F3FF px-3 py-1 text-xs font-semibold text-[#062B52 sm:inline">{{ $roleLabel }}</span>
                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#062B52 text-sm font-semibold text-white" aria-hidden="true">
                        {{ Str::of($portalUser->name)->trim()->explode(' ')->map(fn ($part) => Str::substr($part, 0, 1))->take(2)->implode('') }}
                    </span>
                </div>
            </div>
        </header>

        <main id="portal-content" class="px-4 py-6 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-7xl">
                @include('layouts.partials.flash')

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
