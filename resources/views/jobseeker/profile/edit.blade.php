@extends('layouts.portal')

@section('title', 'My profile')
@section('subtitle', 'Keep your details current so employers see the best version of you.')

@section('content')
    @php
        $selectedCategories = old('preferred_categories', $profile->preferred_categories ?? []);
        $selectedCategories = is_array($selectedCategories) ? array_map('strval', $selectedCategories) : [];
        $skillsValue = old('skills_text', is_array($profile->skills) ? implode(', ', $profile->skills) : '');
        $locationsValue = old('preferred_locations_text', is_array($profile->preferred_locations) ? implode(', ', $profile->preferred_locations) : '');
        $educationLevels = ['Secondary / SSCE', 'Diploma', 'HND', 'Bachelor’s degree', 'Master’s degree', 'Doctorate', 'Other'];
        $experienceLevels = ['Entry level', 'Junior', 'Mid level', 'Senior', 'Expert'];
        $identityStatus = $profile->identity_verification_status ?? 'unsubmitted';
        $identityStatusLabel = $profile->identityStatusLabel();
        $identityStatusVariant = $profile->identityStatusBadgeVariant();
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        <form method="POST" action="{{ route('jobseeker.profile.update') }}" enctype="multipart/form-data" class="space-y-6 lg:col-span-2">
            @csrf
            @method('PUT')

            <section id="personal" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-base font-semibold text-brand-900">Personal information</h2>
                        <p class="mt-1 text-sm text-slate-600">Your name, phone number and location help employers reach you.</p>
                    </div>
                    @if(filled($profile->first_name) || filled($profile->last_name))
                        <x-badge variant="success">Started</x-badge>
                    @endif
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    @if(filled($profile->photo_path))
                        <img src="{{ route('jobseekers.photo', $user) }}" alt="Profile photo" class="h-20 w-20 rounded-full object-cover ring-2 ring-white">
                    @else
                        <span class="flex h-20 w-20 shrink-0 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                            <x-icon name="user" class="h-9 w-9" />
                        </span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <label for="photo" class="mb-1 block text-sm font-medium text-slate-700">Profile photo</label>
                        <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-700 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-800">
                        <p class="mt-1 text-xs text-slate-500">JPG, PNG or WebP. Maximum 2 MB. A clear face photo builds trust with employers.</p>
                        @error('photo') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="mb-1 block text-sm font-medium text-slate-700">First name</label>
                        <input id="first_name" name="first_name" type="text" value="{{ old('first_name', $profile->first_name) }}" autocomplete="given-name" class="w-full rounded-lg border px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('first_name') ? 'border-red-400' : 'border-slate-300' }}">
                        @error('first_name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="last_name" class="mb-1 block text-sm font-medium text-slate-700">Last name</label>
                        <input id="last_name" name="last_name" type="text" value="{{ old('last_name', $profile->last_name) }}" autocomplete="family-name" class="w-full rounded-lg border px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('last_name') ? 'border-red-400' : 'border-slate-300' }}">
                        @error('last_name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="phone" class="mb-1 block text-sm font-medium text-slate-700">Phone number</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone', $user->phone) }}" autocomplete="tel" placeholder="e.g. 0803 622 7769" class="w-full rounded-lg border px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('phone') ? 'border-red-400' : 'border-slate-300' }}">
                        @error('phone') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="date_of_birth" class="mb-1 block text-sm font-medium text-slate-700">Date of birth</label>
                        <input id="date_of_birth" name="date_of_birth" type="date" value="{{ old('date_of_birth', $profile->date_of_birth?->format('Y-m-d')) }}" max="{{ now()->subDay()->format('Y-m-d') }}" class="w-full rounded-lg border px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('date_of_birth') ? 'border-red-400' : 'border-slate-300' }}">
                        @error('date_of_birth') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="gender" class="mb-1 block text-sm font-medium text-slate-700">Gender</label>
                        <select id="gender" name="gender" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                            <option value="">Prefer not to say</option>
                            @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('gender', $profile->gender) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('gender') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="address" class="mb-1 block text-sm font-medium text-slate-700">Address</label>
                        <input id="address" name="address" type="text" value="{{ old('address', $profile->address) }}" autocomplete="street-address" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        @error('address') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <x-location-fields :state="$profile->state" :city="$profile->city" />
                    <div>
                        <label for="country" class="mb-1 block text-sm font-medium text-slate-700">Country</label>
                        <input id="country" name="country" type="text" value="{{ old('country', $profile->country ?? 'Nigeria') }}" autocomplete="country-name" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        @error('country') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <section id="identity" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h2 class="font-display text-base font-semibold text-brand-900">Identity verification</h2>
                        <p class="mt-1 text-sm text-slate-600">Verify your identity to help keep the marketplace safe. Your ID number is encrypted and only visible to you and authorised admins.</p>
                    </div>
                    <x-badge :variant="$identityStatusVariant">{{ $identityStatusLabel }}</x-badge>
                </div>

                @if($identityStatus === 'pending')
                    <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        <p class="font-medium">Your identity is awaiting review.</p>
                        @if($profile->identity_submitted_at)
                            <p class="mt-1">Submitted {{ $profile->identity_submitted_at->format('M j, Y \a\t g:ia') }}. An admin will review your details shortly.</p>
                        @endif
                    </div>
                @elseif($identityStatus === 'approved')
                    <div class="mt-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        <p class="font-medium">Your identity has been verified.</p>
                        @if($profile->identity_reviewed_at)
                            <p class="mt-1">Verified {{ $profile->identity_reviewed_at->format('M j, Y') }}.</p>
                        @endif
                    </div>
                @elseif($identityStatus === 'rejected')
                    <div class="mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <p class="font-medium">Your identity submission needs attention.</p>
                        @if(filled($profile->identity_verification_notes))
                            <p class="mt-1">{{ $profile->identity_verification_notes }}</p>
                        @endif
                        <p class="mt-1">Correct the issue and save your profile to resubmit for review.</p>
                    </div>
                @else
                    <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        Provide your ID type, ID number, a clear copy of the document and your guarantor details to submit for verification.
                    </div>
                @endif

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="identification_type" class="mb-1 block text-sm font-medium text-slate-700">ID type</label>
                        <select id="identification_type" name="identification_type" class="w-full rounded-lg border px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('identification_type') ? 'border-red-400' : 'border-slate-300' }}">
                            <option value="">Not provided</option>
                            @foreach(\App\Models\JobSeekerProfile::IDENTIFICATION_TYPES as $value => $label)
                                <option value="{{ $value }}" @selected(old('identification_type', $profile->identification_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('identification_type') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="identification_number" class="mb-1 block text-sm font-medium text-slate-700">ID number</label>
                        <input id="identification_number" name="identification_number" type="text" value="{{ old('identification_number', $profile->identification_number) }}" autocomplete="off" class="w-full rounded-lg border px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('identification_number') ? 'border-red-400' : 'border-slate-300' }}">
                        @error('identification_number') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="identification_file" class="mb-1 block text-sm font-medium text-slate-700">ID document</label>
                        <input id="identification_file" name="identification_file" type="file" accept="image/jpeg,image/png,image/webp,application/pdf" class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-700 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800">
                        <p class="mt-1 text-xs text-slate-500">Upload a clear JPG, PNG, WebP or PDF copy. Maximum 5 MB.</p>
                        @error('identification_file') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                        @if(filled($profile->identification_document_path))
                            <a href="{{ route('jobseekers.identification', $user) }}" target="_blank" rel="noopener" class="mt-2 inline-flex items-center gap-1 text-sm font-medium text-brand-700 hover:text-brand-900">
                                <x-icon name="document" class="h-4 w-4" /> View uploaded ID document
                            </a>
                        @endif
                    </div>

                    <div class="sm:col-span-2 border-t border-slate-200 pt-4">
                        <h3 class="font-display text-sm font-semibold text-brand-900">Guarantor details</h3>
                        <p class="mt-1 text-sm text-slate-600">Provide someone who can vouch for you. Admins may contact your guarantor as part of identity verification.</p>

                        <div class="mt-3 grid gap-4 sm:grid-cols-2">
                            <div>
                                <label for="guarantor_name" class="mb-1 block text-sm font-medium text-slate-700">Guarantor's full name</label>
                                <input id="guarantor_name" name="guarantor_name" type="text" value="{{ old('guarantor_name', $profile->guarantor_name) }}" autocomplete="off" class="w-full rounded-lg border px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('guarantor_name') ? 'border-red-400' : 'border-slate-300' }}">
                                @error('guarantor_name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="guarantor_phone" class="mb-1 block text-sm font-medium text-slate-700">Guarantor's phone number</label>
                                <input id="guarantor_phone" name="guarantor_phone" type="tel" value="{{ old('guarantor_phone', $profile->guarantor_phone) }}" autocomplete="off" placeholder="e.g. 0803 622 7769" class="w-full rounded-lg border px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('guarantor_phone') ? 'border-red-400' : 'border-slate-300' }}">
                                @error('guarantor_phone') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label for="guarantor_address" class="mb-1 block text-sm font-medium text-slate-700">Guarantor's address</label>
                                <textarea id="guarantor_address" name="guarantor_address" rows="3" maxlength="500" class="w-full rounded-lg border px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('guarantor_address') ? 'border-red-400' : 'border-slate-300' }}">{{ old('guarantor_address', $profile->guarantor_address) }}</textarea>
                                @error('guarantor_address') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="summary" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Professional summary</h2>
                <p class="mt-1 text-sm text-slate-600">A short introduction employers will read first.</p>
                <div class="mt-4">
                    <label for="bio" class="sr-only">Professional summary</label>
                    <textarea id="bio" name="bio" rows="5" maxlength="2000" placeholder="Describe your experience, strengths and the kind of role you are looking for." class="w-full rounded-lg border px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30 {{ $errors->has('bio') ? 'border-red-400' : 'border-slate-300' }}">{{ old('bio', $profile->bio) }}</textarea>
                    <p class="mt-1 text-xs text-slate-500">Up to 2,000 characters.</p>
                    @error('bio') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
            </section>

            <section id="skills" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Skills</h2>
                <p class="mt-1 text-sm text-slate-600">Separate skills with commas. These are used to recommend suitable roles.</p>
                <div class="mt-4">
                    <label for="skills_text" class="sr-only">Skills</label>
                    <input id="skills_text" name="skills_text" type="text" value="{{ $skillsValue }}" placeholder="e.g. Customer service, Cooking, Driving, Bookkeeping" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                    @error('skills_text') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
            </section>

            <section id="education" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Education</h2>
                <p class="mt-1 text-sm text-slate-600">Select your highest level of education.</p>
                <div class="mt-4">
                    <label for="education_level" class="sr-only">Education level</label>
                    <select id="education_level" name="education_level" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        <option value="">Not specified</option>
                        @foreach($educationLevels as $level)
                            <option value="{{ $level }}" @selected(old('education_level', $profile->education_level) === $level)>{{ $level }}</option>
                        @endforeach
                        @if($profile->education_level && ! in_array($profile->education_level, $educationLevels, true))
                            <option value="{{ $profile->education_level }}" selected>{{ $profile->education_level }}</option>
                        @endif
                    </select>
                    @error('education_level') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
            </section>

            <section id="experience" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Work experience</h2>
                <p class="mt-1 text-sm text-slate-600">Tell employers your overall experience level.</p>
                <div class="mt-4">
                    <label for="experience_level" class="sr-only">Experience level</label>
                    <select id="experience_level" name="experience_level" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        <option value="">Not specified</option>
                        @foreach($experienceLevels as $level)
                            <option value="{{ $level }}" @selected(old('experience_level', $profile->experience_level) === $level)>{{ $level }}</option>
                        @endforeach
                        @if($profile->experience_level && ! in_array($profile->experience_level, $experienceLevels, true))
                            <option value="{{ $profile->experience_level }}" selected>{{ $profile->experience_level }}</option>
                        @endif
                    </select>
                    @error('experience_level') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </div>
            </section>

            <section id="preferences" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Career preferences</h2>
                <p class="mt-1 text-sm text-slate-600">Used to match you with relevant vacancies.</p>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="employment_preference" class="mb-1 block text-sm font-medium text-slate-700">Preferred work type</label>
                        <select id="employment_preference" name="employment_preference" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                            <option value="">No preference</option>
                            @foreach(['full_time' => 'Full time', 'part_time' => 'Part time', 'contract' => 'Contract', 'temporary' => 'Temporary', 'domestic' => 'Domestic', 'any' => 'Any'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('employment_preference', $profile->employment_preference) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('employment_preference') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="preferred_locations_text" class="mb-1 block text-sm font-medium text-slate-700">Preferred locations</label>
                        <input id="preferred_locations_text" name="preferred_locations_text" type="text" value="{{ $locationsValue }}" placeholder="e.g. Lagos, Abuja" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                        <p class="mt-1 text-xs text-slate-500">Separate multiple locations with commas.</p>
                        @error('preferred_locations_text') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                    </div>
                </div>

                <fieldset class="mt-4">
                    <legend class="text-sm font-medium text-slate-700">Preferred job categories</legend>
                    @if($categories->isEmpty())
                        <p class="mt-1 text-sm text-slate-500">No job categories have been configured yet.</p>
                    @else
                        <div class="mt-2 grid gap-2 sm:grid-cols-2">
                            @foreach($categories as $category)
                                <label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <input type="checkbox" name="preferred_categories[]" value="{{ $category->id }}" @checked(in_array((string) $category->id, $selectedCategories, true)) class="h-4 w-4 rounded border-slate-300 text-brand-700 focus:ring-brand-500">
                                    {{ $category->name }}
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('preferred_categories') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
                </fieldset>

                <label class="mt-4 flex items-start gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-700">
                    <input type="checkbox" name="available_for_work" value="1" @checked(old('available_for_work', $profile->available_for_work ?? true)) class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-700 focus:ring-brand-500">
                    <span>
                        <span class="font-medium">Available for work</span>
                        <span class="block text-xs text-slate-500">Let employers know you are actively looking.</span>
                    </span>
                </label>
            </section>

            <div class="flex flex-wrap items-center gap-3">
                <button type="submit" class="inline-flex min-h-[44px] items-center gap-2 rounded-lg bg-brand-700 px-6 py-2.5 text-sm font-semibold text-white hover:bg-brand-800">
                    <x-icon name="check" class="h-4 w-4" /> Save profile
                </button>
                <a href="{{ route('jobseeker.dashboard') }}" class="text-sm font-medium text-slate-600 hover:text-brand-700">Back to dashboard</a>
            </div>
        </form>

        <aside class="space-y-4">
            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-2">
                    <h2 class="font-display text-base font-semibold text-brand-900">Profile completion</h2>
                    <span class="font-display text-xl font-bold text-brand-900">{{ $completion['percent'] }}%</span>
                </div>
                <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-100" role="progressbar" aria-valuenow="{{ $completion['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="Profile completion">
                    <div class="h-full rounded-full bg-gold-500" data-progress="{{ $completion['percent'] }}"></div>
                </div>
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach($completion['checks'] as $check)
                        <li class="flex items-start gap-2">
                            <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full {{ $check['complete'] ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-400' }}" aria-hidden="true">
                                <x-icon :name="$check['complete'] ? 'check' : 'plus'" class="h-3 w-3" />
                            </span>
                            <span class="{{ $check['complete'] ? 'text-slate-500' : 'font-medium text-slate-700' }}">{{ $check['label'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">CV / documents</h2>
                <p class="mt-1 text-sm text-slate-600">You have {{ $user->cvs()->count() }} CV{{ $user->cvs()->count() === 1 ? '' : 's' }} on file.</p>
                <a href="{{ route('cvs.index') }}" class="mt-3 inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <x-icon name="document" class="h-4 w-4" /> Manage CVs
                </a>
            </section>

            <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="font-display text-base font-semibold text-brand-900">Profile visibility</h2>
                <p class="mt-1 text-sm text-slate-600">Your full profile and CV are shared only with employers whose vacancies you apply for. Your name and headline-level details may appear to employers reviewing applications.</p>
            </section>
        </aside>
    </div>
@endsection
