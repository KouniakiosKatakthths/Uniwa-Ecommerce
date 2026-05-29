@props(['disabled' => false, 'placeholder' => ''])

<input {{ $disabled ? 'disabled' : '' }} placeholder="{{ $placeholder }}" {!! $attributes->merge(['class' => '
  border-gray-100 
  dark:placeholder-gray-600 dark:bg-white/5 dark:border-white/10 dark:text-gray-300 dark:border-solid dark:border-1 dark:focus:border-white/20
  focus:border-indigo-500 focus:ring-indigo-500 
  rounded-lg shadow-sm px-4 py-2 text-sm focus:outline-none'
]) !!}>
