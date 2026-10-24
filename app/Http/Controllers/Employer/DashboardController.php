<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = $user->employerProfile;
        $jobs = $profile ? $profile->jobs()->withCount('applications')->latest()->take(5)->get() : collect();
        $totalApplications = $profile ? \App\Models\Application::whereHas('job', function($q) use ($profile) {
            $q->where('employer_profile_id', $profile->id);
        })->count() : 0;

        return view('employer.dashboard', compact('profile', 'jobs', 'totalApplications'));
    }
}
