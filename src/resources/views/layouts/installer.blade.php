<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ config('installer.app_name', 'Eii Laravel Installer') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('vendor/installer/css/installer.css') }}">

    @if (config('installer.custom_css'))
        <link rel="stylesheet" href="{{ config('installer.custom_css') }}">
    @endif

    @livewireStyles

    @stack('styles')
</head>

<body class="relative bg-slate-900 antialiased font-sans">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 starting:opacity-0 opacity-100 transition-opacity duration-700 relative overflow-hidden">

        <div class="w-full max-w-5xl z-10">
            <div class="text-center mb-10">
                @if (config('installer.app_logo'))
                    <img src="{{ config('installer.app_logo') }}" alt="Logo" class="w-16 h-16 mx-auto mb-4 object-contain drop-shadow-sm">
                @endif
                <h1 class="text-white font-extrabold text-3xl tracking-tight">{{ config('installer.app_name', 'Laravel Installer') }}</h1>
                <p class="text-slate-300 text-sm mt-2 font-medium tracking-widest">{{ config('installer.app_description', 'Setup Wizard') }}</p>
            </div>

            <main>
                @isset($slot)
                    {{ $slot }}
                @endisset
            </main>

            <div class="mt-12 text-center">
                <a href="https://github.com/amit-eiitech/laravel-installer" target="_blank" class="text-[10px] text-slate-400 hover:text-red-600 transition-all duration-300 uppercase tracking-[0.2em] font-semibold">
                    &copy; {{ date('Y') }} &bull; eii laravel installer
                </a>
            </div>
        </div>

    </div>

    @stack('scripts')

    @livewireScripts
</body>

</html>
