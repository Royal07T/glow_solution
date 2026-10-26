<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnforceSessionTimeout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $request->session()->put(EnforceSessionTimeout::STARTED_AT_KEY, now()->timestamp);

            $user = Auth::user();
            if ($user->is_suspended) {
                Auth::logout();

                return back()->withErrors(['email' => 'Your account has been suspended.']);
            }

            $user->update(['last_login_at' => now()]);

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->isEmployer()) {
                return redirect()->intended(route('employer.dashboard'));
            } else {
                return redirect()->intended(route('jobseeker.dashboard'));
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        $timedOut = $request->input('reason') === 'timeout';

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($timedOut) {
            return redirect()->route('login')
                ->with('error', 'You have been signed out for security reasons.');
        }

        return redirect('/');
    }
}
