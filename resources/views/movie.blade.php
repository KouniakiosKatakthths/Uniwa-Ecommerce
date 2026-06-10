@extends("layouts.cinema")

@php
  $trailerEmbedUrl = null;

  if ($movie->trailer_url) {
    $trailerEmbedUrl = str_replace('watch?v=', 'embed/', $movie->trailer_url);
  }
@endphp

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
          <p class="flex font-normal text-gray-400">
            {{ $movie->getDurationFormatted() }} 
            {{ $movie->rating }}
            @if ($movie->tmdb_rating !== null && $movie->tmdb_vote_count !== null)
              <span class="ml-1">&#8226; </span>
              <span class="text-accent ml-1">★ {{ $movie->tmdb_rating }}</span>
              <span class="text-gray-600 ml-1">({{ $movie->tmdb_vote_count }})</span>
            @endif
          </p>
        </div>
        
        {{-- Movie descritpion --}}
        <div class="flex flex-col gap-2">
          <h3 class="text-gray-300 text-2xl font-bold">Description</h3>
          <p class="text-gray-300">{{ $movie->description }}</p>
        </div>

        {{-- Movie contributors --}}
        <div class="flex flex-col gap-3">
          <div class="flex gap-4 pb-2 border-gray-600 border-b">
            <span class="text-gray-400 w-24 shrink-0 border-r border-gray-600 font-bold">Director:</span>
            <span class="text-gray-300">{{ $movie->director }}</span>
          </div>
          <div class="flex gap-4 pb-2 border-gray-600 border-b">
            <span class="text-gray-400 w-24 shrink-0 border-r border-gray-600 font-bold">Actors:</span>
            <span class="text-gray-300">{{ implode(', ', $movie->actors) }}</span>
          </div>
        </div>
        
        {{-- Buttons --}}
        <div x-data="{ showtimes: @js($showtimes), }" class="flex gap-4">
          @if($trailerEmbedUrl)
            <x-button variant="ghost" @click.prevent="$dispatch('open-modal', 'movie-trailer-container')">Watch trailer</x-button>
          @endif
          <x-button x-show="showtimes.length !== 0" @click="document.getElementById('tickets').scrollIntoView({ behavior: 'smooth' })">
            Get tickets
          </x-button>
        </div>
      </div>
    </div>
  </section>

  {{-- Showtimes --}}
  <section 
    id="tickets"
    class="max-w-7xl w-full min-h-100 mx-auto px-6 lg:px-8 py-10"
    x-data="{
      days: @js(
        collect(range(0, 5))->map(fn($i) => [
          'label'     => now("Europe/Athens")->addDays($i)->format('D'),     // Mon
          'sublabel'  => now("Europe/Athens")->addDays($i)->format('M j'),   // May 29
          'key'       => now("Europe/Athens")->addDays($i)->toDateString(),  // 2026-05-29
        ])
      ),
      showtimes: @js($showtimes),
      selected: Object.keys(@js($showtimes))[0] ?? '{{ now("Europe/Athens")->toDateString() }}',
      get current() { return this.showtimes[this.selected] ?? [] }
    }">
    <h2 class="text-gray-200 text-2xl font-bold mb-6">Showtimes</h2>

    {{-- No showtimes message --}}
    <div x-show="showtimes.length === 0" class="text-gray-400 text-lg font-bold">
      <p>No showtimes are sceduled for this movie yet.</p>
    </div>

    {{-- Showtime selectors --}}
    <div x-show="showtimes.length !== 0">
      {{-- Day pills --}}
      <div class="flex gap-3 mb-8 flex-wrap">
        <template x-for="day in days" :key="day.key">
          <button
            @click="selected = day.key"
            :class="selected === day.key
              ? 'bg-accent text-gray-900 shadow-[0_0_16px_rgba(245,158,11,0.5)]'
              : 'bg-gray-800 text-gray-300 hover:bg-gray-700'"
            class="flex flex-col items-center px-5 py-3 rounded-xl transition-all duration-200 min-w-18 cursor-pointer">
            <span class="text-xs font-semibold uppercase tracking-widest" x-text="day.label"></span>
            <span class="text-base font-bold mt-0.5" x-text="day.sublabel"></span>
          </button>
        </template>
      </div>

      {{-- Showtime slots --}}
      <div class="flex flex-col gap-3">
        <template x-for="showtime in current" :key="showtime.id">
          <x-card variant="accent">
            <div class="flex items-center min-w-60 w-[30%] justify-between">
              <span class="text-gray-200 text-3xl"
                    x-text="new Date(showtime.starts_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})">
              </span>
              <span class="text-gray-500 uppercase" x-text="showtime.room"></span>
            </div>
            <x-button x-bind:href="`/showtimes/${showtime.id}/tickets/create`">Get tickets</x-button>
          </x-card>
        </template>
      </div>

      {{-- Empty day --}}
      <div x-show="current.length === 0" x-transition class="text-gray-400 text-lg font-bold">
        No showtimes available for this day.
      </div>
    </div>
  </section>
</div>

{{-- Trailer modal --}}
<x-modal name="movie-trailer-container" maxWidth="5xl" x-data focusable>
  <div class="flex flex-col gap-5 p-5 text-gray-200">
    <div class="flex justify-between">
      <div class="flex gap-1">
        <h1 class="text-gray-200 text-2xl font-bold ">Trailer -</h1>
        <h1 class="text-gray-200 text-2xl font-bold first-letter:uppercase"> {{ $movie->title }}</h1>
      </div>
      <x-primary-button x-on:click="$dispatch('close-modal', 'movie-trailer-container')">X</x-primary-button>
    </div>
    <div
      x-data="{ trailerUrl: '{{ $trailerEmbedUrl }}' }"
      x-on:modal-closed.window="
        if ($event.detail === 'movie-trailer-container') {
          $refs.trailer.src = '';
        }
      "
      x-on:modal-opened.window="
        if ($event.detail === 'movie-trailer-container') {
          $refs.trailer.src = $refs.trailer.src = trailerUrl;
        }
      ">
      <iframe
        x-ref="trailer"
        class="w-full"
        height="550"
        title="YouTube video player"
        frameborder="0"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        referrerpolicy="strict-origin-when-cross-origin"
        allowfullscreen>
      </iframe>
    </div>
  </div>
</x-modal>
@endsection