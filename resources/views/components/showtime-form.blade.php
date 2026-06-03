@props([ 'showtime' => null ])

@php
  $isEditing = $showtime !== null;
  $action = $isEditing 
    ? route('showtime.update', $showtime->id) 
    : route('showtime.store');
@endphp

<form method="POST">
  @csrf
  @if($isEditing)
    @method('PUT')
  @endif

  {{-- Movie field --}}

  {{-- Room field --}}

  {{-- Starts at field --}}
  <div>
    <x-input-label>asdsd</x-input-label>
  </div>

  {{-- Ticket price field --}}
  <div>

  </div>
</form>