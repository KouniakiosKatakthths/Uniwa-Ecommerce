@extends("layouts.cinema")

@section("content")
<div class="sm:px-6 max-w-7xl mx-auto lg:px-8 py-5">
  <x-flash-messages class="mb-6"></x-flash-messages>  

  {{-- Header --}}
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-gray-200 text-3xl font-bold">Movies</h1>
    <x-button :href="route('movies.create')">Add movie</x-button>
  </div>

  {{-- Movie datagrid --}}
  <x-data-grid :pagination="$movies->links()">

    <x-slot:head>
      <x-data-grid.header>Movie</x-data-grid.header>
      <x-data-grid.header hidden="md">Genre</x-data-grid.header>
      <x-data-grid.header hidden="md">Rating</x-data-grid.header>
      <x-data-grid.header hidden="lg">Duration</x-data-grid.header>
      <x-data-grid.header hidden="lg">Release</x-data-grid.header>
      <x-data-grid.header hidden="sm">Showtimes</x-data-grid.header>
      <x-data-grid.header hidden="sm">Featured</x-data-grid.header>
      <x-data-grid.header></x-data-grid.header>
    </x-slot:head>

    @forelse($movies as $movie)
      <x-data-grid-row>
        <x-data-grid.data>
          <div class="flex items-center gap-3">
            <img src="{{ $movie->getMoviePoster() }}" class="w-10 aspect-2/3 object-cover rounded shrink-0">
            <div>
              <p class="text-gray-200 font-medium">{{ $movie->title }}</p>
              <p class="text-gray-500 text-xs truncate max-w-48">{{ $movie->director }}</p>
            </div>
          </div>
        </x-data-grid.data>
        <x-data-grid.data hidden="md">{{ $movie->genre->value }}</x-data-grid.data>
        <x-data-grid.data hidden="md">
          <span class="px-2 py-0.5 rounded border border-white/20 text-gray-300 text-xs font-mono">
            {{ $movie->rating->value }}
          </span>
        </x-data-grid.data>
        <x-data-grid.data hidden="lg">{{ $movie->getDurationFormatted() }}</x-data-grid.data>
        <x-data-grid.data hidden="lg">{{ $movie->release_date->format('d M Y') }}</x-data-grid.data>
        <x-data-grid.data hidden="sm">{{ $movie->showtimes_count }}</x-data-grid.data>
        <x-data-grid.data hidden="sm">
          @if($movie->featured)
            <span class="px-2 py-0.5 rounded-full bg-accent/20 text-accent text-xs font-medium">Featured</span>
          @else
            <span class="text-gray-600 text-xs">—</span>
          @endif
        </x-data-grid.data>
        <x-data-grid.data class="text-right">
          <div class="flex gap-2 justify-end">
            <x-button variant="ghost" :href="route('movies.show', $movie)">View</x-button>
            <x-button :href="route('movies.edit', $movie)">Edit</x-button>
            @if(auth()->user()->isAdmin())
              <form method="POST" action="{{ route('movies.destroy', $movie) }}" onsubmit="return confirm('Delete {{ addslashes($movie->title) }}?')">
                @csrf @method('DELETE')
                <x-button type="submit" variant="danger">Delete</x-button>
              </form>
            @endif
          </div>
        </x-data-grid.data>
      </x-data-grid-row>
    @empty
      <tr>
        <td colspan="8" class="px-4 py-10 text-center text-gray-500">No movies yet.</td>
      </tr>
    @endforelse
  </x-table>
</div>
@endsection