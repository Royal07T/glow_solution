<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Support\SearchTerm;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'type' => ['nullable', Rule::in(Feedback::TYPES)],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $search = SearchTerm::clean($validated['search'] ?? null);

        $query = Feedback::query()->with(['user', 'job.employerProfile', 'application.job']);

        if (filled($validated['type'] ?? null)) {
            $query->where('type', $validated['type']);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', '%'.$search.'%')
                    ->orWhere('message', 'like', '%'.$search.'%')
                    ->orWhereHas('user', function ($user) use ($search) {
                        $user->where('name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%');
                    });
            });
        }

        $feedback = $query->latest()->paginate(20)->withQueryString();

        return view('admin.feedback.index', compact('feedback'));
    }
}
