@extends("layouts.cinema")
@section("content")

<div class="max-w-4xl mx-auto px-4 py-8">
  <x-flash-messages class="mb-6"></x-flash-messages>

  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <div class="flex gap-3">
      <x-button href="{{ route('showtimes.edit', $showtime) }}" variant="ghost">Edit</x-button>
      @if (auth()->user()->isAdmin())
        <form action="{{ route('showtimes.destroy', $showtime) }}" method="POST" onsubmit="return confirm('Delete this showtime?')">
          @csrf @method('DELETE')
          <x-button variant="danger" type="submit">Delete</x-button>
        </form>
      @endif
    </div>
  </div>

  <div class="flex w-full flex-col gap-5">
    {{-- Movie Info --}}
    <div class="bg-gray-800 rounded-xl p-5">
      <h2 class="text-xs uppercase tracking-widest text-gray-400 mb-3">Movie</h2>
      <a href="{{ route('movies.show', $showtime->movie) }}" class="text-xl font-bold text-white hover:text-accent">
        {{ $showtime->movie->title }}
      </a>
      <div class="mt-2 flex flex-wrap gap-3 text-sm text-gray-400">
        <span>{{ $showtime->movie->genre->value }}</span>
        <span>·</span>
        <span>{{ $showtime->movie->duration }} min</span>
        <span>·</span>
        <span>{{ $showtime->movie->rating->value }}</span>
      </div>
    </div>

    {{-- Showtime Info --}}
    <div class="bg-gray-800 rounded-xl p-5">
      <h2 class="text-xs uppercase tracking-widest text-gray-400 mb-4">Showtime Info</h2>
      <dl class="grid grid-cols-2 gap-4 text-sm">
        <div>
          <dt class="text-gray-400">Date</dt>
          <dd class="text-white font-medium mt-1">{{ $showtime->starts_at->format('D, d M Y') }}</dd>
        </div>
        <div>
          <dt class="text-gray-400">Time</dt>
          <dd class="text-white font-medium mt-1">{{ $showtime->starts_at->format('H:i') }}</dd>
        </div>
        <div>
          <dt class="text-gray-400">Room</dt>
          <dd class="text-white font-medium mt-1">{{ $showtime->room }}</dd>
        </div>
        <div>
          <dt class="text-gray-400">Ticket Price</dt>
          <dd class="text-white font-medium mt-1">€{{ number_format($showtime->ticket_price, 2) }}</dd>
        </div>
        <div>
          <dt class="text-gray-400">Ends At</dt>
          <dd class="text-white font-medium mt-1">
            {{ $showtime->starts_at->addMinutes($showtime->movie->duration)->format('H:i') }}
          </dd>
        </div>
        <div>
          <dt class="text-gray-400">Status</dt>
          <dd class="mt-1">
            @if($showtime->starts_at->isPast())
              <span class="px-2 py-1 bg-gray-700 text-gray-400 rounded text-xs">Ended</span>
            @elseif($showtime->starts_at->diffInMinutes(now()) <= 30)
              <span class="px-2 py-1 bg-yellow-500/20 text-yellow-400 rounded text-xs">Starting Soon</span>
            @else
              <span class="px-2 py-1 bg-green-500/20 text-green-400 rounded text-xs">Upcoming</span>
            @endif
          </dd>
        </div>
      </dl>
    </div>

    {{-- Tickets --}}
    <div class="bg-gray-800 rounded-xl p-5">
      <h2 class="text-xs uppercase tracking-widest text-gray-400 mb-4">Tickets</h2>
      <dl class="grid grid-cols-3 gap-4 text-sm">
        <div>
          <dt class="text-gray-400">Total Sold</dt>
          <dd class="text-white font-bold text-2xl mt-1">{{ $showtime->tickets()->count() }}</dd>
        </div>
        <div>
          <dt class="text-gray-400">Revenue</dt>
          <dd class="text-white font-bold text-2xl mt-1">
            €{{ number_format($showtime->tickets->where('status', '!=', \App\Enums\TicketStatus::Cancelled)->count() * $showtime->ticket_price, 2) }}
          </dd>
        </div>
      </dl>
    </div>

    {{-- Tickets List --}}
    <div class="bg-gray-800 rounded-xl p-5">
      <h2 class="text-xs uppercase tracking-widest text-gray-400 mb-4">Ticket List</h2>

      @if($showtime->tickets->isEmpty())
          <p class="text-gray-500 text-sm">No tickets sold yet.</p>
      @else
        <x-data-grid>
          <x-slot:head>
            <x-data-grid.header hidden="md">Seat</x-data-grid.header>
            <x-data-grid.header hidden="md">Customer</x-data-grid.header>
            <x-data-grid.header hidden="lg">Booked At</x-data-grid.header>
            <x-data-grid.header hidden="lg" class="text-right">Action</x-data-grid.header>
          </x-slot:head>

          @foreach ($showtime->tickets as $ticket)
            <x-data-grid-row>
              <x-data-grid.data class="py-3 font-semibold text-white">#{{ $ticket->seat }}</x-data-grid.data>
              <x-data-grid.data class="py-3">{{ $ticket->user->name ?? '—' }}</x-data-grid.data>
              <x-data-grid.data class="py-3 text-gray-400">{{ $ticket->created_at->format('d M Y, H:i') }}</x-data-grid.data>
              <x-data-grid.data class="py-3 text-right">
                @if ($ticket->status === \App\Enums\TicketStatus::Cancelled)
                  <p class="text-red-500">Ticket Cancelled</p>
                @elseif ($ticket->status === \App\Enums\TicketStatus::Confirmed)
                  <p class="text-green-500">Ticket Confirmed</p>
                @elseif ($ticket->status === \App\Enums\TicketStatus::Pending)
                  <form action="{{ route('tickets.destroy', $ticket) }}" method="POST"
                    onsubmit="return confirm('Cancel ticket for seat #{{ $ticket->seat }}?')">
                    @csrf @method('DELETE')
                    <x-button variant="danger" type="submit">Cancel</x-button>
                  </form>
                @endif
              </x-data-grid.data>
            </x-data-grid-row>
          @endforeach
        </x-data-grid>
      @endif
    </div>
  </div>
</div>
@endsection