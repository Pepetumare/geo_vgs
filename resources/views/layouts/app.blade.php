<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon Links -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-96x96.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- PWA Manifest y Theme Color -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#4A5568" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts y Estilos con Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register("{{ asset('sw.js') }}")
                    .then(registration => console.log('Service Worker registrado con éxito:', registration))
                    .catch(err => console.log('Error en el registro del Service Worker:', err));
            });
        }
    </script>
    <!-- PWA Install Prompt Popup -->
    <div id="install-popup"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4 transition-opacity duration-300 ease-in-out opacity-0">
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-sm w-full text-center transform transition-transform duration-300 ease-in-out scale-95">
            <div class="flex justify-center mb-4">
                <x-application-logo class="w-16 h-16 fill-current text-gray-500" />
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Instalar Geo VGS</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                Añade Geo VGS a tu pantalla de inicio para un acceso más rápido y una mejor experiencia.
            </p>
            <div class="mt-6 flex justify-center space-x-4">
                <button id="later-button"
                    class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500">
                    Más tarde
                </button>
                <button id="install-button"
                    class="px-4 py-2 rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Instalar
                </button>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let deferredPrompt;
            const installPopup = document.getElementById('install-popup');
            const installButton = document.getElementById('install-button');
            const laterButton = document.getElementById('later-button');

            // Solo continuar si el popup no se ha mostrado antes
            if (!localStorage.getItem('pwaInstallPromptShown')) {
                window.addEventListener('beforeinstallprompt', (e) => {
                    // Prevenir que el mini-infobar aparezca en móvil
                    e.preventDefault();
                    // Guardar el evento para poder dispararlo después
                    deferredPrompt = e;
                    // Mostrar nuestro popup personalizado con una transición suave
                    installPopup.classList.remove('hidden');
                    setTimeout(() => {
                        installPopup.classList.add('opacity-100');
                        installPopup.querySelector('div').classList.add('scale-100');
                    }, 10);
                });
            }

            if (installButton) {
                installButton.addEventListener('click', async () => {
                    if (!deferredPrompt) {
                        return;
                    }
                    // Ocultar nuestro popup
                    installPopup.classList.remove('opacity-100');
                    installPopup.querySelector('div').classList.remove('scale-100');
                    setTimeout(() => installPopup.classList.add('hidden'), 300);

                    // Mostrar el diálogo de instalación del navegador
                    deferredPrompt.prompt();

                    // Esperar a que el usuario responda
                    const {
                        outcome
                    } = await deferredPrompt.userChoice;

                    // Ya no necesitamos el evento guardado
                    deferredPrompt = null;

                    // Guardar que el popup ya se mostró para no volver a hacerlo
                    localStorage.setItem('pwaInstallPromptShown', 'true');
                });
            }

            if (laterButton) {
                laterButton.addEventListener('click', () => {
                    // Ocultar el popup
                    installPopup.classList.remove('opacity-100');
                    installPopup.querySelector('div').classList.remove('scale-100');
                    setTimeout(() => installPopup.classList.add('hidden'), 300);

                    // Guardar que el popup ya se mostró para no volver a molestarlo
                    localStorage.setItem('pwaInstallPromptShown', 'true');
                });
            }

            window.addEventListener('appinstalled', () => {
                // Limpiar todo si la app se instala
                deferredPrompt = null;
                if (installPopup) {
                    installPopup.classList.add('hidden');
                }
                localStorage.setItem('pwaInstallPromptShown', 'true');
            });
        });
    </script>
</body>

</html>
