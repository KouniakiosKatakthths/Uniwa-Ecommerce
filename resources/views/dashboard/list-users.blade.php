@extends("layouts.cinema")
@section("content")
<div class="sm:px-6 max-w-7xl mx-auto lg:px-8 py-5">
<x-flash-messages class="mb-6"></x-flash-messages>

  {{-- Header --}}
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-gray-200 text-3xl font-bold">Users</h1>
  </div>

  <x-data-grid :pagination="$users->links()">
    <x-slot:head>
      <x-data-grid.header>User</x-data-grid.header>
      <x-data-grid.header>Role</x-data-grid.header>
      <x-data-grid.header hidden="md">Verified</x-data-grid.header>
      <x-data-grid.header hidden="sm">Tickets</x-data-grid.header>
      <x-data-grid.header hidden="sm">Joined</x-data-grid.header>
      <x-data-grid.header></x-data-grid.header>
    </x-slot:head>

    @forelse($users as $user)
      <x-data-grid-row>
        {{-- Name + email --}}
        <x-data-grid.data>
          <div class="flex flex-col">
            <p class="text-gray-200 font-medium">{{ $user->name }}</p>
            <p class="text-gray-500 text-xs">{{ $user->email }}</p>
          </div>
        </x-data-grid.data>

        {{-- Role --}}
        <x-data-grid.data hidden="md"> 
          <span class="px-2 py-0.5 rounded-full text-xs font-medium 
            {{ $user->isAdmin() ? 'bg-red-500/20 text-red-400' : '' }} 
            {{ $user->isClerk() && !$user->isAdmin() ? 'bg-blue-500/20 text-blue-400' : '' }} 
            {{ $user->isUser() ? 'bg-gray-500/20 text-gray-400' : '' }}"> 
              {{ $user->role->value }} 
            </span> 
        </x-data-grid.data>

        {{-- Verified --}}
        <x-data-grid.data hidden="md">
          @if($user->email_verified_at)
            <span class="text-green-400 text-xs">✓ Verified</span>
          @else
            <span class="text-gray-600 text-xs">—</span>
          @endif
        </x-data-grid.data>

        {{-- Tickets --}}
        <x-data-grid.data hidden="sm">{{ $user->tickets_count }}</x-data-grid.data>

        {{-- Joined --}}
        <x-data-grid.data hidden="sm">{{ $user->created_at->format('d M Y') }}</x-data-grid.data>

        {{-- Actions --}}
        <x-data-grid.data class="text-right">
          <div class="flex gap-2 justify-end">
            <x-button variant="ghost" href="{{ route('profile.edit-user', $user) }}">Edit</x-button>
            @if (auth()->user()->id !== $user->id)
              <form method="POST" action="{{ route('profile.destroy', $user) }}"
                onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                @csrf @method('DELETE')
                <x-button type="submit" variant="danger">Delete</x-button>
              </form>
            @endif
          </div>
        </x-data-grid.data>

      </x-data-grid-row>
    @empty
      <tr>
        <td colspan="6" class="px-4 py-10 text-center text-gray-500">No users found.</td>
      </tr>
    @endforelse
  </x-data-grid>
</div>
@endsection