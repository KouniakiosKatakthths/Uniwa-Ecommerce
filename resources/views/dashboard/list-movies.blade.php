@extends("layouts.cinema")

@section("content")
<div class="sm:px-6 max-w-7xl mx-auto lg:px-8 py-5">
  <x-movies-list :movies="$movies" :route="route('movies.index')" :showDays="false" />
</div>
@endsection