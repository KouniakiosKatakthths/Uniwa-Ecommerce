@props(['hidden' => null])

@php
  $hiddenClass = match($hidden) {
    'sm'  => 'hidden sm:table-cell',
    'md'  => 'hidden md:table-cell',
    'lg'  => 'hidden lg:table-cell',
    default => ''
  };
@endphp

<th {{ $attributes->merge(['class' => "px-4 py-3 $hiddenClass"]) }}>
  {{ $slot }}
</th>