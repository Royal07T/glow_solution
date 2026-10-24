@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-brand-950 via-brand-800 to-brand-700 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.4\"%3E%3Ccircle cx=\"30\" cy=\"30\" r=\"4\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-28">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="font-display text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                    Trusted Recruitment for <span class="text-gold-400">Homes</span> & <span class="text-gold-400">Careers</span>
                </h1>
                <p class="text-xl text-gray-200 mb-8 leading-relaxed">
                    Glow Recruitment Agency connects families with verified domestic staff and helps professionals find meaningful career opportunities across Nigeria.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('jobs.index') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gold-500 hover:bg-gold-600 text-brand-950 font-semibold rounded-lg transition-colors shadow-lg hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-gold-400 focus:ring-offset-2 focus:ring-offset-brand-800 min-h-[44px]">
                        Find Work
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </a>
                    <a href="#for-employers" class="inline-flex items-center justify-center px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-lg border border-white/30 transition-colors backdrop-blur-sm min-h-[44px]">
                        Hire Staff
                    </a>
                </div>
            </div>
            
            <div class="relative">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                    <img src="{{ asset('images/hero-recruitment.jpg') }}" alt="Professional recruiters and job seekers in a meeting" class="w-full h-[400px] sm:h-[500px] object-cover" loading="eager" fetchpriority="high">
                    <div class="absolute inset-0 bg-brand-950 opacity-70"></div>
                </div>
                <div class="absolute -bottom-6 -left-6 bg-white rounded-xl shadow-xl p-4 hidden lg:block">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-brand-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-brand-800" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Verified Employers</p>
                            <p class="text-sm text-slate-600">Safe & secure hiring</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Two-card Audience Split -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mb-4">How Can We Help You?</h2>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">Choose your path - whether you're looking for work or looking to hire</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <a href="{{ route('jobs.index') }}" class="group relative rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                <div class="relative h-64">
                    <img src="{{ asset('images/audience-find-work.jpg') }}" alt="Job seeker looking for employment opportunities" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-brand-950 opacity-70"></div>
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center text-white p-6">
                        <h3 class="font-display text-2xl sm:text-3xl font-bold mb-2">Find Work</h3>
                        <p class="text-gray-200 mb-4">Browse domestic and professional opportunities</p>
                        <span class="inline-flex items-center text-gold-400 font-semibold">
                            Get Started
                            <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </span>
                    </div>
                </div>
            </a>
            
            <a href="#for-employers" class="group relative rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                <div class="relative h-64">
                    <img src="{{ asset('images/audience-find-staff.jpg') }}" alt="Household looking for trusted domestic staff" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    <div class="absolute inset-0 bg-brand-950 opacity-70"></div>
                </div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center text-white p-6">
                        <h3 class="font-display text-2xl sm:text-3xl font-bold mb-2">Find Domestic Staff / Hire Professionals</h3>
                        <p class="text-gray-200 mb-4">Connect with verified candidates</p>
                        <span class="inline-flex items-center text-gold-400 font-semibold">
                            Post a Job
                            <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- Domestic Services Icon Grid -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mb-4">Domestic Staffing Services</h2>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">Trusted help for your home and family needs</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-xl border border-stone-200 text-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-brand-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-brand-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-900">Housekeeping</h3>
            </div>
            <div class="bg-white p-6 rounded-xl border border-stone-200 text-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-brand-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-brand-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-900">Childcare & Nanny</h3>
            </div>
            <div class="bg-white p-6 rounded-xl border border-stone-200 text-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-brand-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-brand-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-900">Elderly Care</h3>
            </div>
            <div class="bg-white p-6 rounded-xl border border-stone-200 text-center hover:shadow-md transition-shadow">
                <div class="w-12 h-12 bg-brand-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-brand-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-slate-900">Cooking & Catering</h3>
            </div>
        </div>
    </div>
</section>

