<x-guest-layout>
    <div class="flex flex-col items-center text-center">
        {{-- Logo de la aplicación --}}
        <a href="/">
            <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
        </a>

        <h1 class="mt-8 text-5xl font-bold text-gray-800 dark:text-gray-200">
            419
        </h1>

        <p class="mt-4 text-xl font-medium text-gray-700 dark:text-gray-300">
            Página Expirada
        </p>

        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Tu sesión ha expirado por inactividad. Serás redirigido a la página de inicio de sesión en unos segundos.
        </p>

        <div class="mt-6">
            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                Ir a Inicio de Sesión Ahora
            </a>
        </div>
    </div>

    <script>
        // Redirige al usuario a la página de login después de 3 segundos (3000 milisegundos)
        setTimeout(function() {
            window.location.href = "{{ route('login') }}";
        }, 3000);
    </script>
</x-guest-layout>
