<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Feedback;
use App\Models\Job;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class FeedbackController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'job' => ['nullable', 'integer', 'exists:jobs,id'],
            'application' => ['nullable', 'integer', 'exists:applications,id'],
        ]);

        $job = null;
        $application = null;

        if (filled($validated['application'] ?? null)) {
            $application = Application::with('job.employerProfile')->findOrFail($validated['application']);

            abort_unless($application->job_seeker_id === $request->user()->id, 403);

            $job = $application->job;
        } elseif (filled($validated['job'] ?? null)) {
            $job = Job::with('employerProfile')->findOrFail($validated['job']);

            abort_unless($job->isApproved(), 404);
        }

        return view('feedback.create', compact('job', 'application'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(Feedback::TYPES)],
            'job_id' => ['nullable', 'integer', 'exists:jobs,id'],
            'application_id' => ['nullable', 'integer', 'exists:applications,id'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        if ($validated['type'] === 'application') {
            if (empty($validated['application_id'])) {
                throw ValidationException::withMessages([
                    'application_id' => 'Select an application to give feedback on.',
                ]);
            }

            $application = Application::findOrFail($validated['application_id']);

            abort_unless($application->job_seeker_id === $request->user()->id, 403);

            $validated['job_id'] = $application->job_id;
        } elseif ($validated['type'] === 'job') {
            if (empty($validated['job_id'])) {
                throw ValidationException::withMessages([
                    'job_id' => 'Select a job to give feedback on.',
                ]);
            }

            $validated['application_id'] = null;
        } else {
            $validated['job_id'] = null;
            $validated['application_id'] = null;
            $validated['rating'] = null;
        }

        $feedback = $request->user()->feedback()->create([
            'type' => $validated['type'],
            'job_id' => $validated['job_id'] ?? null,
            'application_id' => $validated['application_id'] ?? null,
            'rating' => $validated['rating'] ?? null,
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
        ]);

        User::where('role', 'admin')->get()->each(function (User $admin) use ($feedback, $request) {
            $admin->notify(new SystemNotification(
                'New feedback received',
                $request->user()->name.' submitted '.$feedback->typeLabel().' feedback.',
                route('admin.feedback.index', [], false),
                'info',
            ));
        });

        return redirect()
            ->route('feedback.create')
            ->with('success', 'Thank you for your feedback. The team will review it.');
    }
}
