@props([
  'name',
  'placeholder' => null,
])

<select
  name="{{ $name }}"
  {{ $attributes->merge([
      'class' => '
          border-gray-100
          dark:placeholder-gray-600 dark:bg-[#293447] dark:border-white/10 dark:text-gray-300 dark:border-solid dark:border dark:focus:border-white/20
          focus:border-indigo-500 focus:ring-indigo-500
          rounded-lg shadow-sm px-4 py-2 text-sm focus:outline-none
      '
  ]) }}>
  @if($placeholder)
      <option value="">{{ $placeholder }}</option>
  @endif

  {{ $slot }}
</select>