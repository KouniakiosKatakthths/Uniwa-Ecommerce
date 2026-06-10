@extends("layouts.cinema")

@section("content")
<div class="sm:px-6 max-w-3xl mx-auto lg:px-8 py-5">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-gray-200 text-3xl font-bold">Edit User</h1>

    <a href="{{ route('profile.index') }}" class="text-gray-400 hover:text-gray-200 text-sm">
      Back to users
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 rounded-md bg-green-500/10 border border-green-500/20 px-4 py-3 text-green-400 text-sm">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="mb-4 rounded-md bg-red-500/10 border border-red-500/20 px-4 py-3 text-red-400 text-sm">
      {{ session('error') }}
    </div>
  @endif

  <x-card>
    <form method="POST" action="{{ route('profile.update-user', $user) }}" class="flex flex-col gap-3">
      @csrf
      @method('PATCH')

      <div class="w-full flex flex-col gap-1">
        <x-input-label>Name</x-input-label>
        <x-text-input class="w-full" name="name" value="{{ old('name', $user->name) }}" required></x-text-input>
        @error('name')
          <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
      </div>

      <div class="w-full flex flex-col gap-1">
        <x-input-label>Email</x-input-label>
        <x-text-input class="w-full" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required></x-text-input>
        @error('email')
          <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
      </div>

      {{-- Role --}}
      <div class="flex flex-col gap-1 items-start">
        <x-input-label>Role</x-input-label>

        @if(auth()->id() !== $user->id)
          <x-selector class="w-full min-w-32" id="role" name="role">
            @foreach(\App\Enums\UserRole::cases() as $role)
              <option value="{{ $role->value }}" @selected($user->role === $role)>
                {{ ucfirst($role->value) }}
              </option>
            @endforeach
          </x-selector>
        @else
          <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-500/20 text-red-400">
            {{ $user->role->value }}
          </span>
        @endif

        @error('role')
          <p class="mt-1 inline-block text-sm text-red-400">
            {{ $message }}
          </p>
        @enderror
      </div>

      <div class="flex items-center gap-2">
        <input
          id="email_verified"
          name="email_verified"
          type="checkbox"
          value="1"
          @checked(old('email_verified', $user->email_verified_at !== null))
          class="rounded bg-gray-950 border-gray-700 text-red-500 focus:ring-red-500"
        >

        <label for="email_verified" class="text-sm text-gray-300">
          Email verified
        </label>

        @error('email_verified')
          <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
        @enderror
      </div>

      <div class="flex justify-end gap-3 pt-4">
        <a
          href="{{ route('profile.index') }}"
          class="px-4 py-2 rounded-md bg-gray-800 text-gray-300 hover:bg-gray-700"
        >
          Cancel
        </a>

        <x-button type="submit">
          Save Changes
        </x-button>
      </div>
    </form>
  </x-card>
</div>
@endsection