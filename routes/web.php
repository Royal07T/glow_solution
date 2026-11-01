<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\ApplicationController as AdminApplicationController;
use App\Http\Controllers\Admin\AuditLogController as AdminAuditLogController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\SubscriptionPlanController as AdminSubscriptionPlanController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\VerificationController as AdminVerificationController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\Employer\ApplicationController as EmployerApplicationController;
use App\Http\Controllers\Employer\DashboardController as EmployerDashboard;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use App\Http\Controllers\Employer\ProfileController;
use App\Http\Controllers\EmployerVerificationController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobSeeker\DashboardController as JobSeekerDashboard;
use App\Http\Controllers\JobSeeker\ProfileController as JobSeekerProfile;
use App\Http\Controllers\JobSeekerIdentityController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaystackPaymentController;
use App\Http\Controllers\SavedJobController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SubscriptionPlanController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
Route::get('/locations/lgas', [LocationController::class, 'lgas'])->name('locations.lgas')->middleware('rate.limit:locations,60,60');

// Auth routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware(['guest', 'throttle:login']);
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register')->middleware('guest');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware(['guest', 'rate.limit:register,3,60']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');

Route::get('/home', function () {
    $user = auth()->user();

    return redirect()->route(match (true) {
        $user->isAdmin() => 'admin.dashboard',
        $user->isEmployer() => 'employer.dashboard',
        default => 'jobseeker.dashboard',
    });
})->middleware('auth')->name('home.redirect');

Route::view('/email/verified', 'auth.email-verified')
    ->middleware(['auth', 'verified'])
    ->name('verification.verified');

// Job Seeker Routes
Route::middleware(['auth', 'verified', 'role:job_seeker'])->prefix('jobseeker')->name('jobseeker.')->group(function () {
    Route::get('/dashboard', [JobSeekerDashboard::class, 'index'])->name('dashboard');
    Route::get('/recommended', [JobSeekerDashboard::class, 'recommended'])->name('recommended');
    Route::get('/interviews', [ApplicationController::class, 'interviews'])->name('interviews');
    Route::get('/profile', [JobSeekerProfile::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [JobSeekerProfile::class, 'update'])->name('profile.update')->middleware('rate.limit:profile-updates,10,60');
});

// Applications
Route::middleware(['auth', 'verified', 'role:job_seeker'])->group(function () {
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::get('/jobs/{job}/apply', [ApplicationController::class, 'create'])->name('applications.create');
    Route::post('/jobs/{job}/apply', [ApplicationController::class, 'store'])->name('applications.store')->middleware('rate.limit:applications,10,60');
    Route::post('/applications/{application}/withdraw', [ApplicationController::class, 'withdraw'])->name('applications.withdraw')->middleware('rate.limit:withdrawals,5,60');
    Route::get('/saved-jobs', [SavedJobController::class, 'index'])->name('saved-jobs.index');
    Route::delete('/saved-jobs/{job}', [SavedJobController::class, 'destroy'])->name('saved-jobs.destroy')->middleware('rate.limit:saved-jobs,30,60');
});

// CV Management
Route::middleware(['auth', 'verified', 'role:job_seeker'])->group(function () {
    Route::get('/cvs', [CvController::class, 'index'])->name('cvs.index');
    Route::post('/cvs', [CvController::class, 'store'])->name('cvs.store')->middleware('rate.limit:uploads,5,60');
    Route::delete('/cvs/{cv}', [CvController::class, 'destroy'])->name('cvs.destroy')->middleware('rate.limit:uploads,5,60');
});

// Saved Jobs
Route::middleware(['auth', 'verified', 'role:job_seeker'])->group(function () {
    Route::post('/jobs/{job}/save', [SavedJobController::class, 'toggle'])->name('jobs.save')->middleware('rate.limit:saved-jobs,30,60');
});

// Feedback
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store')->middleware('rate.limit:feedback,5,60');
});