<!-- Professional Recruitment Block -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="order-2 lg:order-1">
                <div class="relative rounded-2xl overflow-hidden shadow-xl">
                    <img src="{{ asset('images/professional-recruitment.jpg') }}" alt="Office professionals in a recruitment interview" class="w-full h-[400px] object-cover">
                    <div class="absolute inset-0 bg-brand-950 opacity-70"></div>
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mb-4">Professional Recruitment</h2>
                <p class="text-lg text-slate-600 mb-6 leading-relaxed">
                    Connect with top talent across various industries. From administration to IT, healthcare to engineering - we help businesses find the right professionals.
                </p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Skilled professionals across multiple sectors</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Thorough screening and verification</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Matched to your specific requirements</span>
                    </li>
                </ul>
                <a href="#for-employers" class="inline-flex items-center px-6 py-3 bg-brand-800 hover:bg-brand-700 text-white font-semibold rounded-lg transition-colors min-h-[44px]">
                    Learn More
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Featured Job Previews -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mb-2">Featured Opportunities</h2>
                <p class="text-lg text-slate-600">Latest job openings from verified employers</p>
            </div>
            <a href="{{ route('jobs.index') }}" class="hidden sm:inline-flex items-center text-brand-800 hover:text-brand-700 font-semibold">
                View All Jobs
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($featuredJobs as $job)
                <div class="bg-white rounded-xl border border-stone-200 p-6 hover:shadow-lg transition-shadow">
                    <div class="flex justify-between items-start mb-3">
                        <span class="px-3 py-1 bg-brand-100 text-brand-800 text-xs font-medium rounded-full">{{ ucfirst($job->type) }}</span>
                        <span class="text-xs text-slate-500">{{ $job->created_at->diffForHumans() }}</span>
                    </div>
                    <h3 class="font-display text-lg font-semibold text-slate-900 mb-2 line-clamp-2">{{ $job->title }}</h3>
                    <p class="text-slate-600 text-sm mb-3 flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        {{ $job->location }}
                    </p>
                    @if($job->salary_min && $job->salary_max)
                        <p class="text-gold-600 font-semibold text-sm mb-4">₦{{ number_format($job->salary_min) }} - ₦{{ number_format($job->salary_max) }}</p>
                    @endif
                    <div class="flex items-center justify-between pt-4 border-t border-stone-200">
                        <span class="text-xs text-slate-500">{{ $job->employerProfile->company_name ?? 'Company' }}</span>
                        <a href="{{ route('jobs.show', $job) }}" class="text-brand-800 hover:text-brand-700 font-medium text-sm">View Details</a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <p class="text-slate-600">No featured jobs available at the moment.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- How it Works -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mb-4">How It Works</h2>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">Simple steps to find your perfect match</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-brand-800 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">1</div>
                <h3 class="font-display text-xl font-semibold text-slate-900 mb-2">Create Your Profile</h3>
                <p class="text-slate-600">Sign up and build your profile with your skills, experience, or hiring needs.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-brand-800 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">2</div>
                <h3 class="font-display text-xl font-semibold text-slate-900 mb-2">Browse & Connect</h3>
                <p class="text-slate-600">Search for jobs or candidates, and connect with verified matches.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-brand-800 text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">3</div>
                <h3 class="font-display text-xl font-semibold text-slate-900 mb-2">Start Working</h3>
                <p class="text-slate-600">Begin your employment journey or hire the perfect candidate.</p>
            </div>
        </div>
    </div>
</section>

