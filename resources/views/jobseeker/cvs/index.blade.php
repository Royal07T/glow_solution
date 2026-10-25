@extends('layouts.portal')

@section('title', 'My CV / Resume')
@section('subtitle', 'Upload and manage the documents employers receive with your applications.')

@section('content')
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Upload a new CV</h2>
                <p class="mt-1 text-sm text-slate-600">PDF, DOC or DOCX. Maximum file size 5&nbsp;MB. Only you and employers you apply to can access your CV.</p>

                <form method="POST" action="{{ route('cvs.store') }}" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="mb-1 block text-sm font-medium text-slate-700">CV name <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="name" name="name" type="text" required maxlength="255" value="{{ old('name') }}" placeholder="e.g. My professional CV" class="w-full rounded-lg border px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('name') ? 'border-red-400' : 'border-slate-300' }}">
                        @error('name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="cv" class="mb-1 block text-sm font-medium text-slate-700">CV file <span class="text-danger" aria-hidden="true">*</span></label>
                        <input id="cv" name="cv" type="file" required accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" class="block w-full cursor-pointer rounded-lg border border-slate-300 file:mr-4 file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                        @error('cv') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="inline-flex min-h-[44px] items-center gap-2 rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">
                        <x-icon name="download" class="h-4 w-4 rotate-180" /> Upload CV
                    </button>
                </form>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="font-display text-base font-semibold text-brand-900">Uploaded CVs</h2>
                </div>

                @forelse($cvs as $cv)
                    <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 last:border-0 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="truncate font-semibold text-brand-900">{{ $cv->name }}</h3>
                                @if($cv->is_primary)
                                    <x-badge variant="gold">Primary</x-badge>
                                @endif
                            </div>
                            <p class="text-sm text-slate-500">{{ $cv->file_name }} · {{ number_format($cv->file_size / 1024, 1) }} KB</p>
                            <p class="text-xs text-slate-400">Uploaded {{ $cv->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex shrink-0 gap-2">
                            <a href="{{ route('cvs.download', $cv) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                <x-icon name="download" class="h-4 w-4" /> Download
                            </a>
                            <form method="POST" action="{{ route('cvs.destroy', $cv) }}" data-confirm="Delete this CV permanently?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-danger hover:bg-red-50">
                                    <x-icon name="trash" class="h-4 w-4" /> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-5">
                        <x-empty-state icon="document" title="No CVs uploaded" message="Upload a CV to attach it when you apply for a role." />
                    </div>
                @endforelse
            </section>
        </div>

        <aside class="space-y-4">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-display text-base font-semibold text-brand-900">Tips for a stronger CV</h3>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> Keep it to one or two pages.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> Lead with your most relevant experience.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> Use a clear file name such as Firstname-Lastname-CV.pdf.</li>
                    <li class="flex gap-2"><x-icon name="check" class="mt-0.5 h-4 w-4 shrink-0 text-green-600" /> Check that contact details are current.</li>
                </ul>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="font-display text-base font-semibold text-brand-900">Privacy</h3>
                <p class="mt-2 text-sm text-slate-600">CVs are stored privately. They are only downloadable by you and by employers that receive your application.</p>
            </section>
        </aside>
    </div>
@endsection
