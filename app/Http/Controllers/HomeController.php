<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredJobs = Job::with(['employerProfile', 'category'])
            ->where('status', 'approved')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->latest('published_at')
            ->take(6)
            ->get();

        $categories = JobCategory::where('is_active', true)
            ->withCount(['jobs' => function($q) {
                $q->where('status', 'approved')->whereNotNull('published_at');
            }])
            ->orderBy('jobs_count', 'desc')
            ->take(8)
            ->get();

        return view('home', compact('featuredJobs', 'categories'));
    }
}
