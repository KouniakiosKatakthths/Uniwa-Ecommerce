@props([ 'movies' => null, 'route' => null, "showDays" => true ])
<div class="w-full flex gap-5">

  {{-- Movies filters --}}
  <div class="w-2/6 flex flex-col gap-5">
    {{-- Clear filters --}}
    @if(request()->hasAny(['search', 'day']))
      <x-button variant="ghost" :href="$route" class="text-gray-500 text-sm text-center hover:text-gray-300">Clear filters</x-button>
    @endif
    
    {{-- Search form --}}
    <form method="GET" :action="$route" class="flex flex-col gap-1">
      <x-input-label>Search</x-input-label>
      <x-text-input name="search" value="{{ request('search') }}" placeholder="Search movies..."></x-text-input>
      <x-button type="submit" class="w-full mt-2">Search</x-button>
    </form>

    {{-- Day filter --}}
    @if ($showDays)
      <form method="GET" :action="$route" class="flex flex-col gap-1">
        <x-input-label>Day</x-input-label>
        <div class="flex flex-col gap-2">
          @for($i = 0; $i <= 5; $i++)
            @php $day = now('Europe/Athens')->addDays($i) @endphp
            <button type="submit" name="day" value="{{ $day->format('Y-m-d') }}"
              class="text-left px-3 py-2 rounded-sm text-sm transition-colors
                {{ request('day') === $day->format('Y-m-d') ? 'bg-accent text-[#0a0a0f]' : 'bg-white/5 text-gray-400 hover:bg-white/10' }}">
              {{ $day->format('D d') }}
            </button>
          @endfor
        </div>
      </form>
    @endif
  </div>

  {{-- Movies cards --}}
  <div class="w-full grid grid-cols-2 md:grid-cols-3 gap-4">
    @foreach ($movies as $movie)
      <x-movie-card :movie="$movie"></x-movie-card>
    @endforeach
  </div>
</div>