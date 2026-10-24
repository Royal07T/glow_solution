<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Job;
use App\Models\Payment;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'job_seekers' => User::where('role', 'job_seeker')->count(),
            'employers' => User::where('role', 'employer')->count(),
            'pending_jobs' => Job::where('status', 'pending')->count(),
            'total_jobs' => Job::count(),
            'total_applications' => Application::count(),
            'total_revenue' => Payment::where('status', 'success')->sum('amount'),
        ];

        $pendingJobs = Job::with(['employerProfile.user'])->where('status', 'pending')->latest()->take(5)->get();
        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'pendingJobs', 'recentUsers'));
    }
}
