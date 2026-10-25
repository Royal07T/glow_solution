<footer class="bg-brand-900 text-white mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <span class="inline-flex items-center justify-center rounded-lg bg-white p-2">
                        <img src="{{ asset('logo.png') }}" alt="Glow Recruitment Agency Solution" class="h-10 w-auto">
                    </span>
                    <span class="font-display font-bold text-xl">Glow Recruitment Agency Solution</span>
                </div>
                <p class="text-gray-300 mb-4 max-w-md">
                    Trusted recruitment marketplace connecting families with domestic staff and professionals with career opportunities across Nigeria.
                </p>
            </div>
            
            <div>
                <h3 class="font-display font-semibold text-lg mb-4">For Job Seekers</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('jobs.index') }}" class="text-gray-300 hover:text-gold-500 transition-colors">Browse Jobs</a></li>
                    <li><a href="{{ route('register') }}" class="text-gray-300 hover:text-gold-500 transition-colors">Create Account</a></li>
                    <li><a href="{{ route('home') }}#pricing" class="text-gray-300 hover:text-gold-500 transition-colors">Pricing</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="font-display font-semibold text-lg mb-4">For Employers</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('register') }}" class="text-gray-300 hover:text-gold-500 transition-colors">Post a Job</a></li>
                    <li><a href="{{ route('home') }}#for-employers" class="text-gray-300 hover:text-gold-500 transition-colors">Employer Services</a></li>
                    <li><a href="{{ route('home') }}#pricing" class="text-gray-300 hover:text-gold-500 transition-colors">Plans & Pricing</a></li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-white/10 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} Glow Recruitment Agency Solution. All rights reserved.</p>
        </div>
    </div>
</footer>
