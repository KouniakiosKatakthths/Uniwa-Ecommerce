@extends("layouts.cinema")

@section("content")
<div>
  <section class="relative">
    <div class="relative z-10 py-10 flex gap-5 max-w-7xl w-full mx-auto sm:px-6 lg:px-8">
      {{-- Movie image --}}
      <img class="w-70 max-w-[30%] self-start aspect-2/3 shrink-0 object-cover shadow-[0_10px_40px_rgba(200,169,110,0.3)] rounded-lg duration-500 hover:scale-105" src="{{ $movie->poster_url }}">

      {{-- Movie details --}}
      <div class="flex flex-col gap-8 w-3/4">
        {{-- Movie title --}}
        <div class="flex flex-col gap-2">
          <h1 class="text-gray-200 text-6xl first-letter:uppercase">{{ $movie->title }}</h1>
          <p class="font-normal text-gray-400">
            {{ $movie->getDurationFormatted() }} &#8226; 
            {{ $movie->rating }}
          </p>
        </div>
        
        {{-- Movie descritpion --}}
        <div class="flex flex-col gap-2">
          <h3 class="text-gray-300 text-2xl font-bold">Description</h3>
          <p class="text-gray-300">{{ $movie->description }}</p>
        </div>

        {{-- Movie contributors --}}
        <div class="flex flex-col gap-3">
          <div class="flex gap-4 border-gray-600 border-b">
            <span class="text-gray-400 w-24 shrink-0 border-r border-gray-600 font-bold">Director:</span>
            <span class="text-gray-300">{{ $movie->director }}</span>
          </div>
          <div class="flex gap-4 border-gray-600 border-b">
            <span class="text-gray-400 w-24 shrink-0 border-r border-gray-600 font-bold">Actors:</span>
            <span class="text-gray-300">{{ implode(', ', $movie->actors) }}</span>
          </div>
        </div>
        
        {{-- Buttons --}}
        <div class="flex gap-4">
          <x-button x-data="" variant="ghost" x-on:click.prevent="$dispatch('open-modal', 'movie-trailer-container')">Watch trailer</x-button>
          <x-button>Get tickets</x-button>
        </div>
      </div>
    </div>
  </section>

  {{-- Showtimes --}}
  <section>
  </section>
</div>

<x-modal name="movie-trailer-container" x-data="" focusable>
  <div class="p-10 text-gray-200">
    <iframe width="560" height="315" src="https://www.youtube.com/embed/06gXGAHnRyE" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
  </div>
</x-modal>
@endsection