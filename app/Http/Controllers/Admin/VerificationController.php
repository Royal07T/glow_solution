<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\EmployerVerification;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployerVerification::with(['employerProfile.user', 'reviewer'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $verifications = $query->paginate(15)->withQueryString();

        return view('admin.verifications.index', compact('verifications'));
    }

    public function show(EmployerVerification $verification)
    {
        $verification->load(['employerProfile.user', 'reviewer']);

        return view('admin.verifications.show', compact('verification'));
    }

    public function approve(Request $request, EmployerVerification $verification)
    {
        if ($verification->status !== 'pending') {
            return back()->with('error', 'This verification has already been reviewed.');
        }

        DB::transaction(function () use ($request, $verification) {
            $verification->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            $verification->employerProfile->update([
                'verification_status' => 'approved',
                'verified_at' => now(),
                'verified_by' => $request->user()->id,
            ]);
        });

        AuditLog::record('employer.verification.approved', $verification, ['status' => 'pending'], ['status' => 'approved']);

        $verification->loadMissing('employerProfile.user');
        $verification->employerProfile?->user?->notify(new SystemNotification(
            'Verification approved',
            'Your employer account has been verified. You can now post jobs and manage applications.',
            route('employer.verification', absolute: false),
            'success',
        ));

        return back()->with('success', 'Employer verified successfully.');
    }

    public function reject(Request $request, EmployerVerification $verification)
    {
        if ($verification->status !== 'pending') {
            return back()->with('error', 'This verification has already been reviewed.');
        }

        $validated = $request->validate([
            'admin_notes' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($request, $verification, $validated) {
            $verification->update([
                'status' => 'rejected',
                'admin_notes' => $validated['admin_notes'],
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            $verification->employerProfile->update([
                'verification_status' => 'rejected',
            ]);
        });

        AuditLog::record('employer.verification.rejected', $verification, ['status' => 'pending'], ['status' => 'rejected']);

        $verification->loadMissing('employerProfile.user');
        $verification->employerProfile?->user?->notify(new SystemNotification(
            'Verification needs attention',
            'Your verification was not approved. '.$validated['admin_notes'],
            route('employer.verification', absolute: false),
            'danger',
        ));

        return back()->with('success', 'Verification rejected.');
    }

    public function downloadDocument(EmployerVerification $verification)
    {
        abort_unless(Storage::disk('private')->exists($verification->document_path), 404);

        return Storage::disk('private')->download(
            $verification->document_path,
            'verification-'.$verification->id.'.'.pathinfo($verification->document_path, PATHINFO_EXTENSION)
        );
    }
}
