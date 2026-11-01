<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Services\CvMalwareScanner;
use Illuminate\Http\Request;

class EmployerVerificationController extends Controller
{
    public function index(Request $request)
    {
        $profile = $request->user()->employerProfile;
        $verifications = $profile?->verifications()->latest()->get() ?? collect();

        return view('employer.verification', compact('profile', 'verifications'));
    }

    public function store(Request $request, CvMalwareScanner $scanner)
    {
        $profile = $request->user()->employerProfile;

        abort_unless($profile, 404, 'Employer profile missing.');

        if ($profile->verification_status === 'approved') {
            return back()->with('error', 'Your employer account is already verified.');
        }

        if ($profile->verifications()->where('status', 'pending')->exists()) {
            return back()->with('error', 'You already have a verification submission awaiting review.');
        }

        $validated = $request->validate([
            'cac_certificate' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $file = $request->file('cac_certificate');
        $scanner->scan($file->getRealPath(), 'cac_certificate');
        $path = $file->store('verification-documents', 'private');

        $verification = $profile->verifications()->create([
            'document_type' => 'cac_certificate',
            'document_path' => $path,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
        ]);

        $profile->update(['verification_status' => 'submitted']);

        try {
            AuditLog::record('employer.verification.submitted', $verification, [], [
                'status' => 'pending',
                'document_type' => 'cac_certificate',
            ]);
        } catch (\Throwable $e) {
        }

        return back()->with('success', 'CAC certificate submitted. Our team will review it shortly.');
    }
}
