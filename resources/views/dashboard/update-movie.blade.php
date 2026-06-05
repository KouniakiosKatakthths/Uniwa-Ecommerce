@extends("layouts.cinema")

@section("content")
<x-card class="sm:max-w-2xl mx-auto my-10">
  <h1 class="text-gray-800 dark:text-gray-200 text-2xl mb-5">Edit Movie</h1>
  <x-movie-form :movie="$movie"></x-movie-form>
</x-card>
@endsection