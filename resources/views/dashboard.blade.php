@extends("layouts.cinema")

@section("content")
<div class="sm:px-6 lg:px-8">
  <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Dashboard') }}
  </h2>

  <x-card>
    <div class="text-gray-900 dark:text-gray-300">
      {{ __("You're logged in!") }}
    </div>
  </x-card>
</div>
@endsection
