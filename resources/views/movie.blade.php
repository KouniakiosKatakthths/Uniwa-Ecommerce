@extends("layouts.cinema")

@section("content")
<div>
  <section class="relative">
    <div class="p-10 flex gap-5">
      {{-- Movie image --}}
      <img class="w-70 h-85 shadow-[0_10px_40px_rgba(200,169,110,0.3)] rounded-lg" src="{{ $movie->poster_url }}">

      {{-- Movie details --}}
      <div class="flex flex-col gap-8 w-3/4">
        <div class="flex flex-col gap-2">
          <h1 class="text-gray-200 text-6xl first-letter:uppercase">{{ $movie->title }}</h1>
          <p class="font-normal text-gray-400">
            {{ $movie->getDurationFormatted() }} &#8226; 
            {{ $movie->rating }}
          </p>
        </div>
        
        <div class="flex flex-col gap-2">
          <h3 class="text-gray-300 text-2xl font-bold">Description</h3>
          <p class="text-gray-300">{{ $movie->description }}</p>
        </div>
      </div>

    </div>
  </section>
</div>
@endsection