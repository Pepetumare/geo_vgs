<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard de Asistencia') }}
            {{-- Acá crea un bloque el cual muestre la hora actual del servidor --}} <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                Hora actual del servidor: {{ \Carbon\Carbon::now()->format('H:i') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ showModal: false, lat: 0, lng: 0 }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Formulario de Marcaje Inteligente -->
                    <div id="attendance-box">
                        <p class="mb-4 text-gray-600 dark:text-gray-400">
                            @if ($nextAction == 'entrada')
                                ¡Bienvenido! Presiona el botón para registrar tu entrada.
                            @else
                                Estás dentro. Presiona el botón para registrar tu salida.
                            @endif
                        </p>

                        <!-- Cronómetro (se muestra solo si ha marcado entrada) -->
                        @if ($nextAction == 'salida')
                            <div class="mb-4 p-4 bg-blue-100 dark:bg-gray-700 rounded-lg text-center">
                                <p class="text-sm text-blue-800 dark:text-blue-300">Tiempo transcurrido del turno
                                    actual:
                                </p>
                                <p id="timer"
                                    class="text-3xl font-bold text-blue-900 dark:text-blue-200 tracking-wider">00:00:00
                                </p>
                            </div>
                        @endif

                        <form id="attendance-form" action="{{ route('attendance.store') }}" method="POST"
                            class="space-y-4">
                            @csrf
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <input type="hidden" name="type" id="type" value="{{ $nextAction }}">

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
                        <div class="mt-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative"
                            role="alert">
                            <strong class="font-bold">¡Éxito!</strong>
                            <span class="block sm:inline">{{ session('status') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mt-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded relative"
                            role="alert">
                            <strong class="font-bold">¡Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Historial de Registros -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mis Últimos Registros</h3>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Tipo</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Fecha y Hora</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Ubicación</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($attendances as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if ($item->type == 'entrada')
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Entrada</span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Salida</span>
                                                @endif
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                <button type="button"
                                                    @click="showModal = true; lat = {{ $item->latitude }}; lng = {{ $item->longitude }}; $nextTick(() => initMap(lat, lng))"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Ver
                                                    en Mapa</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3"
                                                class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400">
                                                No tienes registros aún.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="mt-4">
                            {{ $attendances->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Modal del Mapa -->
        <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
            <div @click.away="showModal = false" x-show="showModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 max-w-2xl w-full mx-4">
                <div class="flex justify-between items-center pb-3 border-b dark:border-gray-700">
                    <p class="text-2xl font-bold dark:text-white">Ubicación del Registro</p>
                    <button @click="showModal = false"
                        class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-4">
                    <div id="map" style="height: 400px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <script>
            // Script para el botón de marcaje
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
                                'No se pudo obtener tu ubicación. Asegúrate de haber dado los permisos necesarios y vuelve a intentarlo.'
                            );
                            actionBtn.disabled = false;
                            actionBtn.innerHTML = 'Marcar {{ $nextAction }}';
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        });
                    });
                }
            });

            // ======================================================================
            // SOLUCIÓN: Lógica completa para inicializar y actualizar el mapa. 🗺️
            // ======================================================================
            let map = null;
            let marker = null;

            function initMap(lat, lng) {
                // Si el mapa no ha sido creado, lo inicializamos.
                if (map === null) {
                    map = L.map('map').setView([lat, lng], 16);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);
                    // Creamos el marcador por primera vez.
                    marker = L.marker([lat, lng]).addTo(map);
                } else {
                    // Si el mapa ya existe, solo actualizamos su vista y la posición del marcador.
                    map.setView([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                }
                // Nos aseguramos de que el mapa se redibuje correctamente dentro del modal.
                setTimeout(() => map.invalidateSize(), 100);
            }

            // Script para el cronómetro
            @if ($clockInTime)
                const clockInTime = new Date("{{ $clockInTime->toIso8601String() }}");
                const timerElement = document.getElementById('timer');

                function updateTimer() {
                    const now = new Date();
                    const diff = now - clockInTime;

                    const hours = String(Math.floor(diff / 3600000)).padStart(2, '0');
                    const minutes = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
                    const seconds = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');

                    if (timerElement) {
                        timerElement.textContent = `${hours}:${minutes}:${seconds}`;
                    }
                }
                setInterval(updateTimer, 1000);
                updateTimer();
            @endif
        </script>
    @endpush
</x-app-layout>
