@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#0a3d6b] focus:ring-[#D9E2EC] rounded-md shadow-sm']) }}>
