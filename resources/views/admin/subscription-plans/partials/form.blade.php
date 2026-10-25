@php
    $editing = $plan->exists;
    $entitlementJson = old(
        'entitlements',
        $plan->entitlements && count($plan->entitlements)
            ? json_encode($plan->entitlements, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            : ''
    );
@endphp

<form method="POST" action="{{ $editing ? route('admin.subscription-plans.update', $plan) : route('admin.subscription-plans.store') }}" class="space-y-6">
    @csrf
    @if($editing)
        @method('PUT')
    @endif

    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="font-display text-base font-semibold text-brand-900">Plan details</h2>
        <p class="mt-1 text-sm text-slate-600">Pricing and availability shown to job seekers and employers.</p>

        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Plan name <span class="text-danger" aria-hidden="true">*</span></label>
                <input id="name" type="text" name="name" required maxlength="255" value="{{ old('name', $plan->name) }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                @error('name') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="slug" class="mb-1 block text-sm font-medium text-slate-700">Slug</label>
                <input id="slug" type="text" name="slug" maxlength="255" value="{{ old('slug', $plan->slug) }}" placeholder="auto-generated from name" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                <p class="mt-1 text-xs text-slate-500">Leave blank to generate from the plan name.</p>
                @error('slug') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="sort_order" class="mb-1 block text-sm font-medium text-slate-700">Display order</label>
                <input id="sort_order" type="number" name="sort_order" min="0" max="100000" value="{{ old('sort_order', $plan->sort_order ?? 0) }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                @error('sort_order') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="target_role" class="mb-1 block text-sm font-medium text-slate-700">Audience <span class="text-danger" aria-hidden="true">*</span></label>
                <select id="target_role" name="target_role" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                    <option value="job_seeker" @selected(old('target_role', $plan->target_role) === 'job_seeker')>Job seeker</option>
                    <option value="employer" @selected(old('target_role', $plan->target_role) === 'employer')>Employer</option>
                </select>
                @error('target_role') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="interval" class="mb-1 block text-sm font-medium text-slate-700">Billing interval <span class="text-danger" aria-hidden="true">*</span></label>
                <select id="interval" name="interval" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                    @foreach(['weekly' => 'Weekly', 'monthly' => 'Monthly', 'quarterly' => 'Quarterly', 'annual' => 'Annual'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('interval', $plan->interval) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('interval') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="price" class="mb-1 block text-sm font-medium text-slate-700">Price <span class="text-danger" aria-hidden="true">*</span></label>
                <input id="price" type="number" name="price" required min="0" step="0.01" value="{{ old('price', $plan->price ?? 0) }}" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                <p class="mt-1 text-xs text-slate-500">A price of 0 marks the plan as free.</p>
                @error('price') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="currency" class="mb-1 block text-sm font-medium text-slate-700">Currency <span class="text-danger" aria-hidden="true">*</span></label>
                <select id="currency" name="currency" required class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                    @foreach(['NGN', 'USD', 'GBP', 'EUR'] as $currency)
                        <option value="{{ $currency }}" @selected(old('currency', $plan->currency ?? 'NGN') === $currency)>{{ $currency }}</option>
                    @endforeach
                </select>
                @error('currency') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                <textarea id="description" name="description" rows="3" maxlength="2000" class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">{{ old('description', $plan->description) }}</textarea>
                @error('description') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $plan->is_active ?? true)) class="h-4 w-4 rounded border-slate-300 text-brand-700 focus:ring-brand-500">
                    Active and visible to users
                </label>
            </div>
        </div>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <h2 class="font-display text-base font-semibold text-brand-900">Entitlements</h2>
        <p class="mt-1 text-sm text-slate-600">Optional JSON object describing what the plan includes. Use <code class="rounded bg-slate-100 px-1 py-0.5 text-xs">-1</code> for unlimited.</p>

        <div class="mt-4">
            <label for="entitlements" class="mb-1 block text-sm font-medium text-slate-700">Entitlements JSON</label>
            <textarea id="entitlements" name="entitlements" rows="9" spellcheck="false" placeholder='{"active_job_listings": 5}' class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 font-mono text-xs text-slate-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">{{ $entitlementJson }}</textarea>
            @error('entitlements') <p class="mt-1 text-sm text-danger">{{ $message }}</p> @enderror
        </div>
    </section>

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-brand-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800">
            {{ $editing ? 'Save changes' : 'Create plan' }}
        </button>
        <a href="{{ route('admin.subscription-plans.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
    </div>
</form>
