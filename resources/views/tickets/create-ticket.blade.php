@extends("layouts.cinema")

@section("content")

<div class="flex flex-col gap-10 sm:px-6 max-w-7xl mx-auto lg:px-8 py-5">
  {{-- Showtime info --}}
  <div class="flex flex-col sm:flex-row gap-6 w-full">
    <div class="relative group rounded-sm overflow-hidden w-auto shrink-0">
      <img 
        src="{{ $showtime->movie->getMoviePoster() }}" 
        alt="{{ $showtime->movie->title }}" 
        class="sm:w-20 md:w-28 lg:w-38 max-w-80 aspect-2/3 object-cover rounded-lg transition-transform duration-500">
    </div>
    <div class="flex flex-col ">
      <h1 class="text-4xl font-bold mb-2 text-gray-100">{{ $showtime->movie->title }}</h1>
      <p class="text-gray-400 text-lg">{{ $showtime->starts_at->format('l, d M Y \a\t H:i') }}</p>
      <p class="text-gray-400 text-lg">Room: <span class="text-gray-200 text-lg">{{ $showtime->room }}</span></p>
      <p class="text-accent font-semibold text-lg">€{{ number_format($showtime->ticket_price, 2) }}</p>
    </div>
  </div>

  <h2 class="text-2xl font-bold text-gray-200">Select your seat</h2>
  {{-- Seat picker --}}
  <form method="POST" action="{{ route('tickets.store', $showtime) }}" x-data="seatPicker({{ $showtime->total_seats }}, {{ json_encode($takenSeats) }})">
    @csrf
    <input type="hidden" name="seat" :value="selected">
    <div class="flex flex-col gap-4">
      {{-- Screen indicator --}}
      <div class="w-2/3 mx-auto h-2 bg-accent/40 rounded-full mb-4 relative">
        <span class="absolute -bottom-5 left-1/2 -translate-x-1/2 text-xs text-gray-500">SCREEN</span>
      </div>

      {{-- Seat grid --}}
      <div class="grid gap-2 justify-center" style="grid-template-columns: repeat(10, minmax(2.5rem, 3.5rem))">
        <template x-for="n in capacity" :key="n">
          <button
              type="button"
              @click="toggle(n)"
              :disabled="taken.includes(String(n))"
              :class="{
                  'bg-red-500 cursor-not-allowed opacity-60': taken.includes(String(n)),
                  'bg-accent text-black':                     selected == n && !taken.includes(String(n)),
                  'bg-white/10 hover:bg-white/25 text-gray-300':            selected != n && !taken.includes(String(n)),
              }"
              class="aspect-square w-full max-w-14 rounded text-xs font-medium transition"
              x-text="n"> 
          </button>
        </template>
      </div>

      {{-- Legend --}}
      <div class="flex gap-6 justify-center text-sm mt-2 dark:bg-slate-800 p-3 rounded-lg w-auto mx-auto">
        <span class="flex items-center text-gray-200 gap-2"><span class="w-4 h-4 rounded bg-white/10"></span> Available</span>
        <span class="flex items-center text-gray-200 gap-2"><span class="w-4 h-4 rounded bg-accent"></span> Selected</span>
        <span class="flex items-center text-gray-200 gap-2"><span class="w-4 h-4 rounded bg-red-500"></span> Taken</span>
      </div>

      {{-- Selected summary --}}
      <div x-show="selected" class="text-center text-gray-300">
        Seat <span class="text-white font-semibold" x-text="selected"></span> selected
        &bull; €{{ number_format($showtime->ticket_price, 2) }}
      </div>

      @error('seat')
        <p class="text-red-500 text-sm text-center">{{ $message }}</p>
      @enderror

      <button
        type="submit"
        :disabled="!selected"
        class="mt-2 w-2/3 mx-auto py-3 rounded-lg bg-accent text-black font-semibold transition
               disabled:opacity-40 disabled:cursor-not-allowed">
        Confirm Booking
      </button>

    </div>
  </form>
</div>


<script>
function seatPicker(capacity, taken) {
  return {
    capacity,
    taken,
    selected: null,
    toggle(n) {
      if (this.taken.includes(String(n))) return;
      this.selected = this.selected === n ? null : n;
    }
  }
}
</script>
@endsection