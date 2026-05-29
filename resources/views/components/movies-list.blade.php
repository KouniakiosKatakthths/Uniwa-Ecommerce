@props([ 'movies' => null ])
<div x-data="{
  search: '',
  ratings: [],
  genres: [],
  selectedDay: null,
  days: [
    @for($i = 0; $i < 5; $i++)
      { label: '{{ now()->addDays($i)->format('D d') }}', value: '{{ now()->addDays($i)->format('Y-m-d') }}' },
    @endfor
  ],
  movies: {{ $movies->map(fn($m) => [
    'id'         => $m->id,
    'title'      => $m->title,
    'rating'     => $m->rating,
    'duration'   => $m->duration,
    'genre'      => $m->genre,
    'poster_url' => $m->poster_url,
    'showtimes'  => $m->showtimes->map(fn($s) => [
      'date'  => $s->starts_at->format('Y-m-d'),
      'time'  => $s->starts_at->format('H:i'),
    ])
  ])->toJson() }},
  get filtered() {
    return this.movies.filter(m => {
      const matchSearch  = m.title.toLowerCase().includes(this.search.toLowerCase())
      const matchRating  = this.ratings.length === 0 || this.ratings.includes(m.rating)
      const matchGenre   = this.genres.length === 0 || this.genres.includes(m.genre)
      const matchDay     = this.selectedDay === null || m.showtimes.some(s => s.date === this.selectedDay)
      return matchSearch && matchRating && matchGenre && matchDay
    })
  },
  toggleRating(r)  { this.ratings.includes(r)  ? this.ratings  = this.ratings.filter(x => x !== r)  : this.ratings.push(r)  },
  toggleGenre(g)   { this.genres.includes(g)   ? this.genres   = this.genres.filter(x => x !== g)   : this.genres.push(g)   },
  toggleDay(d)     { this.selectedDay = this.selectedDay === d ? null : d },
}">
  {{-- Movies list --}}
  <div class="w-full flex gap-5">
    {{-- Movies filters --}}
    <div class="w-2/6 flex flex-col gap-5">
      <form method="GET" class="flex flex-col gap-3" action="{{ route('movies.now') }}">
    
      {{-- Search --}}
      <div class="flex flex-col gap-1">
        <x-input-label>Search </x-input-label>
        <x-text-input placeholder="Search movies..."></x-text-input>
      </div>

      {{-- Day selector --}}
      <div class="flex flex-col gap-1">
        <x-input-label>Day</x-input-label>
        <div class="flex flex-col gap-2">
          <template x-for="day in days" :key="day.value">
            <button @click="toggleDay(day.value)"
              :class="selectedDay === day.value
                ? 'bg-accent text-[#0a0a0f]'
                : 'bg-white/5 text-gray-400 hover:bg-white/10'"
              class="text-left px-3 py-2 rounded-sm text-sm transition-colors"
              x-text="day.label">
            </button>
          </template>
        </div>
      </div>
    </div>

    {{-- Movies cards --}}
    <div class="w-full grid grid-cols-2 md:grid-cols-3 gap-4">
      @foreach ($movies as $movie)
        <x-movie-card :movie="$movie"></x-movie-card>          
      @endforeach
    </div>
  </div>
</div>