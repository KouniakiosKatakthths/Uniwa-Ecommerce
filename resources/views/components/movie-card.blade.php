@props([ 'movie' ])

<div class="cursor-pointer group w-95 h-150 shrink-0">
  <div class="relative rounded-sm overflow-hidden mb-3">
    <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"
         class="w-85 h-130 transition-transform rounded-lg duration-500 group-hover:scale-105 m-5">
  </div>
  <p class="text-base font-semibold text-gray-300 truncate">{{ $movie->title }}</p>
  <div class="flex justify-between">
    <p class="text-sm font-normal text-gray-500">{{ $movie->rating }} &#8226; {{ $movie->getDurationFormatted() }}</p>
    <span class="bottom-2 right-2 text-xs text-[#c8a96e] font-medium">★ 4.8</span>
  </div>
</div>