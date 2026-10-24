<nav class="bg-white border-b border-stone-200 sticky top-0 z-40" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-brand-800 rounded-lg flex items-center justify-center">
                        <span class="text-gold-500 font-display font-bold text-lg">G</span>
                    </div>
                    <span class="font-display font-bold text-xl text-slate-900 hidden sm:block">Glow Recruitment</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="{{ route('jobs.index') }}" class="text-slate-600 hover:text-brand-800 font-medium transition-colors">Find Jobs</a>
                <a href="{{ route('home') }}#for-employers" class="text-slate-600 hover:text-brand-800 font-medium transition-colors">For Employers</a>
                <a href="{{ route('home') }}#pricing" class="text-slate-600 hover:text-brand-800 font-medium transition-colors">Pricing</a>
                
                @auth
                    @if(auth()->user()->isJobSeeker())
                        <a href="{{ route('jobseeker.dashboard') }}" class="text-slate-600 hover:text-brand-800 font-medium transition-colors">Dashboard</a>
                    @elseif(auth()->user()->isEmployer())
                        <a href="{{ route('employer.dashboard') }}" class="text-slate-600 hover:text-brand-800 font-medium transition-colors">Dashboard</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-slate-600 hover:text-brand-800 font-medium transition-colors">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-slate-600 hover:text-brand-800 font-medium transition-colors">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-slate-600 hover:text-brand-800 font-medium transition-colors">Login</a>
                    <x-button href="{{ route('register') }}" variant="primary">Get Started</x-button>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen" type="button" class="text-slate-600 hover:text-brand-800 p-2" aria-label="Toggle menu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div x-show="mobileMenuOpen" @click.away="mobileMenuOpen = false" class="md:hidden border-t border-stone-200 bg-white">
        <div class="px-4 pt-2 pb-3 space-y-1">
            <a href="{{ route('jobs.index') }}" class="block px-3 py-2 text-slate-600 hover:text-brand-800 hover:bg-brand-50 rounded-lg font-medium">Find Jobs</a>
            <a href="{{ route('home') }}#for-employers" class="block px-3 py-2 text-slate-600 hover:text-brand-800 hover:bg-brand-50 rounded-lg font-medium">For Employers</a>
            <a href="{{ route('home') }}#pricing" class="block px-3 py-2 text-slate-600 hover:text-brand-800 hover:bg-brand-50 rounded-lg font-medium">Pricing</a>
            
            @auth
                @if(auth()->user()->isJobSeeker())
                    <a href="{{ route('jobseeker.dashboard') }}" class="block px-3 py-2 text-slate-600 hover:text-brand-800 hover:bg-brand-50 rounded-lg font-medium">Dashboard</a>
                @elseif(auth()->user()->isEmployer())
                    <a href="{{ route('employer.dashboard') }}" class="block px-3 py-2 text-slate-600 hover:text-brand-800 hover:bg-brand-50 rounded-lg font-medium">Dashboard</a>
                @elseif(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-slate-600 hover:text-brand-800 hover:bg-brand-50 rounded-lg font-medium">Admin</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-slate-600 hover:text-brand-800 hover:bg-brand-50 rounded-lg font-medium">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="block px-3 py-2 text-slate-600 hover:text-brand-800 hover:bg-brand-50 rounded-lg font-medium">Login</a>
                <a href="{{ route('register') }}" class="block px-3 py-2 bg-brand-800 text-white rounded-lg font-medium text-center mt-2">Get Started</a>
            @endauth
        </div>
    </div>
</nav>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
