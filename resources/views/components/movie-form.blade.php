@props(['movie' => null])

@php
  $isEditing = $movie !== null;
  $action = $isEditing 
    ? route('movies.update', $movie->id) 
    : route('movies.store');
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="flex flex-col gap-5">
  @csrf
  @if($isEditing)
    @method('PUT')
  @endif

  <div class="flex flex-col gap-1">
    <x-input-label>TMDB id</x-input-label>
    <x-text-input name="tmdb_id" type="number" value="{{ old('tmdb_id', $movie?->tmdb_id) }}" placeholder="TMDB id"></x-text-input>
    <x-input-error :messages="$errors->get('tmdb_id')"></x-input-error>
  </div>

  {{-- Title --}}
  <div class="flex flex-col gap-1">
    <x-input-label>Title</x-input-label>
    <x-text-input name="title" value="{{ old('title', $movie?->title) }}" placeholder="Movie title"></x-text-input>
    <x-input-error :messages="$errors->get('title')"></x-input-error>
  </div>

  {{-- Description --}}
  <div class="flex flex-col gap-1">
    <x-input-label>Description</x-input-label>

    <x-text-input name="description" textarea="true" value="{{ old('description', $movie?->description) }}" placeholder="Movie description"></x-text-input>
    <x-input-error :messages="$errors->get('description')"></x-input-error>
  </div>

  {{-- Trailer URL --}}
  <div class="flex flex-col gap-1">
    <x-input-label>Trailer youtube URL</x-input-label>

    <x-text-input name="trailer_url" value="{{ old('trailer_url', $movie?->trailer_url) }}" placeholder="https://www.youtube.com/watch?v=..."></x-text-input>
    <x-input-error :messages="$errors->get('trailer_url')"></x-input-error>
  </div>

  {{-- Director --}}
  <div class="flex flex-col gap-1">
    <x-input-label>Director</x-input-label>
    <x-text-input name="director" value="{{ old('director', $movie?->director) }}" placeholder="Director name"></x-text-input>
    <x-input-error :messages="$errors->get('director')"></x-input-error>
  </div>

  {{-- Actors --}}
  <div class="flex flex-col gap-1">
    <x-input-label>Comma separated actor names</x-input-label>
    <x-text-input 
      textarea="true" 
      name="actors" 
      value="{{ old('actors', is_array($movie?->actors) ? implode(', ', $movie->actors) : $movie?->actors) }}" 
      placeholder="Actor1, Actor2, ...">
    </x-text-input>
    <x-input-error :messages="$errors->get('actors')"></x-input-error>
  </div>
  
  {{-- Genre & Release date --}}
  <div class="flex gap-4">
    {{-- Genre --}}
    <div class="flex flex-col gap-1 w-1/2">
      <x-input-label>Genre</x-input-label>
      <x-selector name="genre">
        @foreach(App\Enums\MovieGenre::cases() as $genre)
          <option value="{{ $genre }}" {{ old('genre', $movie?->genre) === $genre ? 'selected' : '' }}>
            {{ $genre }}
          </option>
        @endforeach
      </x-selector>
      <x-input-error :messages="$errors->get('genre')"></x-input-error>
    </div>

    {{-- Release date --}}
    <div class="flex flex-col gap-1 w-1/2">
      <x-input-label>Release date</x-input-label>
      <x-text-input name="release_date" type="date" value="{{ old('release_date', $movie?->release_date?->format('Y-m-d')) }}"
      ></x-text-input>
      <x-input-error :messages="$errors->get('release_date')"></x-input-error>
    </div>
  </div>

  {{-- Rating & Duration --}}
  <div class="flex gap-4">
    <div class="flex flex-col gap-1 w-1/2">
      <x-input-label>Rating</x-input-label>
      <x-selector name="rating">
        @foreach(App\Enums\MovieRating::cases() as $rating)
          <option value="{{ $rating }}" {{ old('rating', $movie?->rating) === $rating ? 'selected' : '' }}>
            {{ $rating }}
          </option>
        @endforeach
      </x-selector>
      <x-input-error :messages="$errors->get('rating')"></x-input-error>
    </div>

    <div class="flex flex-col gap-1 w-1/2">
      <x-input-label>Duration (minutes)</x-input-label>
      <x-text-input name="duration" type="number" value="{{ old('duration', $movie?->duration) }}" placeholder="120"></x-text-input>
      <x-input-error :messages="$errors->get('duration')"></x-input-error>
    </div>
  </div>

  {{-- Featured --}}
  <div class="flex items-center gap-2">
    <input 
      type="checkbox" 
      name="featured" 
      id="featured"
      value="1"
      {{ old('featured', $movie?->featured) ? 'checked' : '' }}
      class="rounded border-gray-600 bg-gray-800 text-accent focus:ring-accent"
    >
    <x-input-label for="featured">Featured movie</x-input-label>
  </div>

  {{-- Poster --}}
  <div class="flex flex-col gap-1">
    <x-input-label>Poster</x-input-label>
    @if($isEditing && $movie->poster_url)
      <img src="{{ $movie->getMoviePoster() }}" class="w-24 rounded-lg aspect-2/3 object-cover mb-2">
    @endif
    <input type="file" name="poster" accept="image/*"
      class="text-gray-400 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-accent file:text-gray-900 file:cursor-pointer">
    <x-input-error :messages="$errors->get('poster')"></x-input-error>
  </div>

  {{-- Submit --}}
  <div class="flex gap-3 justify-end">
    <x-button type="submit">
      {{ $isEditing ? 'Update Movie' : 'Create Movie' }}
    </x-button>
  </div>
</form>