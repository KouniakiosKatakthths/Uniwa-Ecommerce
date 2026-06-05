@extends("layouts.cinema")
@section("content")
<div class="sm:px-6 max-w-7xl mx-auto lg:px-8 py-5 flex flex-col gap-6">

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-100">Movies</h1>
    <x-button :href="route('movies.create')">Add movie</x-button>
  </div>

  {{-- Table --}}
  <div class="rounded-xl border border-white/10 overflow-hidden">
    <table class="w-full text-sm text-left">
      <thead class="bg-white/5 text-gray-500 uppercase tracking-widest text-xs">
        <tr>
          <th class="px-4 py-3">Movie</th>
          <th class="px-4 py-3 hidden md:table-cell">Genre</th>
          <th class="px-4 py-3 hidden md:table-cell">Rating</th>
          <th class="px-4 py-3 hidden lg:table-cell">Duration</th>
          <th class="px-4 py-3 hidden lg:table-cell">Release</th>
          <th class="px-4 py-3 hidden sm:table-cell">Showtimes</th>
          <th class="px-4 py-3 hidden sm:table-cell">Featured</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-white/5">
        @forelse($movies as $movie)
          <tr class="hover:bg-white/5 transition">
            {{-- Poster + title --}}
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <img src="{{ $movie->poster_url }}"
                     alt="{{ $movie->title }}"
                     class="w-10 aspect-2/3 object-cover rounded shrink-0">
                <div>
                  <p class="text-gray-200 font-medium">{{ $movie->title }}</p>
                  <p class="text-gray-500 text-xs truncate max-w-48">{{ $movie->director }}</p>
                </div>
              </div>
            </td>
            {{-- Genre --}}
            <td class="px-4 py-3 hidden md:table-cell text-gray-400">
              {{ $movie->genre->value }}
            </td>
            {{-- Rating --}}
            <td class="px-4 py-3 hidden md:table-cell">
              <span class="px-2 py-0.5 rounded border border-white/20 text-gray-300 text-xs font-mono">
                {{ $movie->rating->value }}
              </span>
            </td>
            {{-- Duration --}}
            <td class="px-4 py-3 hidden lg:table-cell text-gray-400">
              {{ $movie->getDurationFormatted() }}
            </td>
            {{-- Release date --}}
            <td class="px-4 py-3 hidden lg:table-cell text-gray-400">
              {{ $movie->release_date->format('d M Y') }}
            </td>
            {{-- Showtimes count --}}
            <td class="px-4 py-3 hidden sm:table-cell text-gray-400">
              {{ $movie->showtimes_count }}
            </td>
            {{-- Featured badge --}}
            <td class="px-4 py-3 hidden sm:table-cell">
              @if($movie->featured)
                <span class="px-2 py-0.5 rounded-full bg-accent/20 text-accent text-xs font-medium">Featured</span>
              @else
                <span class="text-gray-600 text-xs">—</span>
              @endif
            </td>
            {{-- Actions --}}
            <td class="px-4 py-3">
              <div class="flex gap-2 justify-end">
                <x-button variant="ghost" :href="route('movies.show', $movie)">View</x-button>
                <x-button :href="route('movies.edit', $movie)">Edit</x-button>
                @if(auth()->user()->isAdmin())
                  <form method="POST" action="{{ route('movies.destroy', $movie) }}"
                        onsubmit="return confirm('Delete {{ addslashes($movie->title) }}?')">
                    @csrf
                    @method('DELETE')
                    {{-- class="px-4 py-2 text-xs font-semibold uppercase tracking-widest rounded-lg border border-red-500/30 text-red-400 hover:bg-red-500/10 transition" --}}
                    <x-button type="submit" variant="danger">
                      Delete
                    </x-button>
                  </form>
                @endif
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="px-4 py-10 text-center text-gray-500">No movies yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div>
    {{ $movies->links() }}
  </div>

</div>
@endsection