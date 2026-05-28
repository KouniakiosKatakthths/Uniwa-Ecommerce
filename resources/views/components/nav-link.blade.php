@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-accent text-sm uppercase font-medium leading-5 text-gray-900 dark:text-white/60 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm uppercase font-medium leading-5 text-gray-500 dark:text-white/70 dark:hover:text-white/90 hover:text-gray-700 hover:border-gray-300 focus:outline-none dark:focus:text-white/100 focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
