@props(['variant' => 'primary', 'href' => null])

@php
$classes = $variant === 'primary'
    ? 'px-6 py-2.5 bg-[#c8a96e] text-[#0a0a0f] text-sm font-semibold rounded-lg cursor-pointer tracking-widest uppercase hover:bg-[#b8956a] transition-all'
    : 'px-6 py-2.5 border border-white/20 text-white text-sm tracking-widest cursor-pointer rounded-lg uppercase hover:border-white/50 transition-all';
@endphp

@if($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
  </a>
@elseif($attributes->has('x-bind:href') || $attributes->has(':href'))
  <a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
  </a>
@else
  <button {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
  </button>
@endif