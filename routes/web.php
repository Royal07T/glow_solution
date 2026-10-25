<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionController;
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
use App\Http\Controllers\Admin\VerificationController as AdminVerificationController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');

// Auth routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware(['guest', 'throttle:login']);
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
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::get('/jobs/{job}/apply', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/jobs/{job}/apply', [ApplicationController::class, 'store'])->name('applications.store');
    Route::post('/applications/{application}/withdraw', [ApplicationController::class, 'withdraw'])->name('applications.withdraw');
    Route::get('/saved-jobs', [App\Http\Controllers\SavedJobController::class, 'index'])->name('saved-jobs.index');
});

// CV Management
Route::middleware(['auth', 'verified', 'role:job_seeker'])->group(function () {
    Route::get('/cvs', [CvController::class, 'index'])->name('cvs.index');
    Route::post('/cvs', [CvController::class, 'store'])->name('cvs.store');
    Route::get('/cvs/{cv}/download', [CvController::class, 'download'])->name('cvs.download');
    Route::delete('/cvs/{cv}', [CvController::class, 'destroy'])->name('cvs.destroy');
});

// Saved Jobs
Route::middleware(['auth', 'verified', 'role:job_seeker'])->group(function () {
    Route::post('/jobs/{job}/save', function(\App\Models\Job $job) {
        if ($job->status !== 'approved' && (!method_exists($job->status, 'value') || (is_object($job->status) && method_exists($job->status, 'value') ? $job->status->value : (string)$job->status) !== 'approved')) {
            abort(404);
        }
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
    Route::get('/profile', [\App\Http\Controllers\Employer\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Employer\ProfileController::class, 'update'])->name('profile.update');
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
    Route::get('/verifications', [AdminVerificationController::class, 'index'])->name('verifications.index');
    Route::get('/verifications/{verification}', [AdminVerificationController::class, 'show'])->name('verifications.show');
    Route::post('/verifications/{verification}/approve', [AdminVerificationController::class, 'approve'])->name('verifications.approve');
    Route::post('/verifications/{verification}/reject', [AdminVerificationController::class, 'reject'])->name('verifications.reject');
    Route::get('/verifications/{verification}/document', [AdminVerificationController::class, 'downloadDocument'])->name('verifications.document');
    Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [AdminAuditLogController::class, 'show'])->name('audit-logs.show');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/settings', [App\Http\Controllers\AccountController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [App\Http\Controllers\AccountController::class, 'update'])->name('settings.update');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/subscriptions/plans', [SubscriptionPlanController::class, 'index'])->name('subscriptions.plans');
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/activate/{plan}', [SubscriptionController::class, 'activate'])->name('subscriptions.activate');
    Route::get('/billing', [SubscriptionPlanController::class, 'index'])->name('billing.index');
});
