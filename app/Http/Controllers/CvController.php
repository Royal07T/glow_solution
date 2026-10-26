<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Services\CvMalwareScanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CvController extends Controller
{
    public function index()
    {
        $this->authorize('create', Cv::class);
        $cvs = auth()->user()->cvs()->latest()->get();

        return view('jobseeker.cvs.index', compact('cvs'));
    }

    public function store(Request $request, CvMalwareScanner $scanner)
    {
        $this->authorize('create', Cv::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:5120', // 5MB
        ]);

        $file = $request->file('cv');
        $scanner->scan($file->getRealPath());
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store('cvs', 'private');

        Cv::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'is_primary' => auth()->user()->cvs()->count() === 0,
        ]);

        return redirect()->route('cvs.index')->with('success', 'CV uploaded successfully.');
    }

    public function download(Cv $cv)
    {
        $this->authorize('download', $cv);

        if (! Storage::disk('private')->exists($cv->file_path)) {
            abort(404);
        }

        return Storage::disk('private')->download($cv->file_path, $cv->file_name);
    }

    public function destroy(Cv $cv)
    {
        $this->authorize('delete', $cv);

        if (Storage::disk('private')->exists($cv->file_path)) {
            Storage::disk('private')->delete($cv->file_path);
        }

        $cv->delete();

        return redirect()->route('cvs.index')->with('success', 'CV deleted successfully.');
    }
}
