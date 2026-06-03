@extends("layouts.cinema")

@section("content")
{{-- Header --}}
<div class="flex gap-6 mb-10 items-center">
  <img class="w-24 rounded-lg object-cover aspect-2/3" src="{{ $movie->poster_url }}">
  <div>
    <h1 class="text-gray-200 text-4xl font-bold">{{ $movie->title }}</h1>
    <p class="text-gray-400 mt-1">
      {{ $showtime->starts_at->format('l, M j · H:i') }} &bull; {{ $showtime->room }}
    </p>
  </div>
</div>
@endsection