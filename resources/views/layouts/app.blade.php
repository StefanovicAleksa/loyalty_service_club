<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>
    <link rel="icon" href="{{asset('assets/favicon.ico')}}">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&display=swap" rel="stylesheet">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-row justify-center items-center bg-cover bg-no-repeat bg-[-180px] lg:bg-center" style="background-image: url('{{asset('assets/background.webp')}}');">
    <x-theme-toggle lightTheme="light" darkTheme="dark" class="btn btn-circle fixed right-4 top-4 z-10 shadow-xl" />
    @auth
        <livewire:account class="fixed left-4 top-4 z-20 shadow-xl rounded-full"/>
    @endauth
        @yield('content')
    @livewireScripts
</body>
</html>