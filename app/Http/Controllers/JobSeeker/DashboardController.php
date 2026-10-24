<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $applications = $user->applications()->with('job.employerProfile')->latest()->take(5)->get();
        $savedJobs = $user->savedJobs()->with('employerProfile')->take(5)->get();
        
        return view('jobseeker.dashboard', compact('applications', 'savedJobs'));
    }
}
