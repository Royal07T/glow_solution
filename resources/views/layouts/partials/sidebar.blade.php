@php
    $user = auth()->user();

    $sections = [];

    if ($user->isJobSeeker()) {
        $sections = [
            ['label' => 'Overview', 'items' => [
                ['label' => 'Dashboard', 'route' => 'jobseeker.dashboard', 'icon' => 'home', 'active' => ['jobseeker.dashboard']],
            ]],
            ['label' => 'Job search', 'items' => [
                ['label' => 'Browse jobs', 'route' => 'jobs.index', 'icon' => 'search', 'active' => ['jobs.index', 'jobs.show']],
                ['label' => 'Saved jobs', 'route' => 'saved-jobs.index', 'icon' => 'bookmark', 'active' => ['saved-jobs.*']],
                ['label' => 'My applications', 'route' => 'applications.index', 'icon' => 'clipboard', 'active' => ['applications.*']],
            ]],
            ['label' => 'My career', 'items' => [
                ['label' => 'My CVs', 'route' => 'cvs.index', 'icon' => 'document', 'active' => ['cvs.*']],
                ['label' => 'My profile', 'route' => 'jobseeker.profile.edit', 'icon' => 'user', 'active' => ['jobseeker.profile.*']],
            ]],
            ['label' => 'Account', 'items' => [
                ['label' => 'Subscription', 'route' => 'subscriptions.plans', 'icon' => 'card', 'active' => ['subscriptions.*']],
                ['label' => 'Account settings', 'route' => 'settings.edit', 'icon' => 'settings', 'active' => ['settings.*']],
            ]],
        ];
    } elseif ($user->isEmployer()) {
        $sections = [
            ['label' => 'Overview', 'items' => [
                ['label' => 'Dashboard', 'route' => 'employer.dashboard', 'icon' => 'home', 'active' => ['employer.dashboard']],
            ]],
            ['label' => 'Hiring', 'items' => [
                ['label' => 'My jobs', 'route' => 'employer.jobs.index', 'icon' => 'briefcase', 'active' => ['employer.jobs.index', 'employer.jobs.edit', 'employer.jobs.show']],
                ['label' => 'Post a job', 'route' => 'employer.jobs.create', 'icon' => 'plus', 'active' => ['employer.jobs.create']],
                ['label' => 'Applications', 'route' => 'employer.applications.index', 'icon' => 'clipboard', 'active' => ['employer.applications.*']],
            ]],
            ['label' => 'Company', 'items' => [
                ['label' => 'Company profile', 'route' => 'employer.profile.edit', 'icon' => 'building', 'active' => ['employer.profile.*']],
                ['label' => 'Verification', 'route' => 'employer.verification', 'icon' => 'badge-check', 'active' => ['employer.verification']],
            ]],
            ['label' => 'Account', 'items' => [
                ['label' => 'Subscription', 'route' => 'subscriptions.plans', 'icon' => 'card', 'active' => ['subscriptions.*']],
                ['label' => 'Account settings', 'route' => 'settings.edit', 'icon' => 'settings', 'active' => ['settings.*']],
            ]],
        ];
    } elseif ($user->isAdmin()) {
        $sections = [
            ['label' => 'Overview', 'items' => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home', 'active' => ['admin.dashboard']],
            ]],
            ['label' => 'Moderation', 'items' => [
                ['label' => 'Job listings', 'route' => 'admin.jobs.index', 'icon' => 'briefcase', 'active' => ['admin.jobs.*']],
                ['label' => 'Employer verification', 'route' => 'admin.verifications.index', 'icon' => 'badge-check', 'active' => ['admin.verifications.*']],
            ]],
            ['label' => 'People', 'items' => [
                ['label' => 'Users', 'route' => 'admin.users.index', 'icon' => 'users', 'active' => ['admin.users.*']],
            ]],
            ['label' => 'Oversight', 'items' => [
                ['label' => 'Audit log', 'route' => 'admin.audit-logs.index', 'icon' => 'shield', 'active' => ['admin.audit-logs.*']],
            ]],
            ['label' => 'Account', 'items' => [
                ['label' => 'Account settings', 'route' => 'settings.edit', 'icon' => 'settings', 'active' => ['settings.*']],
            ]],
        ];
    }

    $isActive = fn (array $item): bool => request()->routeIs(...$item['active']);

    $roleLabel = $user->isAdmin() ? 'Administrator' : ($user->isEmployer() ? 'Employer' : 'Job seeker');
    $verificationPending = $user->isEmployer() && (! $user->employerProfile || ! $user->employerProfile->isVerified());
@endphp

<aside
    data-sidebar
    class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-brand-950 text-white transition-transform duration-200 ease-out lg:translate-x-0"
    aria-label="Account navigation"
>
    <div class="flex h-16 shrink-0 items-center gap-2 border-b border-white/10 px-5">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gold-500" aria-hidden="true">
                <span class="font-display text-lg font-bold text-brand-950">G</span>
            </span>
            <span class="font-display text-base font-bold">Glow Recruitment</span>
        </a>
        <button type="button" data-sidebar-toggle class="ml-auto rounded-lg p-1.5 text-white/70 hover:bg-white/10 hover:text-white lg:hidden" aria-label="Close navigation">
            <x-icon name="menu" class="h-5 w-5" />
        </button>
    </div>

    <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
        @foreach($sections as $section)
            <div>
                <p class="px-3 text-[11px] font-semibold uppercase tracking-wider text-white/40">{{ $section['label'] }}</p>
                <ul class="mt-2 space-y-1">
                    @foreach($section['items'] as $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                               @class([
                                   'group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                                   'bg-white/10 text-gold-400' => $isActive($item),
                                   'text-white/75 hover:bg-white/5 hover:text-white' => ! $isActive($item),
                               ])
                               @if($isActive($item)) aria-current="page" @endif>
                                <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                                <span>{{ $item['label'] }}</span>
                                @if($item['route'] === 'employer.verification' && $verificationPending)
                                    <span class="ml-auto rounded-full bg-gold-500 px-2 py-0.5 text-[10px] font-bold text-brand-950">Action</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        <div class="border-t border-white/10 pt-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-white/60 transition-colors hover:bg-white/5 hover:text-white">
                <x-icon name="search" class="h-5 w-5 shrink-0" />
                <span>Public site</span>
            </a>
        </div>
    </nav>

    <div class="shrink-0 border-t border-white/10 p-4">
        <p class="truncate text-sm font-semibold">{{ $user->name }}</p>
        <p class="mb-3 truncate text-xs text-white/50">{{ $roleLabel }}</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center gap-2 rounded-lg border border-white/15 px-3 py-2 text-sm font-medium text-white/80 transition-colors hover:border-white/30 hover:bg-white/5 hover:text-white">
                <x-icon name="logout" class="h-4 w-4" />
                Sign out
            </button>
        </form>
    </div>
</aside>

<div data-sidebar-overlay class="fixed inset-0 z-40 hidden bg-slate-900/60 lg:hidden" aria-hidden="true"></div>
