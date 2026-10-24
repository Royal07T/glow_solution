<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['jobSeekerProfile', 'employerProfile', 'subscriptions.plan']);
        return view('admin.users.show', compact('user'));
    }

    public function suspend(Request $request, User $user)
    {
        $validated = $request->validate([
            'suspension_reason' => 'required|string',
        ]);

        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot suspend admin users.');
        }

        $user->update([
            'is_suspended' => true,
            'suspended_at' => now(),
            'suspension_reason' => $validated['suspension_reason'],
        ]);

        return redirect()->back()->with('success', 'User suspended successfully.');
    }

    public function unsuspend(User $user)
    {
        $user->update([
            'is_suspended' => false,
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        return redirect()->back()->with('success', 'User unsuspended successfully.');
    }
}
