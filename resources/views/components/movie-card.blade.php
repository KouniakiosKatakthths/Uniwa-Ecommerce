<a href="{{ route("movies.show", $movie) }}" class="cursor-pointer group w-full max-w-75 shrink-0 overflow-hidden">
  <div class="relative rounded-sm overflow-hidden">
    <img src="{{ $movie->getMoviePoster() }}" alt="{{ $movie->title }}"
        class="w-5/6 aspect-2/3 object-cover transition-transform rounded-lg duration-500 group-hover:scale-105 mx-auto my-5">
  </div>
  <div class="ml-5 mr-5">
    <p class="text-base font-semibold text-gray-300 truncate">{{ $movie->title }}</p>
    <div class="flex justify-between">
      <p class="text-sm font-normal text-gray-500">{{ $movie->rating }} &#8226; {{ $movie->getDurationFormatted() }}</p>
      @if ($movie->tmdb_rating !== null)
        <span class="text-xs text-accent font-medium">★ {{ $movie->tmdb_rating }}</span>
      @endif
    </div>
  </div>
</a>