<!-- Employer Block and Job Seeker Block -->
<section id="for-employers" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-12">
            <div>
                <div class="relative rounded-2xl overflow-hidden shadow-xl">
                    <img src="{{ asset('images/employer-hiring.jpg') }}" alt="Employer reviewing job applications" class="w-full h-[400px] object-cover">
                    <div class="absolute inset-0 bg-brand-950 opacity-70"></div>
                </div>
            </div>
            <div class="flex flex-col justify-center">
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mb-4">For Employers</h2>
                <p class="text-lg text-slate-600 mb-6 leading-relaxed">
                    Find qualified domestic staff and professionals for your business or home. Access our network of verified candidates with our subscription plans.
                </p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Post jobs and manage applications</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Access verified candidate profiles</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Streamlined recruitment process</span>
                    </li>
                </ul>
                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-brand-800 hover:bg-brand-700 text-white font-semibold rounded-lg transition-colors self-start min-h-[44px]">
                    Post a Job
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div class="flex flex-col justify-center order-2 lg:order-1">
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mb-4">For Job Seekers</h2>
                <p class="text-lg text-slate-600 mb-6 leading-relaxed">
                    Discover opportunities in domestic work and professional careers. Create your profile, apply to jobs, and take the next step in your career.
                </p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Free and premium job access</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Professional profile management</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-gold-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Application tracking</span>
                    </li>
                </ul>
                <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-gold-500 hover:bg-gold-600 text-brand-950 font-semibold rounded-lg transition-colors self-start min-h-[44px]">
                    Find Jobs
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
            <div class="order-1 lg:order-2">
                <div class="relative rounded-2xl overflow-hidden shadow-xl">
                    <img src="{{ asset('images/jobseeker-search.jpg') }}" alt="Job seeker browsing opportunities on a device" class="w-full h-[400px] object-cover">
                    <div class="absolute inset-0 bg-brand-950 opacity-70"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Subscription Benefits -->
<section id="pricing" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mb-4">Subscription Plans</h2>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">Access platform features with flexible subscription options</p>
        </div>
        <div class="bg-gold-100 border border-gold-400 rounded-lg p-4 mb-8 max-w-3xl mx-auto">
            <p class="text-center text-brand-950 font-medium">
                <strong>Important:</strong> A subscription gives access to platform features. It does not guarantee employment.
            </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
            <div class="bg-white rounded-xl border-2 border-stone-200 p-8 relative">
                <h3 class="font-display text-xl font-bold text-slate-900 mb-2">Job Seeker</h3>
                <p class="text-slate-600 mb-6">For individuals seeking employment</p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Browse and apply to jobs</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Manage applications</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Profile management</span>
                    </li>
                </ul>
                <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-6 py-3 bg-brand-800 hover:bg-brand-700 text-white font-semibold rounded-lg transition-colors min-h-[44px]">
                    Get Started
                </a>
            </div>
            
            <div class="bg-white rounded-xl border-2 border-gold-500 p-8 relative shadow-lg">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="bg-gold-500 text-brand-950 px-4 py-1 rounded-full text-sm font-semibold">Most popular</span>
                </div>
                <h3 class="font-display text-xl font-bold text-slate-900 mb-2 mt-2">Employer</h3>
                <p class="text-slate-600 mb-6">For businesses and households hiring</p>
                <ul class="space-y-3 mb-8">
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Post job listings</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Manage applications</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-slate-700">Access candidate database</span>
                    </li>
                </ul>
                <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-6 py-3 bg-gold-500 hover:bg-gold-600 text-brand-950 font-semibold rounded-lg transition-colors min-h-[44px]">
                    Post a Job
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Slider -->
<section class="py-16 bg-gray-50 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mb-4">What Our Clients Say</h2>
            <p class="text-lg text-slate-600 max-w-2xl mx-auto">Real experiences from Nigerian families and professionals</p>
        </div>
    </div>
    <div class="relative">
        <div class="flex animate-scroll hover:pause-animation">
            @php
                $testimonials = [
                    ['name' => 'Adebayo Olumide', 'role' => 'Employer, Lagos', 'text' => 'Glow Recruitment helped us find a reliable housekeeper within days. Professional service and verified candidates.'],
                    ['name' => 'Chioma Nwankwo', 'role' => 'Job Seeker, Abuja', 'text' => 'I found my dream administrative job through Glow. The platform is easy to use and very trustworthy.'],
                    ['name' => 'Ibrahim Musa', 'role' => 'Employer, Port Harcourt', 'text' => 'Excellent service! We hired a qualified nanny through them. Highly recommended for families.'],
                    ['name' => 'Funke Adeyemi', 'role' => 'Job Seeker, Ibadan', 'text' => 'Thanks to Glow, I secured a caregiving job that changed my life. Great platform for domestic workers.'],
                    ['name' => 'Emeka Okonkwo', 'role' => 'Employer, Enugu', 'text' => 'Professional and efficient. Found skilled staff for our business quickly. Very satisfied.'],
                    ['name' => 'Aisha Bello', 'role' => 'Job Seeker, Kano', 'text' => 'Glow connected me with a great family. The verification process gave me confidence.'],
                ];
            @endphp
            @foreach(array_merge($testimonials, $testimonials) as $testimonial)
                <div class="flex-shrink-0 w-80 mx-4">
                    <div class="bg-white rounded-xl border border-stone-200 p-6 h-full shadow-sm">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-brand-800 to-brand-600 rounded-full flex items-center justify-center mr-4">
                                <span class="text-white font-semibold text-sm">{{ substr($testimonial['name'], 0, 2) }}</span>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-900">{{ $testimonial['name'] }}</p>
                                <p class="text-sm text-slate-600">{{ $testimonial['role'] }}</p>
                            </div>
                        </div>
                        <p class="text-slate-700 leading-relaxed">"{{ $testimonial['text'] }}"</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <style>
        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        .animate-scroll {
            animation: scroll 40s linear infinite;
        }
        .hover\:pause-animation:hover {
            animation-play-state: paused;
        }
    </style>
