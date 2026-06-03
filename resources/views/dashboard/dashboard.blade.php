@extends("layouts.cinema")

@section("content")
<div class="sm:px-6 max-w-7xl mx-auto lg:px-8 py-5">
  <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Dashboard') }}
  </h2>

  {{-- Management tools --}}
  <x-card>
    <div class="text-gray-900 dark:text-gray-300">
      <h2 class="text-lg font-semibold">Management tools</h2>
      
      {{-- Movies management --}}
      <div class="flex items-center gap-4 mt-4 sm:flex-row flex-col">
        <h2 class="text-lg">Movies</h2>
        <x-button class="w-full sm:w-auto" :href="route('movies.index')">List all</x-button>
        <x-button class="w-full sm:w-auto" :href="route('movies.create')">Create movie</x-button>
      </div>

      {{-- Showtimes management --}}
      <div class="flex items-center gap-4 mt-4 sm:flex-row flex-col">
        <h2 class="text-lg">Showtimes</h2>
        <x-button class="w-full sm:w-auto">List all</x-button>
        <x-button class="w-full sm:w-auto">Create showtime</x-button>
      </div>
    </div>
  </x-card>

  <x-card>
    <div class="text-gray-900 dark:text-gray-300">
      {{ __("You're logged in!") }}
    </div>
  </x-card>
</div>
@endsection
