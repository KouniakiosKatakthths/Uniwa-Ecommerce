@props([ 'show' => null ])

@php
  $isEditing = $show !== null;
  $action = $isEditing 
    ? route('showtimes.update', $show->id) 
    : route('showtimes.store');
@endphp

<form method="POST" class="flex flex-col gap-5">
  @csrf
  @if($isEditing)
    @method('PUT')
  @endif

  {{-- Movie field --}}
  <div>
  </div>

  {{-- Room field --}}
  <div class="flex flex-col gap-1">
    <x-input-label>Showtime room name</x-input-label>
    <x-text-input name="room" value="{{ old('room', $show?->room) }}" placeholder="Room name"></x-text-input>
  </div>


  {{-- Starts at datetime field --}}
  <div class="flex flex-col gap-1">
    <x-input-label>Show starts at</x-input-label>
    <x-text-input name="starts_at" type="datetime-local" value="{{ old('starts_at', $show?->starts_at) }}"></x-text-input>
  </div>

  {{-- Ticket price field --}}
  <div class="flex flex-col gap-1">
    <x-input-label>Ticket price</x-input-label>
    <x-text-input name="ticket_price" type="number" step="0.01" value="{{ old('ticket_price', $show?->ticket_price) }}" placeholder="Ticket price in euros"></x-text-input>
  </div>

  {{-- Submit --}}
  <div class="flex gap-3 justify-end">
    <x-button type="submit">{{ $isEditing ? 'Update showtime' : 'Create showtime' }}</x-button>
  </div>
</form>