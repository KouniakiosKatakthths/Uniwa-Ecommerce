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
          <x-button variant="ghost">Watch trailer</x-button>
          <x-button>Get tickets</x-button>
        </div>
      </div>
    </div>
  </section>

  {{-- Showtimes --}}
  <section>
    
  </section>
</div>
@endsection