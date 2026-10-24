<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\JobSeeker\DashboardController as JobSeekerDashboard;
use App\Http\Controllers\JobSeeker\ProfileController as JobSeekerProfile;
use App\Http\Controllers\Employer\DashboardController as EmployerDashboard;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use App\Http\Controllers\Employer\ApplicationController as EmployerApplicationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');

// Auth routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register')->middleware('guest');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

// Job Seeker Routes
Route::middleware(['auth', 'verified', 'role:job_seeker'])->prefix('jobseeker')->name('jobseeker.')->group(function () {
    Route::get('/dashboard', [JobSeekerDashboard::class, 'index'])->name('dashboard');
    Route::get('/profile', [JobSeekerProfile::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [JobSeekerProfile::class, 'update'])->name('profile.update');
});

// Applications
Route::middleware(['auth', 'verified', 'role:job_seeker'])->group(function () {
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/jobs/{job}/apply', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/jobs/{job}/apply', [ApplicationController::class, 'store'])->name('applications.store');
    Route::post('/applications/{application}/withdraw', [ApplicationController::class, 'withdraw'])->name('applications.withdraw');
});

// CV Management
Route::middleware(['auth', 'verified', 'role:job_seeker'])->group(function () {
    Route::get('/cvs', [CvController::class, 'index'])->name('cvs.index');
    Route::post('/cvs', [CvController::class, 'store'])->name('cvs.store');
    Route::get('/cvs/{cv}/download', [CvController::class, 'download'])->name('cvs.download');
    Route::delete('/cvs/{cv}', [CvController::class, 'destroy'])->name('cvs.destroy');
});

// Saved Jobs
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/jobs/{job}/save', function(\App\Models\Job $job) {
        auth()->user()->savedJobs()->toggle($job->id);
        return back()->with('success', 'Job saved status updated.');
    })->name('jobs.save');
});

// Employer Routes
Route::middleware(['auth', 'verified', 'role:employer'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('/dashboard', [EmployerDashboard::class, 'index'])->name('dashboard');
    Route::resource('jobs', EmployerJobController::class);
    Route::get('/applications', [EmployerApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [EmployerApplicationController::class, 'show'])->name('applications.show');
    Route::patch('/applications/{application}/status', [EmployerApplicationController::class, 'updateStatus'])->name('applications.update-status');
    Route::get('/verification', function() {
        return view('employer.verification');
    })->name('verification');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/unsuspend', [AdminUserController::class, 'unsuspend'])->name('users.unsuspend');
    Route::get('/jobs', [AdminJobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/{job}', [AdminJobController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{job}/approve', [AdminJobController::class, 'approve'])->name('jobs.approve');
    Route::post('/jobs/{job}/reject', [AdminJobController::class, 'reject'])->name('jobs.reject');
});
