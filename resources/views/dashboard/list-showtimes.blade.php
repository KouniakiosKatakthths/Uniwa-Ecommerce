@extends('layouts.cinema')
@section('content')
<div class="max-w-7xl mx-auto px-6 lg:px-8 py-10">
  <x-flash-messages class="mb-6"></x-flash-messages>

  <div class="flex justify-between items-center mb-6">
    <h1 class="text-gray-200 text-3xl font-bold">Showtimes</h1>
    <x-button :href="route('showtimes.create')">Add showtime</x-button>
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('showtimes.index') }}" class="flex gap-3 mb-6 flex-wrap">
    <x-text-input
      name="movie"
      type="text"
      placeholder="Search movie..."
      value="{{ request('movie') }}"
    />

    <x-text-input name="day" type="date" value="{{ request('day') }}"></x-text-input>
    <x-selector name="room">
      <option value="">All rooms</option>
      @foreach($rooms as $room)
        <option value="{{ $room }}" {{ request('room') === $room ? 'selected' : '' }}>{{ $room }}</option>
      @endforeach
    </x-selector>
    <x-button type="submit">Filter</x-button>
    @if(request()->hasAny(['day', 'room']))
      <x-button variant="ghost" :href="route('showtimes.index')">Clear</x-button>
    @endif
  </form>

  {{-- Data Grid --}}
  <x-data-grid :pagination="$showtimes->links()">

    <x-slot:head>
      <x-data-grid.header>Movie</x-data-grid.header>
      <x-data-grid.header hidden="md">Room</x-data-grid.header>
      <x-data-grid.header hidden="lg">Date</x-data-grid.header>
      <x-data-grid.header>Time</x-data-grid.header>
      <x-data-grid.header hidden="sm">Price</x-data-grid.header>
      <x-data-grid.header hidden="md">Seats</x-data-grid.header>
      <x-data-grid.header></x-data-grid.header>
    </x-slot:head>

    @forelse($showtimes as $showtime)
      <x-data-grid-row>
        <x-data-grid.data>
          <div>
            <p class="text-gray-200 font-medium">
              {{ $showtime->movie->title }}
            </p>

            {{-- Mobile-only extra details --}}
            <div class="mt-1 flex flex-wrap gap-x-3 gap-y-1 text-xs text-gray-500 md:hidden">
              <span>{{ $showtime->room }}</span>
              <span>{{ $showtime->starts_at->format('D, M j Y') }}</span>
              <span>€{{ number_format($showtime->ticket_price, 2) }}</span>
            </div>
          </div>
        </x-data-grid.data>

        <x-data-grid.data hidden="md">
          {{ $showtime->room }}
        </x-data-grid.data>

        <x-data-grid.data hidden="lg">
          {{ $showtime->starts_at->format('D, M j Y') }}
        </x-data-grid.data>

        <x-data-grid.data>
          <span class="text-gray-300 font-mono">
            {{ $showtime->starts_at->format('H:i') }}
          </span>
        </x-data-grid.data>

        <x-data-grid.data hidden="sm">
          €{{ number_format($showtime->ticket_price, 2) }}
        </x-data-grid.data>

        <x-data-grid.data hidden="md">
          {{ $showtime->total_seats }}
        </x-data-grid.data>

        <x-data-grid.data class="text-right">
          <div class="flex gap-2 justify-end">
            <x-button variant="ghost" :href="route('showtimes.show', $showtime->id)">
              View
            </x-button>

            <x-button variant="primary" :href="route('showtimes.edit', $showtime->id)">
              Edit
            </x-button>

            {{-- Show delete only to admin --}}
            @if (auth()->user()->isAdmin())
              <form
                method="POST"
                action="{{ route('showtimes.destroy', $showtime->id) }}"
                onsubmit="return confirm('Delete this showtime?')"
              >
                @csrf
                @method('DELETE')

                <x-button type="submit" variant="danger">
                  Delete
                </x-button>
              </form>
            @endif
          </div>
        </x-data-grid.data>
      </x-data-grid-row>
    @empty
      <tr>
        <td colspan="7" class="px-4 py-10 text-center text-gray-500">
          No showtimes found.
        </td>
      </tr>
    @endforelse
  </x-data-grid>
</div>
@endsection