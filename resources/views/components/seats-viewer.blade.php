@props(['showtime', 'takenSeats', 'action' => null, 'submit_text' => null])

<form 
  method="POST" 
  action="{{ $action }}"
  class="flex flex-col gap-4"
  x-data="seatPicker({{ $showtime->total_seats }}, {{ json_encode($takenSeats) }})">
  @csrf

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
            'bg-white/10 hover:bg-white/25 text-gray-300': selected != n && !taken.includes(String(n)),
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
    <div x-show="selected" class="text-center text-gray-300 mt-2">
      Seat <span class="text-white font-semibold" x-text="selected"></span> selected
      &bull; €{{ number_format($showtime->ticket_price, 2) }}
    </div>

    {{-- Exposes selected to parent form via hidden input --}}
    <input type="hidden" name="seat" :value="selected">
  </div>

  @error('seat')
    <p class="text-red-500 text-sm text-center">{{ $message }}</p>
  @enderror

  @if ($submit_text !== null)
    <button
      type="submit"
      :disabled="!selected"
      class="mt-2 w-2/3 mx-auto py-3 rounded-lg bg-accent text-black font-semibold transition disabled:opacity-40 disabled:cursor-not-allowed">
      {{ $submit_text }}
    </button>
  @endif
</form>

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