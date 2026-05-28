@props(['href'])
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:text-gray-300 dark:hover:text-gray-500']) }}>
  {{ $slot }}
</a>