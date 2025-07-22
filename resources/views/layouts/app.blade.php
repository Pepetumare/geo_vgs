<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- PWA Manifest y Theme Color -->
        {{-- Estos metadatos son esenciales para que la app sea instalable --}}
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#4A5568"/>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts y Estilos con Vite -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            {{-- Incluimos la barra de navegación --}}
            @include('layouts.navigation')

            <!-- Cabecera de la Página -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Contenido Principal de la Página -->
            <main>
                {{ $slot }}
            </main>
        </div>

        {{-- Stack para scripts específicos de cada página (como el de geolocalización) --}}
        @stack('scripts')

        <!-- Script para registrar el Service Worker de la PWA -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register("{{ asset('sw.js') }}")
                        .then(registration => console.log('Service Worker registrado con éxito:', registration))
                        .catch(err => console.log('Error en el registro del Service Worker:', err));
                });
            }
        </script>
    </body>
</html>