@props([ 'movie' ])

<div class="cursor-pointer group">
  <div class="relative  rounded-sm overflow-hidden mb-3">
    <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"
         class="w-100 h-85 transition-transform duration-500 group-hover:scale-105">
    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
    <span class="absolute bottom-2 right-2 text-xs text-[#c8a96e] font-medium">★ {{ $movie->score }}</span>
  </div>
  <p class="text-sm font-medium text-gray-300">{{ $movie->title }}</p>
  <p class="text-xs text-gray-500">{{ $movie->genre }} · {{ $movie->getDurationFormatted() }}</p>
</div>