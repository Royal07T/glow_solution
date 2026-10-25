<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')
            ->orderBy('target_role')
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        return view('admin.subscription-plans.index', compact('plans'));
    }

    public function create()
    {
        $plan = new SubscriptionPlan([
            'target_role' => 'job_seeker',
            'interval' => 'monthly',
            'price' => 0,
            'currency' => 'NGN',
            'entitlements' => [],
            'is_active' => true,
            'sort_order' => (int) SubscriptionPlan::max('sort_order') + 1,
        ]);

        return view('admin.subscription-plans.create', compact('plan'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name']);
        $data['is_free'] = (float) $data['price'] === 0.0;

        $plan = SubscriptionPlan::create($data);

        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', "Plan \"{$plan->name}\" created.");
    }

    public function edit(SubscriptionPlan $subscriptionPlan)
    {
        $subscriptionPlan->loadCount('subscriptions');

        return view('admin.subscription-plans.edit', ['plan' => $subscriptionPlan]);
    }

    public function update(Request $request, SubscriptionPlan $subscriptionPlan)
    {
        $data = $this->validated($request, $subscriptionPlan);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? null, $data['name'], $subscriptionPlan);
        $data['is_free'] = (float) $data['price'] === 0.0;

        $subscriptionPlan->update($data);

        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', "Plan \"{$subscriptionPlan->name}\" updated.");
    }

    public function destroy(SubscriptionPlan $subscriptionPlan)
    {
        if ($subscriptionPlan->subscriptions()->exists()) {
            return redirect()
                ->route('admin.subscription-plans.index')
                ->with('error', 'This plan has active or historical subscribers. Deactivate it instead of deleting.');
        }

        $name = $subscriptionPlan->name;
        $subscriptionPlan->delete();

        return redirect()
            ->route('admin.subscription-plans.index')
            ->with('success', "Plan \"{$name}\" deleted.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?SubscriptionPlan $plan = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('subscription_plans', 'slug')->ignore($plan?->id),
            ],
            'target_role' => ['required', Rule::in(['job_seeker', 'employer'])],
            'interval' => ['required', Rule::in(['weekly', 'monthly', 'quarterly', 'annual'])],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999999'],
            'currency' => ['required', 'string', 'size:3'],
            'description' => ['nullable', 'string', 'max:2000'],
            'entitlements' => ['nullable', 'json'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ]);

        $validated['currency'] = strtoupper($validated['currency']);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['entitlements'] = $this->decodeEntitlements($validated['entitlements'] ?? null);

        return $validated;
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeEntitlements(?string $json): array
    {
        if (! $json) {
            return [];
        }

        return json_decode($json, true) ?: [];
    }

    private function uniqueSlug(?string $slug, string $name, ?SubscriptionPlan $plan = null): string
    {
        $slug = $slug ?: Str::slug($name);
        $base = $slug;

        for ($i = 2; SubscriptionPlan::where('slug', $slug)->when($plan, fn ($query) => $query->whereKeyNot($plan->getKey()))->exists(); $i++) {
            $slug = $base.'-'.$i;
        }

        return $slug;
    }
}
