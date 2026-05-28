@props(['variant' => 'primary', 'href' => null])

@if($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $variant === 'primary' 
    ? 'px-6 py-2.5 bg-[#c8a96e] text-[#0a0a0f] text-sm tracking-widest uppercase hover:bg-[#b8956a] transition-all'
    : 'px-6 py-2.5 border border-white/20 text-white text-sm tracking-widest uppercase hover:border-white/50 transition-all']) }}>
    {{ $slot }}
  </a>
@else
  <button {{ $attributes->merge(['class' => $variant === 'primary'
    ? 'px-6 py-2.5 bg-[#c8a96e] text-[#0a0a0f] text-sm tracking-widest uppercase hover:bg-[#b8956a] transition-all'
    : 'px-6 py-2.5 border border-white/20 text-white text-sm tracking-widest uppercase hover:border-white/50 transition-all']) }}>
    {{ $slot }}
  </button>
@endif