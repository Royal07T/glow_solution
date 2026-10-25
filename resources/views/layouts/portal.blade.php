<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body class="font-sans antialiased bg-brand-50 text-slate-700">
    <a href="#portal-content" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-brand-700 focus:px-4 focus:py-2 focus:text-white">Skip to content</a>

    @include('layouts.partials.sidebar')

    <div data-portal-main class="min-h-screen transition-[padding] duration-200 lg:pl-64">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="flex h-16 items-center gap-3 px-4 sm:px-6 lg:px-8">
                <button type="button" data-sidebar-toggle class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 hover:text-brand-700 lg:hidden" aria-label="Open navigation" aria-expanded="false" aria-controls="portal-sidebar">
                    <x-icon name="menu" class="h-6 w-6" />
                </button>

                <div class="min-w-0 flex-1">
                    @hasSection('breadcrumbs')
                        <nav aria-label="Breadcrumb" class="mb-0.5 hidden text-xs text-slate-500 sm:block">
                            @yield('breadcrumbs')
                        </nav>
                    @endif
                    <h1 class="truncate font-display text-lg font-bold text-brand-900">@yield('title', $title ?? 'Dashboard')</h1>
                    @hasSection('subtitle')
                        <p class="hidden truncate text-xs text-slate-500 sm:block">@yield('subtitle')</p>
                    @endif
                </div>

                <div class="flex items-center gap-2 sm:gap-3">
                    @php
                        $portalUser = auth()->user();
                        $roleLabel = $portalUser->isAdmin() ? 'Admin' : ($portalUser->isEmployer() ? 'Employer' : 'Job seeker');
                        $initials = \Illuminate\Support\Str::of($portalUser->name)->trim()->explode(' ')->filter()->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode('');
                    @endphp
                    <span class="hidden rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 sm:inline">{{ $roleLabel }}</span>

                    <details data-menu class="relative">
                        <summary class="flex cursor-pointer items-center gap-2 rounded-full p-1 pr-2 hover:bg-slate-100" aria-label="Open account menu">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-700 text-sm font-semibold text-white" aria-hidden="true">{{ $initials ?: 'G' }}</span>
                            <x-icon name="chevron-down" class="h-4 w-4 text-slate-500" />
                        </summary>
                        <div class="absolute right-0 mt-2 w-56 rounded-xl border border-slate-200 bg-white py-2 shadow-lg">
                            <div class="border-b border-slate-100 px-4 pb-2">
                                <p class="truncate text-sm font-semibold text-brand-900">{{ $portalUser->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $portalUser->email }}</p>
                            </div>
                            <a href="{{ route('settings.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                <x-icon name="settings" class="h-4 w-4" /> Account settings
                            </a>
                            @unless($portalUser->isAdmin())
                                <a href="{{ route('billing.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <x-icon name="card" class="h-4 w-4" /> Subscription & billing
                                </a>
                            @endunless
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 pt-1">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm font-medium text-danger hover:bg-red-50">
                                    <x-icon name="logout" class="h-4 w-4" /> Sign out
                                </button>
                            </form>
                        </div>
                    </details>
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
