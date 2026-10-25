@props([
    'src' => null,
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'lazy' => true,
    'priority' => false,
    'sizes' => null,
    'srcset' => null,
    'overlay' => false,
    'overlayOpacity' => '70'
])

<div class="relative {{ $overlay ? 'overflow-hidden' : '' }}">
    <img 
        src="{{ $src }}" 
        alt="{{ $alt }}" 
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        @if($lazy && !$priority) loading="lazy" @endif
        @if($priority) loading="eager" fetchpriority="high" @endif
        @if($srcset) srcset="{{ $srcset }}" @endif
        @if($sizes) sizes="{{ $sizes }}" @endif
        class="{{ $class }} w-full h-full object-cover"
        @error($src) onerror="this.style.display='none'" @enderror
    />
    @if($overlay)
        <div class="absolute inset-0 bg-[#041e3a]" style="opacity: {{ $overlayOpacity }}%"></div>
    @endif
</div>
