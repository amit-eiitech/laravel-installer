<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Eii Laravel Installer') }}</title>

    <link rel="stylesheet" href="{{ asset('vendor/installer/css/installer.css') }}">
    {{-- @livewireStyles --}}
</head>

<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center starting:opacity-0 opacity-100 transition-opacity duration-700">
        <main>
            <h1 class="text-gray-400 m-2">Laravel Installer</h1>
            @isset($slot)
                {{ $slot }}
            @endisset
            <p class="text-xs text-gray-400 text-center mt-6">by Eii Tech Solutions</p>
        </main>
    </div>
    {{-- @livewireScripts --}}
</body>

</html>
