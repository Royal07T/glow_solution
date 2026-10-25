<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:job_seeker,employer'],
        ], [
            'phone.regex' => 'Please enter a valid phone number.',
        ]);

        $firstName = trim($request->first_name);
        $lastName = trim($request->last_name);

        $user = User::create([
            'name' => trim($firstName.' '.$lastName),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($user->isJobSeeker()) {
            $user->jobSeekerProfile()->create([]);
        } elseif ($user->isEmployer()) {
            $user->employerProfile()->create(['type' => 'business']);
        }

        event(new Registered($user));

        Auth::login($user);

        if ($user->isEmployer()) {
            return redirect(route('employer.dashboard'));
        }
        return redirect(route('jobseeker.dashboard'));
    }
}
