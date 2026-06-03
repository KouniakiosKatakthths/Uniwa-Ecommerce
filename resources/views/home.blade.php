@extends('layouts.cinema')

@section('content')
<div class="flex flex-col">
  {{-- Featured movie hero --}}
  <section class="relative">
    <div class="absolute inset-0 bg-(image:--gradient-hero)"></div>
    <div class="absolute inset-0 bg-(image:--gradient-hero-overlay)"></div>
    <div class="relative z-10 py-10 flex gap-5 max-w-7xl w-full mx-auto sm:px-6 lg:px-8">
      <img class="w-70 max-w-[30%] aspect-2/3 shrink-0 object-cover shadow-[0_10px_40px_rgba(200,169,110,0.3)] rounded-lg" src="{{ $featured->poster_url }}">

      {{-- Movie detailes --}}
      <div class="flex flex-col gap-5">
        <h1 class="text-gray-200 text-6xl first-letter:uppercase">{{ $featured->title }}</h1>
        <div class="flex flex-col gap-2">
          <p class="w-4/5 text-gray-400 first-letter:uppercase">{{ $featured->description }}</p>
          <div class="flex items-center">
            <p class="text-sm font-normal text-gray-600">
              {{ $featured->rating }} &#8226; 
              {{ $featured->getDurationFormatted() }} &#8226; 
              <span class="bottom-2 right-2 text-sm text-accent font-medium">★ 4.8</span>
            </p>
          </div>
        </div>

        {{-- Control buttons --}}
        <div class="mt-auto flex gap-4">
          <x-button variant="ghost" class="flex items-center gap-2" href="{{ route('movies.show', $featured->id) }}">
            <span>More</span>
            <svg width="15" height="15" viewBox="0 2 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M8 19L19 12L8 5V19Z" fill="currentColor"></path>
            </svg>
          </x-button>
          <x-button>Get tickets</x-button>
        </div>
      </div>
    </div>
  </section>

  {{-- Playing now --}}
  <section class="max-w-7xl w-full mx-auto py-10 sm:px-6 lg:px-8">
    <div class="flex justify-between">
      <h1 class="text-gray-300 text-3xl font-bold">Playing now</h1>
      <x-button variant="ghost" class="flex items-center gap-2" href="{{ route('movies.now') }}">
        <span>Show all</span>
        <svg width="15" height="15" viewBox="0 2 19 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M8 19L19 12L8 5V19Z" fill="currentColor"></path>
      </svg>
      </x-button>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      @foreach ($nowPlaying as $movie)
        <x-movie-card :movie="$movie"></x-movie-card>
      @endforeach
    </div>
  </section>
</div>   
@endsection