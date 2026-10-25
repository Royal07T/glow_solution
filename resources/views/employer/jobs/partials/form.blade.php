@php
    $job = $job ?? null;
@endphp

<div class="grid gap-6 lg:grid-cols-3">
    <div class="space-y-6 lg:col-span-2">
        <section class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="font-display text-base font-semibold text-slate-900">Role details</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="title" class="mb-1 block text-sm font-medium text-slate-700">Job title</label>
                    <input id="title" type="text" name="title" required maxlength="150" value="{{ old('title', $job->title ?? '') }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                    @error('title') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="job_category_id" class="mb-1 block text-sm font-medium text-slate-700">Category</label>
                        <select id="job_category_id" name="job_category_id" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                            <option value="">Uncategorised</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected((int) old('job_category_id', $job->job_category_id ?? 0) === $category->id)>
                                    {{ $category->name }} ({{ $category->type }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="type" class="mb-1 block text-sm font-medium text-slate-700">Sector</label>
                        <select id="type" name="type" required class="w-full rounded-lg border border-stone-300 px-3 py-2">
                            @foreach(['domestic' => 'Domestic', 'professional' => 'Professional'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('type', $job->type ?? 'domestic') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="employment_type" class="mb-1 block text-sm font-medium text-slate-700">Employment type</label>
                        <select id="employment_type" name="employment_type" required class="w-full rounded-lg border border-stone-300 px-3 py-2">
                            @foreach(['full_time' => 'Full time', 'part_time' => 'Part time', 'contract' => 'Contract', 'temporary' => 'Temporary', 'live_in' => 'Live in', 'live_out' => 'Live out'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('employment_type', $job->employment_type ?? 'full_time') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="positions_available" class="mb-1 block text-sm font-medium text-slate-700">Positions available</label>
                        <input id="positions_available" type="number" name="positions_available" min="1" max="100" value="{{ old('positions_available', $job->positions_available ?? 1) }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                    </div>
                </div>

                <div>
                    <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                    <textarea id="description" name="description" rows="6" required maxlength="5000" class="w-full rounded-lg border border-stone-300 px-3 py-2">{{ old('description', $job->description ?? '') }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="responsibilities" class="mb-1 block text-sm font-medium text-slate-700">Responsibilities <span class="text-slate-400">(optional)</span></label>
                    <textarea id="responsibilities" name="responsibilities" rows="3" maxlength="3000" class="w-full rounded-lg border border-stone-300 px-3 py-2">{{ old('responsibilities', $job->responsibilities ?? '') }}</textarea>
                </div>

                <div>
                    <label for="qualifications" class="mb-1 block text-sm font-medium text-slate-700">Qualifications <span class="text-slate-400">(optional)</span></label>
                    <textarea id="qualifications" name="qualifications" rows="3" maxlength="3000" class="w-full rounded-lg border border-stone-300 px-3 py-2">{{ old('qualifications', $job->qualifications ?? '') }}</textarea>
                </div>

                <div>
                    <label for="benefits" class="mb-1 block text-sm font-medium text-slate-700">Benefits <span class="text-slate-400">(optional)</span></label>
                    <textarea id="benefits" name="benefits" rows="3" maxlength="3000" class="w-full rounded-lg border border-stone-300 px-3 py-2">{{ old('benefits', $job->benefits ?? '') }}</textarea>
                </div>
            </div>
        </section>
    </div>

    <div class="space-y-6">
        <section class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="font-display text-base font-semibold text-slate-900">Location</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <label for="location" class="mb-1 block text-sm font-medium text-slate-700">Location</label>
                    <input id="location" type="text" name="location" required maxlength="255" value="{{ old('location', $job->location ?? '') }}" class="w-full rounded-lg border border-stone-300 px-3 py-2" placeholder="e.g. Ikoyi, Lagos">
                    @error('location') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="city" class="mb-1 block text-sm font-medium text-slate-700">City</label>
                    <input id="city" type="text" name="city" maxlength="100" value="{{ old('city', $job->city ?? '') }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                </div>
                <div>
                    <label for="state" class="mb-1 block text-sm font-medium text-slate-700">State</label>
                    <input id="state" type="text" name="state" maxlength="100" value="{{ old('state', $job->state ?? '') }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-stone-200 bg-white p-6">
            <h2 class="font-display text-base font-semibold text-slate-900">Salary & deadline</h2>
            <div class="mt-4 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="salary_min" class="mb-1 block text-sm font-medium text-slate-700">Minimum</label>
                        <input id="salary_min" type="number" name="salary_min" min="0" step="0.01" value="{{ old('salary_min', $job->salary_min ?? '') }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                    </div>
                    <div>
                        <label for="salary_max" class="mb-1 block text-sm font-medium text-slate-700">Maximum</label>
                        <input id="salary_max" type="number" name="salary_max" min="0" step="0.01" value="{{ old('salary_max', $job->salary_max ?? '') }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="salary_currency" class="mb-1 block text-sm font-medium text-slate-700">Currency</label>
                        <input id="salary_currency" type="text" name="salary_currency" maxlength="3" value="{{ old('salary_currency', $job->salary_currency ?? 'NGN') }}" class="w-full rounded-lg border border-stone-300 px-3 py-2 uppercase">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 pb-3 text-sm text-slate-700">
                            <input type="checkbox" name="salary_negotiable" value="1" class="rounded border-stone-300" @checked(old('salary_negotiable', $job->salary_negotiable ?? false))>
                            Negotiable
                        </label>
                    </div>
                </div>
                <div>
                    <label for="application_deadline" class="mb-1 block text-sm font-medium text-slate-700">Application deadline</label>
                    <input id="application_deadline" type="date" name="application_deadline" value="{{ old('application_deadline', isset($job) && $job->application_deadline ? $job->application_deadline->format('Y-m-d') : '') }}" class="w-full rounded-lg border border-stone-300 px-3 py-2">
                    @error('application_deadline') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-[#EDE9FE bg-[#F5F3FF p-5">
            <h2 class="font-display text-sm font-semibold text-[#05244a">Moderation</h2>
            <p class="mt-1 text-xs text-[#062B52">
                Every new or edited listing is reviewed by an administrator before it becomes public.
            </p>
        </section>
    </div>
</div>
