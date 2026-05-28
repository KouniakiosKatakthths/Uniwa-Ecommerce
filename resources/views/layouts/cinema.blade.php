<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">


    <title>CineLux</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>

  <body class="dark:bg-slate-950 bg-gray-100 min-h-screen flex flex-col">
    @include('partials.navbar')

    <main class="flex-1">@yield('content')</main>

    @include('partials.footer')
  </body>
</html>