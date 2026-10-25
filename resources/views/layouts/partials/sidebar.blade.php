@php
    $user = auth()->user();

    $sections = [];

    if ($user->isJobSeeker()) {
        $sections = [
            ['label' => 'Overview', 'items' => [
                ['label' => 'Dashboard', 'route' => 'jobseeker.dashboard', 'icon' => 'home', 'active' => ['jobseeker.dashboard']],
            ]],
            ['label' => 'Job search', 'items' => [
                ['label' => 'Find jobs', 'route' => 'jobs.index', 'icon' => 'search', 'active' => ['jobs.index', 'jobs.show']],
                ['label' => 'Recommended jobs', 'route' => 'jobseeker.recommended', 'icon' => 'sparkles', 'active' => ['jobseeker.recommended']],
                ['label' => 'Saved jobs', 'route' => 'saved-jobs.index', 'icon' => 'bookmark', 'active' => ['saved-jobs.*']],
            ]],
            ['label' => 'My career', 'items' => [
                ['label' => 'My applications', 'route' => 'applications.index', 'icon' => 'clipboard', 'active' => ['applications.*']],
                ['label' => 'Interviews', 'route' => 'jobseeker.interviews', 'icon' => 'calendar', 'active' => ['jobseeker.interviews']],
                ['label' => 'My CV / Resume', 'route' => 'cvs.index', 'icon' => 'document', 'active' => ['cvs.*']],
                ['label' => 'My profile', 'route' => 'jobseeker.profile.edit', 'icon' => 'user', 'active' => ['jobseeker.profile.*']],
            ]],
            ['label' => 'Account', 'items' => [
                ['label' => 'Subscription & billing', 'route' => 'billing.index', 'icon' => 'card', 'active' => ['billing.*']],
                ['label' => 'Plans & pricing', 'route' => 'subscriptions.plans', 'icon' => 'sparkles', 'active' => ['subscriptions.plans']],
                ['label' => 'Settings', 'route' => 'settings.edit', 'icon' => 'settings', 'active' => ['settings.*']],
                ['label' => 'Help & support', 'route' => 'home', 'icon' => 'help-circle', 'active' => [], 'fragment' => 'contact'],
            ]],
        ];
    } elseif ($user->isEmployer()) {
        $sections = [
            ['label' => 'Overview', 'items' => [
                ['label' => 'Dashboard', 'route' => 'employer.dashboard', 'icon' => 'home', 'active' => ['employer.dashboard']],
            ]],
            ['label' => 'Recruitment', 'items' => [
                ['label' => 'Post a job', 'route' => 'employer.jobs.create', 'icon' => 'plus', 'active' => ['employer.jobs.create']],
                ['label' => 'Manage jobs', 'route' => 'employer.jobs.index', 'icon' => 'briefcase', 'active' => ['employer.jobs.index', 'employer.jobs.edit', 'employer.jobs.show']],
                ['label' => 'Applicants', 'route' => 'employer.applications.index', 'icon' => 'users', 'active' => ['employer.applications.*'], 'without_query' => ['status']],
                ['label' => 'Shortlisted candidates', 'route' => 'employer.applications.index', 'icon' => 'check', 'active' => ['employer.applications.*'], 'query' => ['status' => 'shortlisted']],
                ['label' => 'Interviews', 'route' => 'employer.applications.index', 'icon' => 'calendar', 'active' => ['employer.applications.*'], 'query' => ['status' => 'interview']],
                ['label' => 'Hired candidates', 'route' => 'employer.applications.index', 'icon' => 'badge-check', 'active' => ['employer.applications.*'], 'query' => ['status' => 'accepted']],
            ]],
            ['label' => 'Company', 'items' => [
                ['label' => 'Company profile', 'route' => 'employer.profile.edit', 'icon' => 'building', 'active' => ['employer.profile.*']],
                ['label' => 'Verification', 'route' => 'employer.verification', 'icon' => 'shield', 'active' => ['employer.verification']],
            ]],
            ['label' => 'Billing', 'items' => [
                ['label' => 'Subscription & billing', 'route' => 'billing.index', 'icon' => 'card', 'active' => ['billing.*']],
                ['label' => 'Plans & pricing', 'route' => 'subscriptions.plans', 'icon' => 'sparkles', 'active' => ['subscriptions.plans']],
                ['label' => 'Payments & invoices', 'route' => 'billing.index', 'icon' => 'document', 'active' => [], 'fragment' => 'payments'],
            ]],
            ['label' => 'Account', 'items' => [
                ['label' => 'Settings', 'route' => 'settings.edit', 'icon' => 'settings', 'active' => ['settings.*']],
                ['label' => 'Help & support', 'route' => 'home', 'icon' => 'help-circle', 'active' => [], 'fragment' => 'contact'],
            ]],
        ];
    } elseif ($user->isAdmin()) {
        $sections = [
            ['label' => 'Overview', 'items' => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'home', 'active' => ['admin.dashboard']],
            ]],
            ['label' => 'User management', 'items' => [
                ['label' => 'All users', 'route' => 'admin.users.index', 'icon' => 'users', 'active' => ['admin.users.*'], 'without_query' => ['role']],
                ['label' => 'Job seekers', 'route' => 'admin.users.index', 'icon' => 'user', 'active' => ['admin.users.*'], 'query' => ['role' => 'job_seeker']],
                ['label' => 'Employers', 'route' => 'admin.users.index', 'icon' => 'building', 'active' => ['admin.users.*'], 'query' => ['role' => 'employer']],
            ]],
            ['label' => 'Recruitment', 'items' => [
                ['label' => 'Post a job', 'route' => 'admin.jobs.create', 'icon' => 'plus', 'active' => ['admin.jobs.create']],
                ['label' => 'Job listings', 'route' => 'admin.jobs.index', 'icon' => 'briefcase', 'active' => ['admin.jobs.index', 'admin.jobs.show', 'admin.jobs.edit'], 'without_query' => ['status']],
                ['label' => 'Job approvals', 'route' => 'admin.jobs.index', 'icon' => 'badge-check', 'active' => ['admin.jobs.*'], 'query' => ['status' => 'pending']],
                ['label' => 'Applicants', 'route' => 'admin.applications.index', 'icon' => 'users', 'active' => ['admin.applications.*']],
            ]],
            ['label' => 'Business management', 'items' => [
                ['label' => 'Subscription plans', 'route' => 'admin.subscription-plans.index', 'icon' => 'card', 'active' => ['admin.subscription-plans.*']],
            ]],
            ['label' => 'Platform operations', 'items' => [
                ['label' => 'Audit logs', 'route' => 'admin.audit-logs.index', 'icon' => 'shield', 'active' => ['admin.audit-logs.*']],
            ]],
            ['label' => 'Account', 'items' => [
                ['label' => 'Settings', 'route' => 'settings.edit', 'icon' => 'settings', 'active' => ['settings.*']],
            ]],
        ];
    }

    $isActive = function (array $item): bool {
        if (! request()->routeIs(...$item['active'])) {
            return false;
        }

        foreach ($item['query'] ?? [] as $key => $value) {
            if ((string) request()->query($key) !== (string) $value) {
                return false;
            }
        }

        foreach ($item['without_query'] ?? [] as $key) {
            if (request()->filled($key)) {
                return false;
            }
        }

        return true;
    };

    $roleLabel = $user->isAdmin() ? 'Administrator' : ($user->isEmployer() ? 'Employer' : 'Job seeker');
    $verificationPending = $user->isEmployer() && (! $user->employerProfile || ! $user->employerProfile->isVerified());
    $initials = \Illuminate\Support\Str::of($user->name)->trim()->explode(' ')->filter()->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode('');
