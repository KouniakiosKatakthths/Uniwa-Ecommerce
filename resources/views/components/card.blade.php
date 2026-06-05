@props(['variant' => 'normal', 'tag' => 'div', 'href' => null])
@php
$classes = '';
if ($variant === 'normal')
    $classes = 'w-full mt-6 px-6 py-4 dark:bg-slate-800 shadow-md overflow-hidden sm:rounded-lg dark:shadow-[0_4px_40px_rgba(30,30,30,0.6)]';
else if ($variant === 'accent')
    $classes = 'group flex items-center justify-between px-6 py-3 rounded-xl border border-gray-700 bg-gray-800/60 hover:border-accent hover:bg-gray-800 transition-all duration-200 dark:shadow-[0_4px_40px_rgba(30,30,30,0.6)]';
@endphp

<{{ $tag }} {{ $href ? "href=$href" : '' }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</{{ $tag }}>