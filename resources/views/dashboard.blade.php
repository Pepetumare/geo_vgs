<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard de Asistencia') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Formulario de Marcaje Inteligente -->
                    <div id="attendance-box">
                        <p class="mb-4">
                            @if ($nextAction == 'entrada')
                                ¡Bienvenido! Presiona el botón para registrar tu entrada.
                            @else
                                Estás dentro. Presiona el botón para registrar tu salida.
                            @endif
                        </p>
                        <form id="attendance-form" action="{{ route('attendance.store') }}" method="POST"
                            class="space-y-4">
                            @csrf
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <input type="hidden" name="type" id="type" value="{{ $nextAction }}">

                            {{-- El botón se muestra condicionalmente --}}
                            @if ($nextAction == 'entrada')
                                <button type="button" id="action-btn"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-lg transition duration-300 ease-in-out w-full sm:w-auto">
                                    Marcar Entrada
                                </button>
                            @else
                                <button type="button" id="action-btn"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-lg text-lg transition duration-300 ease-in-out w-full sm:w-auto">
                                    Marcar Salida
                                </button>
                            @endif
                        </form>
                    </div>

                    <!-- Mensajes de estado -->
                    @if (session('status'))
                        <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                            role="alert">
                            <strong class="font-bold">¡Éxito!</strong>
                            <span class="block sm:inline">{{ session('status') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                            role="alert">
                            <strong class="font-bold">¡Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Historial de Registros -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900">Mis Últimos Registros</h3>
                        <div class="mt-4 border-t border-gray-200">
                            <ul class="divide-y divide-gray-200">
                                @forelse ($attendances as $item)
                                    <li class="p-4 flex items-center justify-between">
                                        <div class="flex items-center">
                                            @if ($item->type == 'entrada')
                                                <span
                                                    class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                                    <svg class="h-5 w-5 text-green-600" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                                    </svg>
                                                </span>
                                            @else
                                                <span
                                                    class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                                    <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                                    </svg>
                                                </span>
                                            @endif
                                            <div>
                                                <strong class="text-gray-800">{{ ucfirst($item->type) }}</strong>
                                                <p class="text-sm text-gray-600">
                                                    {{ $item->created_at->format('d/m/Y H:i:s') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-500 text-right">
                                            <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}"
                                                target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                                Ver en Mapa
                                            </a>
                                        </div>
                                    </li>
                                @empty
                                    <li class="p-4 text-center text-gray-500">No tienes registros aún.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('attendance-form');
                const latitudeInput = document.getElementById('latitude');
                const longitudeInput = document.getElementById('longitude');
                const actionBtn = document.getElementById('action-btn');

                if (actionBtn) {
                    actionBtn.addEventListener('click', () => {
                        if (!navigator.geolocation) {
                            alert('La geolocalización no es soportada por tu navegador.');
                            return;
                        }

                        actionBtn.disabled = true;
                        actionBtn.innerHTML = `
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Procesando...
                    `;

                        navigator.geolocation.getCurrentPosition(position => {
                            latitudeInput.value = position.coords.latitude;
                            longitudeInput.value = position.coords.longitude;
                            form.submit();
                        }, () => {
                            alert(
                                'No se pudo obtener tu ubicación. Asegúrate de haber dado los permisos necesarios y vuelve a intentarlo.');
                            actionBtn.disabled = false;
                            actionBtn.innerHTML = '{{ ucfirst($nextAction) }}';
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        });
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
