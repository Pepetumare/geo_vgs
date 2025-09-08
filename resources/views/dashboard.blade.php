<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard de Asistencia') }}
            </h2>
            <a href="{{ route('overtime.create') }}"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Solicitar Horas Extras
            </a>
        </div>
        <div class="text-right text-sm text-gray-500 dark:text-gray-400 mt-1">
            Hora actual del servidor: {{ \Carbon\Carbon::now()->format('H:i') }}
        </div>
    </x-slot>

    {{-- Toda la lógica de JS se centraliza en este componente de Alpine.js --}}
    <div class="py-12" x-data="dashboardController" x-init="initTimer('{{ $clockInTime?->toIso8601String() }}')">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Formulario de Marcaje y Resumen -->
                    <div id="attendance-box" class="mb-6">
                        <p class="mb-4 text-gray-600 dark:text-gray-400">
                            @if ($nextAction == 'entrada')
                                ¡Bienvenido! Presiona el botón para registrar tu entrada.
                            @else
                                Estás dentro. Presiona el botón para registrar tu salida.
                            @endif
                        </p>
                        <form id="attendance-form" action="{{ route('attendance.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="latitude" id="latitude" x-model="latitude">
                            <input type="hidden" name="longitude" id="longitude" x-model="longitude">
                            <input type="hidden" name="type" id="type" value="{{ $nextAction }}">

                            <button type="button" id="action-btn" @click="submitAttendance()" :disabled="isLoading"
                                class="text-white font-bold py-3 px-6 rounded-lg text-lg transition duration-300 ease-in-out w-full sm:w-auto"
                                :class="{
                                    'bg-green-500 hover:bg-green-700': '{{ $nextAction }}' == 'entrada',
                                    'bg-red-500 hover:bg-red-700': '{{ $nextAction }}' == 'salida'
                                }">
                                <span x-show="!isLoading" x-text="'Marcar {{ ucfirst($nextAction) }}'"></span>
                                <span x-show="isLoading" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Procesando...
                                </span>
                            </button>
                        </form>
                    </div>

                    <!-- Sección de Resumen -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
                        <!-- Card Cronómetro -->
                        @if ($nextAction == 'salida')
                            <div class="p-4 bg-blue-100 dark:bg-gray-700 rounded-lg text-center shadow">
                                <p class="text-sm text-blue-800 dark:text-blue-300">Tiempo transcurrido del turno:</p>
                                <p id="timer"
                                    class="text-3xl font-bold text-blue-900 dark:text-blue-200 tracking-wider"
                                    x-text="timer"></p>
                            </div>
                        @endif

                        <!-- Card Horas Extras Totales -->
                        <div
                            class="p-4 bg-purple-100 dark:bg-gray-700 rounded-lg text-center shadow @if ($nextAction == 'entrada') sm:col-span-2 @endif">
                            <p class="text-sm text-purple-800 dark:text-purple-300">Total de Horas Extras Aprobadas:</p>
                            <p class="text-3xl font-bold text-purple-900 dark:text-purple-200 tracking-wider">
                                {{ number_format($totalOvertimeHours, 2) }}</p>
                        </div>
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
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
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
                                                    @click="openMap({{ $item->latitude }}, {{ $item->longitude }})"
                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Ver
                                                    en Mapa</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3"
                                                class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400">
                                                No tienes registros de asistencia aún.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $attendances->links() }}</div>
                    </div>

                    <!-- Historial de Solicitudes de Horas Extras -->
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Mis Últimas 5 Solicitudes de
                            Horas Extras</h3>
                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Fecha</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Horas</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Descripción</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($overtimeRequests as $request)
                                        <tr>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ \Carbon\Carbon::parse($request->date)->format('d/m/Y') }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $request->hours }}</td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $request->description ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if ($request->status == 'approved')
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aprobado</span>
                                                @elseif($request->status == 'rejected')
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rechazado</span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4"
                                                class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500 dark:text-gray-400">
                                                No tienes solicitudes de horas extras.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{-- La paginación de horas extras se ha eliminado de aquí --}}
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal del Mapa -->
        <div x-show="showModal" @keydown.escape.window="showModal = false" x-transition x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div @click.away="showModal = false"
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
            document.addEventListener('alpine:init', () => {
                Alpine.data('dashboardController', () => ({
                    showModal: false,
                    lat: 0,
                    lng: 0,
                    latitude: null,
                    longitude: null,
                    isLoading: false,
                    timer: '00:00:00',
                    map: null,
                    marker: null,

                    initTimer(clockInTimeString) {
                        if (!clockInTimeString) return;
                        const clockInTime = new Date(clockInTimeString);
                        setInterval(() => {
                            const now = new Date();
                            const diff = now - clockInTime;
                            const hours = String(Math.floor(diff / 3600000)).padStart(2, '0');
                            const minutes = String(Math.floor((diff % 3600000) / 60000)).padStart(2,
                                '0');
                            const seconds = String(Math.floor((diff % 60000) / 1000)).padStart(2,
                                '0');
                            this.timer = `${hours}:${minutes}:${seconds}`;
                        }, 1000);
                    },

                    submitAttendance() {
                        if (!navigator.geolocation) {
                            alert('La geolocalización no es soportada por tu navegador.');
                            return;
                        }
                        this.isLoading = true;
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.latitude = position.coords.latitude;
                                this.longitude = position.coords.longitude;
                                this.$nextTick(() => {
                                    document.getElementById('attendance-form').submit();
                                });
                            },
                            () => {
                                alert(
                                    'No se pudo obtener tu ubicación. Asegúrate de haber dado los permisos necesarios y vuelve a intentarlo.');
                                this.isLoading = false;
                            }, {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    },

                    openMap(lat, lng) {
                        this.showModal = true;
                        this.lat = lat;
                        this.lng = lng;
                        this.$nextTick(() => this.initMap());
                    },

                    initMap() {
                        if (this.map === null) {
                            this.map = L.map('map').setView([this.lat, this.lng], 16);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                            }).addTo(this.map);
                            this.marker = L.marker([this.lat, this.lng]).addTo(this.map);
                        } else {
                            this.map.setView([this.lat, this.lng], 16);
                            this.marker.setLatLng([this.lat, this.lng]);
                        }
                        setTimeout(() => this.map.invalidateSize(), 100);
                    }
                }));
            });
        </script>
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    @endpush
</x-app-layout>
