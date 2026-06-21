@props([ 'show' => null ])

@php
  $isEditing = $show !== null;
  $action = $isEditing 
    ? route('showtimes.update', $show->id) 
    : route('showtimes.store');
@endphp

<form method="POST" action="{{ $action }}" class="flex flex-col gap-5">
  @csrf
  @if($isEditing)
    @method('PUT')
  @endif

  <x-flash-messages></x-flash-messages>

  {{-- Movie field --}}
  <div class="flex flex-col gap-1" x-data='movieSearch(@json(old('movie_id')), @json($show?->movie?->only('id', 'title')))'>
    {{-- Hidden input for submit --}}
    <input type="hidden" name="movie_id" :value="selectedMovie?.id">

    <x-input-label>Movie</x-input-label>

    {{-- Search input --}}
    <div class="relative">
      <x-text-input
        class="w-full"
        x-model="query"
        x-ref="input"
        @input.debounce.300ms="search"
        @focus="if (query) open = true"
        @click.outside="open = false"
        @keydown.escape="open = false"
        @keydown.arrow-down.prevent="focusResult(0)"
        placeholder="Search for a movie..."
      />

      {{-- Dropdown results --}}
      <ul
        x-show="open && results.length > 0"
        x-transition
        class="absolute z-10 mt-1 w-full dark:placeholder-gray-600 dark:bg-[#293447] dark:border-white/10 dark:text-gray-300 dark:border-solid dark:border dark:focus:border-white/20 bg-white border border-gray-200 rounded-md shadow-lg max-h-60 overflow-auto">
        <template x-for="(movie, i) in results" :key="movie.id">
          <li
            @click="select(movie)"
            @keydown.enter.prevent="select(movie)"
            @keydown.arrow-down.prevent="focusResult(i + 1)"
            @keydown.arrow-up.prevent="focusResult(i - 1)"
            :tabindex="0"
            class="px-4 py-2 cursor-pointer dark:hover:bg-slate-600 dark:focus:bg-slate-600 hover:bg-gray-100 focus:bg-gray-100 outline-none text-sm"
            x-text="movie.title">
          </li>
        </template>
      </ul>

      {{-- Loading spinner --}}
      <div x-show="loading" class="absolute right-3 top-1/2 -translate-y-1/2">
        <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
      </div>
    </div>

    <x-input-label>Movie ID</x-input-label>
    {{-- Input that actually submits --}}
    <input class="
      border-gray-100 bg-gray-200
      dark:placeholder-gray-600 dark:bg-white/15 dark:border-white/10 dark:text-gray-300 dark:border-solid dark:border dark:focus:border-white/20
      focus:border-indigo-500 focus:ring-indigo-500 
      rounded-lg shadow-sm px-4 py-2 text-sm focus:outline-none" disabled :value="selectedMovie?.id">

    <x-input-error :messages="$errors->get('movie_id')"></x-input-error>
  </div>

  {{-- Room field --}}
  <div class="flex flex-col gap-1">
    <x-input-label>Showtime room name</x-input-label>
    <x-text-input name="room" value="{{ old('room', $show?->room) }}" placeholder="Room name"></x-text-input>
    <x-input-error :messages="$errors->get('room')"></x-input-error>
  </div>


  {{-- Starts at datetime field --}}
  <div class="flex flex-col gap-1">
    <x-input-label>Show starts at</x-input-label>
    <x-text-input name="starts_at" type="datetime-local" value="{{ old('starts_at', $show?->starts_at) }}"></x-text-input>
    <x-input-error :messages="$errors->get('starts_at')"></x-input-error>
  </div>

  {{-- Ticket price field --}}
  <div class="flex flex-col gap-1">
    <x-input-label>Ticket price</x-input-label>
    <x-text-input name="ticket_price" type="number" step="0.1" min="0" value="{{ old('ticket_price', $show?->ticket_price) }}" placeholder="Ticket price in euros"></x-text-input>
    <x-input-error :messages="$errors->get('ticket_price')"></x-input-error>
  </div>

  {{-- Submit --}}
  <div class="flex gap-3 justify-end">
    <x-button type="submit">{{ $isEditing ? 'Update showtime' : 'Create showtime' }}</x-button>
  </div>
</form>