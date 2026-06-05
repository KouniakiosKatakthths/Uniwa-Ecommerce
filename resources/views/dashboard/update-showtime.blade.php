@extends("layouts.cinema")
@section("content")
<x-card class="sm:max-w-2xl mx-auto my-10">
  <h1 class="text-gray-800 dark:text-gray-200 text-2xl mb-5">Edit showtime</h1>
  <x-showtime-form :show="$showtime"></x-showtime-form>
</x-card>
@endsection