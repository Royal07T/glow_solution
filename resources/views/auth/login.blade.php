<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Glow Recruitment Agency Solution</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex">
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-brand-900 via-brand-700 to-brand-600 items-center justify-center p-12">
            <div class="text-center max-w-md">
                <img src="{{ asset('logo.png') }}" alt="Glow Recruitment Agency Solution" class="mx-auto mb-8 h-36 w-auto brightness-0 invert drop-shadow-lg">
                <h1 class="font-display text-3xl xl:text-4xl font-bold text-white mb-4">Glow Recruitment Agency Solution</h1>
                <p class="text-lg text-gray-200">Trusted Recruitment for Homes & Careers</p>
            </div>
        </div>
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6">
            <div class="w-full max-w-md">
                <div class="lg:hidden text-center mb-8">
                    <img src="{{ asset('logo.png') }}" alt="Glow Recruitment Agency Solution" class="h-20 w-auto mx-auto mb-4">
                    <h2 class="font-display text-2xl font-bold text-slate-900">Glow Recruitment Agency Solution</h2>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                    <div class="mb-6">
                        <h2 class="font-display text-2xl font-bold text-slate-900 mb-1">Sign In</h2>
                        <p class="text-slate-600 text-sm">Enter your credentials to access your account</p>
                    </div>
                    @if (session('status'))
                        <div class="mb-4 text-sm text-green-600">{{ session('status') }}</div>
                    @endif
                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-700/30 focus:border-brand-700">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                            <div class="relative">
                                <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-700/30 focus:border-brand-700">
                                <button type="button" data-password-toggle="password" aria-label="Show password" aria-pressed="false" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg data-eye-open class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    <svg data-eye-off class="hidden h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-700 focus:ring-brand-700">
                                <span class="ml-2 text-sm text-slate-600">Remember me</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-brand-700 hover:text-brand-600 font-medium">Forgot password?</a>
                            @endif
                        </div>
                        <button type="submit" class="w-full py-2.5 px-4  bg-brand-700 hover:bg-brand-600 text-white font-semibold rounded-lg transition-colors">Sign In</button>
                        <p class="text-center text-sm text-slate-600">
                            Don't have an account? <a href="{{ route('register') }}" class="text-brand-700 hover:text-brand-600 font-medium">Sign up</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
