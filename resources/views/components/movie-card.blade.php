<a href="{{ route("movies.show", [ 'movie_id' => $movie->id ]) }}" class="cursor-pointer group w-70 h-155 shrink-0 overflow-hidden">
  <div class="relative rounded-sm overflow-hidden">
    <img src="{{ $movie->poster_url }}" alt="{{ $movie->title }}"
         class="w-70 h-90 transition-transform rounded-lg duration-500 group-hover:scale-105 m-5">
  </div>

  <div class="ml-5 mr-5">
    <p class="text-base font-semibold text-gray-300 truncate">{{ $movie->title }}</p>
    <div class="flex justify-between">
      <p class="text-sm font-normal text-gray-500">{{ $movie->rating }} &#8226; {{ $movie->getDurationFormatted() }}</p>
      <span class="bottom-2 right-2 text-xs text-accent font-medium">★ 4.8</span>
    </div>
  </div>
</a>