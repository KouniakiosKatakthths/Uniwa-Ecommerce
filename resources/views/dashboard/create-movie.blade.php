@extends("layouts.cinema")

@section("content")
<x-card class="sm:max-w-2xl mx-auto">
  <h1 class="text-gray-800 dark:text-gray-200 text-2xl mb-5">Create Movie</h1>
  <x-movie-form></x-movie-form>
</x-card>
@endsection