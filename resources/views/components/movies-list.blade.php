@props([ 'movies' => null ])

{{-- Movies list --}}
<div class="w-full flex gap-5">
  {{-- Movies filters --}}
  <div class="w-2/6 h-full">
    <form method="GET" action="{{ route('movies.now') }}">
  
    {{-- Search --}}
    <div class="flex flex-col gap-4">
      <x-text-input placeholder="Search movies..."></x-text-input>
    </div>

    {{-- Rating --}}
    <div class="flex flex-col gap-3 mt-6">
      <h3 class="text-gray-400 text-xs tracking-widest uppercase">Rating</h3>
      @foreach(['PG', 'PG-13', 'R', 'TV-14'] as $rating)
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" name="rating[]" value="{{ $rating }}"
            {{ in_array($rating, request('rating', [])) ? 'checked' : '' }}
            class="rounded border-white/20 bg-white/5 text-accent">
          <span class="text-gray-400 text-sm">{{ $rating }}</span>
        </label>
      @endforeach
    </div>
  </div>

  {{-- Movies cards --}}
  <div class="w-full grid grid-cols-2 md:grid-cols-3 gap-4">
    @foreach ($movies as $movie)
      <x-movie-card :movie="$movie"></x-movie-card>          
    @endforeach
  </div>
</div>