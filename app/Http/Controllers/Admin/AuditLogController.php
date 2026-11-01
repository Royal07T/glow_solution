<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\EmployerVerification;
use App\Models\Job;
use App\Models\User;
use App\Support\SearchTerm;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'action' => ['nullable', 'string', 'max:100'],
            'user' => ['nullable', 'string', 'max:100'],
        ]);

        $action = SearchTerm::clean($validated['action'] ?? null);
        $user = SearchTerm::clean($validated['user'] ?? null);

        $query = AuditLog::with('user')->latest();

        if ($action !== '') {
            $query->where('action', 'like', '%'.$action.'%');
        }

        if ($user !== '') {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('email', 'like', '%'.$user.'%');
            });
        }

        $logs = $query->paginate(30)->withQueryString();

        return view('admin.audit-logs.index', compact('logs'));
    }

    public function show(AuditLog $auditLog)
    {
        $auditLog->loadMissing('user');

        $auditable = $auditLog->auditable;

        $targetUrl = match (true) {
            $auditable instanceof Job => route('admin.jobs.show', $auditable),
            $auditable instanceof User => route('admin.users.show', $auditable),
            $auditable instanceof EmployerVerification => route('admin.verifications.show', $auditable),
            default => null,
        };

        return view('admin.audit-logs.show', compact('auditLog', 'auditable', 'targetUrl'));
    }
}
