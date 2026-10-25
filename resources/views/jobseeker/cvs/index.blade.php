@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-purple-900 mb-8">My CVs</h1>
    
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-xl font-semibold text-purple-900 mb-4">Upload New CV</h2>
        <form method="POST" action="{{ route('cvs.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">CV Name</label>
                <input type="text" name="name" required class="w-full border border-gray-300 rounded-md px-4 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">File (PDF, DOC, DOCX - Max 5MB)</label>
                <input type="file" name="cv" required accept=".pdf,.doc,.docx" class="w-full">
            </div>
            <button type="submit" class="bg-purple-900 text-white px-6 py-2 rounded-md hover:bg-purple-800">Upload CV</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-purple-900">Uploaded CVs</h2>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($cvs as $cv)
                <div class="p-6 flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $cv->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $cv->file_name }} ({{ number_format($cv->file_size / 1024, 2) }} KB)</p>
                        @if($cv->is_primary)
                            <span class="inline-block mt-1 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Primary</span>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('cvs.download', $cv) }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">Download</a>
                        <form method="POST" action="{{ route('cvs.destroy', $cv) }}" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">No CVs uploaded yet.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
