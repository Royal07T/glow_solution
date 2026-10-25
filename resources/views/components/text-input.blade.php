@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-[#F5F3FF0 focus:ring-#F5F3FF0 rounded-md shadow-sm']) }}>