@endphp

<aside
    id="portal-sidebar"
    data-sidebar
    class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-brand-700 text-white shadow-xl transition-transform duration-200 ease-out lg:translate-x-0"
    aria-label="{{ $roleLabel }} navigation"
>
    <div class="flex h-16 shrink-0 items-center gap-3 border-b border-white/10 px-3">
        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2.5 rounded-lg p-1 focus-visible:outline-gold-400">
            <span class="inline-flex rounded-md bg-white p-1.5">
                <img src="{{ asset('logo.png') }}" alt="Glow Recruitment Agency" class="h-7 w-auto">
            </span>
            <span data-sidebar-brand-text class="sidebar-label truncate font-display text-sm font-bold leading-tight">Glow Recruitment</span>
        </a>

        <div class="ml-auto flex items-center gap-1">
            <button type="button" data-sidebar-collapse class="hidden rounded-lg p-2 text-white/70 hover:bg-white/10 hover:text-white lg:inline-flex" aria-label="Collapse navigation" aria-pressed="false">
                <x-icon name="panel-left" class="h-5 w-5" />
            </button>
            <button type="button" data-sidebar-close class="inline-flex rounded-lg p-2 text-white/70 hover:bg-white/10 hover:text-white lg:hidden" aria-label="Close navigation">
                <x-icon name="x" class="h-5 w-5" />
            </button>
        </div>
    </div>

    <nav class="flex-1 space-y-5 overflow-y-auto px-2.5 py-5" aria-label="{{ $roleLabel }} sections">
        @foreach($sections as $section)
            <div>
                <p class="sidebar-section-label px-3 text-[11px] font-semibold uppercase tracking-wider text-white/45">{{ $section['label'] }}</p>
                <ul class="mt-2 space-y-0.5">
                    @foreach($section['items'] as $item)
                        @php
                            $active = $isActive($item);
                            $href = route($item['route'], $item['query'] ?? []);
                            if (! empty($item['fragment'])) {
                                $href .= '#'.$item['fragment'];
                            }
                        @endphp
                        <li>
                            <a href="{{ $href }}"
                               @class([
                                   'sidebar-link group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors',
                                   'bg-gold-500 text-brand-900 shadow-sm hover:bg-gold-400' => $active,
                                   'text-white/80 hover:bg-white/10 hover:text-white' => ! $active,
                               ])
                               @if($active) aria-current="page" @endif>
                                <x-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                                <span class="sidebar-label truncate">{{ $item['label'] }}</span>
                                @if($item['route'] === 'employer.verification' && $verificationPending)
                                    <span class="sidebar-badge ml-auto rounded-full bg-gold-500 px-2 py-0.5 text-[10px] font-bold text-brand-900">Action</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach

        <div class="border-t border-white/10 pt-3">
            <a href="{{ route('home') }}" class="sidebar-link flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-white/60 transition-colors hover:bg-white/5 hover:text-white">
                <x-icon name="search" class="h-5 w-5 shrink-0" />
                <span class="sidebar-label">Public site</span>
            </a>
        </div>
    </nav>

    <div class="shrink-0 border-t border-white/10 bg-brand-900/60 p-3">
        <div class="flex items-center gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gold-500 text-sm font-bold text-brand-900" aria-hidden="true">
                {{ $initials ?: 'G' }}
            </span>
            <div class="sidebar-user-meta min-w-0 flex-1">
                <p class="truncate text-xs text-white/60">{{ $roleLabel }}</p>
            </div>
        </div>
    </div>
</aside>

<div data-sidebar-overlay class="fixed inset-0 z-40 hidden bg-brand-950/60 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>
