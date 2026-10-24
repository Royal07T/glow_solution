<nav class="bg-purple-900 text-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-yellow-400">Glow Recruitment</a>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:text-yellow-400">Home</a>
                    <a href="{{ route('jobs.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:text-yellow-400">Find Jobs</a>
                </div>
            </div>
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                    @if(auth()->user()->isJobSeeker())
                        <a href="{{ route('jobseeker.dashboard') }}" class="text-sm hover:text-yellow-400 mr-4">Dashboard</a>
                    @elseif(auth()->user()->isEmployer())
                        <a href="{{ route('employer.dashboard') }}" class="text-sm hover:text-yellow-400 mr-4">Dashboard</a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="text-sm hover:text-yellow-400 mr-4">Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm hover:text-yellow-400">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm hover:text-yellow-400 mr-4">Login</a>
                    <a href="{{ route('register') }}" class="bg-yellow-400 text-purple-900 px-4 py-2 rounded-md text-sm font-semibold hover:bg-yellow-300">Register</a>
                @endauth
            </div>
        </div>
    </div>
</nav>
