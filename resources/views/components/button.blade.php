@props(['variant' => 'primary', 'href' => null])

@php
$classes = '';

if ($variant === 'primary')
  $classes = 'px-6 py-2.5 bg-accent text-gray-900 text-sm font-semibold rounded-lg cursor-pointer tracking-widest uppercase hover:bg-[#b8956a] transition-all';
else if ($variant === 'danger')
  $classes = 'px-6 py-2.5 border-red-500/30 border tracking-widest text-red-400 text-sm font-semibold rounded-lg cursor-pointer tracking-widest uppercase hover:bg-red-500/10 transition-all';
else if ($variant === 'ghost')
  $classes = 'px-6 py-2.5 border border-white/20 text-white text-sm tracking-widest cursor-pointer rounded-lg uppercase hover:border-white/50 transition-all';
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