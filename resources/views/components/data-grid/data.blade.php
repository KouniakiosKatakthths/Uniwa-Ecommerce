@props(['hidden' => null])

@php
  $hiddenClass = match($hidden) {
    'sm'  => 'hidden sm:table-cell',
    'md'  => 'hidden md:table-cell',
    'lg'  => 'hidden lg:table-cell',
    default => ''
  };
@endphp

<td {{ $attributes->merge(['class' => "px-4 py-3 text-gray-400 $hiddenClass"]) }}>
  {{ $slot }}
</td>