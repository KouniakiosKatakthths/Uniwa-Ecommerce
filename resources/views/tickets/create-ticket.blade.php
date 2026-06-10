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
  <x-seats-viewer :showtime="$showtime" :takenSeats="$takenSeats" :action="route('tickets.store', $showtime)" submit_text="Confirm Booking"></x-seats-viewer>
</div>
@endsection