<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class JobSeekerIdentityController extends Controller
{
    public function photo(Request $request, User $user): Response
    {
        $this->authorizeAccess($request, $user);

        $path = $user->jobSeekerProfile?->photo_path;

        abort_unless(filled($path) && Storage::disk('private')->exists($path), 404);

        return Storage::disk('private')->response($path, null, [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function document(Request $request, User $user): Response
    {
        $this->authorizeAccess($request, $user);

        $path = $user->jobSeekerProfile?->identification_document_path;

        abort_unless(filled($path) && Storage::disk('private')->exists($path), 404);

        return Storage::disk('private')->response(
            $path,
            'identification-document-'.$user->id.'.'.pathinfo($path, PATHINFO_EXTENSION),
            ['Cache-Control' => 'private, no-store'],
        );
    }

    private function authorizeAccess(Request $request, User $user): void
    {
        $viewer = $request->user();

        abort_unless($viewer && ($viewer->is($user) || $viewer->isAdmin()), 403);
    }
}