// Employer Routes
Route::middleware(['auth', 'verified', 'role:employer'])->prefix('employer')->name('employer.')->group(function () {
    Route::get('/dashboard', [EmployerDashboard::class, 'index'])->name('dashboard');
    Route::resource('jobs', EmployerJobController::class)
        ->middlewareFor(['store', 'update', 'destroy'], 'rate.limit:employer-jobs,20,60');
    Route::get('/applications', [EmployerApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [EmployerApplicationController::class, 'show'])->name('applications.show');
    Route::patch('/applications/{application}/status', [EmployerApplicationController::class, 'updateStatus'])->name('applications.update-status')->middleware('rate.limit:application-status,30,60');
    Route::get('/verification', [EmployerVerificationController::class, 'index'])->name('verification');
    Route::post('/verification', [EmployerVerificationController::class, 'store'])->name('verification.store')->middleware('rate.limit:employer-verification,5,60');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update')->middleware('rate.limit:profile-updates,10,60');
});

// Admin Routes
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('users', AdminUserController::class)
        ->middlewareFor(['store', 'update', 'destroy'], 'rate.limit:admin-users,10,60');
    Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('users.suspend')->middleware('rate.limit:admin-actions,30,60');
    Route::post('/users/{user}/unsuspend', [AdminUserController::class, 'unsuspend'])->name('users.unsuspend')->middleware('rate.limit:admin-actions,30,60');
    Route::post('/users/{user}/identity/approve', [AdminUserController::class, 'approveIdentity'])->name('users.identity.approve')->middleware('rate.limit:admin-actions,30,60');
    Route::post('/users/{user}/identity/reject', [AdminUserController::class, 'rejectIdentity'])->name('users.identity.reject')->middleware('rate.limit:admin-actions,30,60');
    Route::get('/jobs', [AdminJobController::class, 'index'])->name('jobs.index');
    Route::get('/jobs/create', [AdminJobController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [AdminJobController::class, 'store'])->name('jobs.store')->middleware('rate.limit:admin-actions,30,60');
    Route::get('/jobs/{job}', [AdminJobController::class, 'show'])->name('jobs.show');
    Route::get('/jobs/{job}/edit', [AdminJobController::class, 'edit'])->name('jobs.edit');
    Route::put('/jobs/{job}', [AdminJobController::class, 'update'])->name('jobs.update')->middleware('rate.limit:admin-actions,30,60');
    Route::post('/jobs/{job}/approve', [AdminJobController::class, 'approve'])->name('jobs.approve')->middleware('rate.limit:admin-actions,30,60');
    Route::post('/jobs/{job}/reject', [AdminJobController::class, 'reject'])->name('jobs.reject')->middleware('rate.limit:admin-actions,30,60');
    Route::get('/applications', [AdminApplicationController::class, 'index'])->name('applications.index');
    Route::get('/applications/{application}', [AdminApplicationController::class, 'show'])->name('applications.show');
    Route::patch('/applications/{application}/status', [AdminApplicationController::class, 'updateStatus'])->name('applications.update-status')->middleware('rate.limit:admin-actions,30,60');
    Route::get('/verifications', [AdminVerificationController::class, 'index'])->name('verifications.index');
    Route::get('/verifications/{verification}', [AdminVerificationController::class, 'show'])->name('verifications.show');
    Route::post('/verifications/{verification}/approve', [AdminVerificationController::class, 'approve'])->name('verifications.approve')->middleware('rate.limit:admin-actions,30,60');
    Route::post('/verifications/{verification}/reject', [AdminVerificationController::class, 'reject'])->name('verifications.reject')->middleware('rate.limit:admin-actions,30,60');
    Route::get('/verifications/{verification}/document', [AdminVerificationController::class, 'downloadDocument'])->name('verifications.document');
    Route::get('/feedback', [AdminFeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [AdminAuditLogController::class, 'show'])->name('audit-logs.show');
    Route::resource('subscription-plans', AdminSubscriptionPlanController::class)
        ->except(['show'])
        ->parameters(['subscription-plans' => 'subscriptionPlan'])
        ->middlewareFor(['store', 'update', 'destroy'], 'rate.limit:admin-actions,30,60');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/cvs/{cv}/download', [CvController::class, 'download'])->name('cvs.download');
    Route::get('/job-seekers/{user}/photo', [JobSeekerIdentityController::class, 'photo'])->name('jobseekers.photo');
    Route::get('/job-seekers/{user}/identification', [JobSeekerIdentityController::class, 'document'])->name('jobseekers.identification');

    Route::get('/settings', [AccountController::class, 'edit'])->name('settings.edit');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all')->middleware('rate.limit:notifications,30,60');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/subscriptions/plans', [SubscriptionPlanController::class, 'index'])->name('subscriptions.plans');
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/subscriptions/activate/{plan}', [SubscriptionController::class, 'activate'])->name('subscriptions.activate')->middleware('rate.limit:subscriptions,5,60');
    Route::get('/billing', [SubscriptionPlanController::class, 'index'])->name('billing.index');
    Route::post('/payments/paystack/initialize/{plan}', [PaystackPaymentController::class, 'initialize'])->name('payments.paystack.initialize')->middleware('rate.limit:payments,5,60');
    Route::get('/payments/paystack/callback', [PaystackPaymentController::class, 'callback'])->name('payments.paystack.callback');
    Route::get('/payments/{payment}/receipt', [PaystackPaymentController::class, 'receipt'])->name('payments.receipt')->middleware('rate.limit:receipts,10,60');
});

Route::post('/payments/paystack/webhook', [PaystackPaymentController::class, 'webhook'])->name('payments.paystack.webhook');
