@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => '
  border-gray-100 
  dark:border-slate-500 dark:text-gray-200 dark:border-solid dark:border-1
  focus:border-indigo-500 focus:ring-indigo-500 
  rounded-md shadow-sm p-1'
]) !!}>
