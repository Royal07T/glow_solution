@props([
    'state' => null,
    'city' => null,
    'stateName' => 'state',
    'cityName' => 'city',
    'inputClass' => 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30',
])

@php
    $stateFieldId = 'state-'.\Illuminate\Support\Str::slug($stateName);
    $cityFieldId = 'city-'.\Illuminate\Support\Str::slug($cityName);
    $states = \App\Models\State::query()->orderBy('name')->get(['id', 'name']);
    $selectedState = old($stateName, $state);
    $selectedCity = old($cityName, $city);
    $selectedStateModel = filled($selectedState) ? $states->firstWhere('name', $selectedState) : null;
    $lgas = $selectedStateModel
        ? \App\Models\Lga::query()->where('state_id', $selectedStateModel->id)->orderBy('name')->get(['name'])
        : collect();
    $legacyState = filled($selectedState) && ! $selectedStateModel;
    $legacyCity = filled($selectedCity) && ! $lgas->contains(fn ($lga) => $lga->name === $selectedCity);
@endphp

<div>
    <label for="{{ $stateFieldId }}" class="mb-1 block text-sm font-medium text-slate-700">State</label>
    <select
        id="{{ $stateFieldId }}"
        name="{{ $stateName }}"
        autocomplete="address-level1"
        data-location-state
        data-location-city="{{ $cityFieldId }}"
        data-lga-url="{{ route('locations.lgas') }}"
        @class([$inputClass, 'border-danger' => $errors->has($stateName)])
    >
        <option value="">Select state</option>
        @foreach ($states as $stateOption)
            <option value="{{ $stateOption->name }}" data-state-id="{{ $stateOption->id }}" @selected($selectedState === $stateOption->name)>
                {{ $stateOption->name }}
            </option>
        @endforeach
        @if ($legacyState)
            <option value="{{ $selectedState }}" selected>{{ $selectedState }}</option>
        @endif
    </select>
    @error($stateName)
        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="{{ $cityFieldId }}" class="mb-1 block text-sm font-medium text-slate-700">City / LGA</label>
    <select
        id="{{ $cityFieldId }}"
        name="{{ $cityName }}"
        autocomplete="address-level2"
        data-location-city
        @class([$inputClass, 'border-danger' => $errors->has($cityName)])
    >
        <option value="">Select city / LGA</option>
        @foreach ($lgas as $lga)
            <option value="{{ $lga->name }}" @selected($selectedCity === $lga->name)>{{ $lga->name }}</option>
        @endforeach
        @if ($legacyCity)
            <option value="{{ $selectedCity }}" selected>{{ $selectedCity }}</option>
        @endif
    </select>
    @error($cityName)
        <p class="mt-1 text-sm text-danger">{{ $message }}</p>
    @enderror
</div>