</section>

<!-- FAQ Accordion -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-lg text-slate-600">Common questions about our services</p>
        </div>
        <div class="space-y-4" x-data="{ open: null }">
            <div class="border border-stone-200 rounded-lg">
                <button @click="open = open === 1 ? null : 1" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition-colors min-h-[44px]">
                    <span class="font-semibold text-slate-900">How do I register as a job seeker?</span>
                    <svg class="w-5 h-5 text-slate-500 transition-transform" :class="{ 'rotate-180': open === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open === 1" x-collapse class="px-6 pb-4">
                    <p class="text-slate-600">Click "Get Started" and select "Job Seeker" to create your account. Complete your profile to start browsing and applying to jobs.</p>
                </div>
            </div>
            
            <div class="border border-stone-200 rounded-lg">
                <button @click="open = open === 2 ? null : 2" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition-colors min-h-[44px]">
                    <span class="font-semibold text-slate-900">How do employers post jobs?</span>
                    <svg class="w-5 h-5 text-slate-500 transition-transform" :class="{ 'rotate-180': open === 2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open === 2" x-collapse class="px-6 pb-4">
                    <p class="text-slate-600">Register as an employer, subscribe to a plan, and you can post job listings. All employers go through verification.</p>
                </div>
            </div>
            
            <div class="border border-stone-200 rounded-lg">
                <button @click="open = open === 3 ? null : 3" class="w-full px-6 py-4 text-left flex justify-between items-center hover:bg-gray-50 transition-colors min-h-[44px]">
                    <span class="font-semibold text-slate-900">Are subscriptions refundable?</span>
                    <svg class="w-5 h-5 text-slate-500 transition-transform" :class="{ 'rotate-180': open === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open === 3" x-collapse class="px-6 pb-4">
                    <p class="text-slate-600">Please review our terms of service for refund policies. Subscriptions provide access to platform features as described.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-slate-900 mb-4">Contact Us</h2>
            <p class="text-lg text-slate-600">Get in touch with our team</p>
        </div>
        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-xl border border-stone-200 p-8 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-brand-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-brand-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1">Address</h3>
                            <p class="text-slate-600 leading-relaxed">{{ env('COMPANY_ADDRESS', 'Ultra mordern market mpape Block C Suite C73 opposite Chiken Republic Mpape Abuja Nigeria') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-brand-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-brand-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-slate-900 mb-1">Phone</h3>
                            <p class="text-slate-600 mb-2">{{ env('COMPANY_PHONE', '08036227769') }}</p>
                            <div class="flex gap-3">
                                <a href="tel:{{ env('COMPANY_PHONE', '08036227769') }}" class="text-brand-800 hover:text-brand-700 font-medium">Call</a>
                                <a href="https://wa.me/234{{ substr(env('COMPANY_PHONE', '08036227769'), 1) }}" target="_blank" rel="noopener" class="text-green-600 hover:text-green-700 font-medium">WhatsApp</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
