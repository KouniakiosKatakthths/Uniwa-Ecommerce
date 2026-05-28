@extends('layouts.cinema')

@section('content')
<div class="flex flex-col">
  {{-- Featured movie hero --}}
  <section class="relative">
    <div class="absolute inset-0 bg-(image:--gradient-hero)"></div>
    <div class="absolute inset-0 bg-(image:--gradient-hero-overlay)"></div>
    <div class="relative z-10 p-10">
      <div class="flex gap-5">
        {{-- Movie image --}}
        <img class="w-3xl h-85" src="{{ $featured->poster_url }}">

        {{-- Movie detailes --}}
        <div class="flex flex-col gap-5">
          <h1 class="text-gray-200 text-6xl">{{ $featured->title }}</h1>
          <p class="w-4/5 text-gray-500">{{ $featured->description }}{{ $featured->description }}{{ $featured->description }}</p>

          {{-- Control buttons --}}
          <div class="mt-auto flex gap-4">
            <x-button variant="ghost" class="flex items-center gap-2">
              <span>More</span>
              <svg width="15" height="15" viewBox="0 2 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M8 19L19 12L8 5V19Z" fill="currentColor"></path>
              </svg>
            </x-button>
            <x-button>Get tickets</x-button>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- Playing now --}}
  <section class="p-10">
    <h1 class="text-gray-300 text-center text-3xl font-bold">Playing now</h1>

    @foreach ($nowPlaying as $movie)
      <x-movie-card :movie="$movie"></x-movie-card>
    @endforeach
  </section>
</div>   
@endsection