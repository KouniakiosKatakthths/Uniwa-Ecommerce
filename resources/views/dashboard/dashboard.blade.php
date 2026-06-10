@extends("layouts.cinema")

@section("content")
<div class="sm:px-6 max-w-7xl mx-auto lg:px-8 py-5">
  <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Dashboard') }}
  </h2>

  {{-- Management tools --}}
  @if (auth()->user()->isClerk())
    <x-card class="mt-6">
      <div class="flex flex-col gap-6">
        <h2 class="text-lg font-semibold text-gray-200 border-b border-white/10 pb-3">Management Tools</h2>

        {{-- Movies --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
          <span class="text-sm uppercase tracking-widest text-gray-500 w-28 shrink-0">Movies</span>
          <div class="flex gap-3 flex-wrap">
            <x-button :href="route('movies.index')" variant="ghost">List all</x-button>
            <x-button :href="route('movies.create')">Create movie</x-button>
          </div>
        </div>

        {{-- Showtimes --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
          <span class="text-sm uppercase tracking-widest text-gray-500 w-28 shrink-0">Showtimes</span>
          <div class="flex gap-3 flex-wrap">
            <x-button :href="route('showtimes.index')" variant="ghost">List all</x-button>
            <x-button :href="route('showtimes.create')">Create showtime</x-button>
          </div>
        </div>

        {{-- Divider --}}
        <div class="border-t border-white/10"></div>

        {{-- Ticket validator --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
          <span class="text-sm uppercase tracking-widest text-gray-500 w-28 shrink-0">Tickets</span>
          <x-button :href="route('tickets.validate')">
            <svg class="w-4 h-4 mr-2 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 14v1m8-8h-1M5 12H4m15.07-6.07-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Ticket validator
          </x-button>
        </div>
      </div>
    </x-card>
  @endif

  {{-- My Tickets --}}
  <x-card class="mt-6">
    <div class="flex flex-col gap-4">
      <h2 class="text-lg font-semibold text-gray-200">My Tickets</h2>

      @forelse($tickets as $ticket)
        <x-card variant="accent" tag="a" href="{{ route('tickets.show', $ticket) }}" class="flex gap-4 items-center">
          {{-- Poster --}}
          <img src="{{ $ticket->showtime->movie->getMoviePoster() }}"
              alt="{{ $ticket->showtime->movie->title }}"
              class="w-12 aspect-2/3 object-cover rounded shrink-0">

          {{-- Info --}}
          <div class="flex flex-col gap-0.5 flex-1 min-w-0">
            <p class="text-gray-200 font-medium truncate">{{ $ticket->showtime->movie->title }}</p>
            <p class="text-gray-500 text-sm">{{ $ticket->showtime->starts_at->format('d M Y, H:i') }}</p>
            <p class="text-gray-500 text-sm">Room: {{ $ticket->showtime->room }} &bull; Seat {{ $ticket->seat }}</p>
          </div>

          {{-- Price --}}
          <p class="text-accent font-semibold shrink-0">€{{ number_format($ticket->price, 2) }}</p>

          {{-- Status --}}
          <span class="text-xs font-semibold px-2 py-1 rounded-full shrink-0
            {{ $ticket->status === \App\Enums\TicketStatus::Confirmed ? 'bg-green-500/20 text-green-400' : '' }}
            {{ $ticket->status === \App\Enums\TicketStatus::Pending   ? 'bg-yellow-500/20 text-yellow-400' : '' }}
            {{ $ticket->status === \App\Enums\TicketStatus::Cancelled ? 'bg-red-500/20 text-red-400' : '' }}">
            {{ $ticket->status->name }}
          </span>

          {{-- Arrow --}}
          <svg class="w-4 h-4 text-gray-600 group-hover:text-accent transition shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>

        </x-card> 
      @empty
        <p class="text-gray-500 text-sm py-4 text-center">You have no tickets yet.</p>
      @endforelse

      {{-- Pagination --}}
      @if($tickets->hasPages())
          <div class="mt-4">
              {{ $tickets->links() }}
          </div>
      @endif
    </div>
  </x-card>
</div>
@endsection
