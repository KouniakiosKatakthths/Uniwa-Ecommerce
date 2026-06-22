@extends("layouts.cinema")

@section("content")
<div class="flex flex-col max-w-7xl mx-auto sm:px-6 lg:px-8 py-10">
  <x-flash-messages class="mb-6"></x-flash-messages>

  <h2 class="font-semibold text-3xl text-gray-800 leading-tight dark:text-gray-300 ml-5">
    {{ __('Profile') }}
  </h2>

  <div class="w-full mx-auto space-y-6">
    <x-card class="w-full">
      <div class="max-w-xl">
        @include('profile.partials.update-profile-information-form')
      </div>
    </x-card>

    <x-card class="w-full">
      <div class="max-w-xl">
        @include('profile.partials.update-password-form')
      </div>
    </x-card>

    <x-card class="w-full">
      <div class="max-w-xl">
        @include('profile.partials.delete-user-form')
      </div>
    </x-card>
  </div>
</div>
@endsection

