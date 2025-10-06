<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Panel de Reportes y Horas
        </h2>
    </x-slot>

    <div class="py-12">
        {{-- Inicializamos Alpine.js para el modal --}}
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ showModal: false, lat: 0, lng: 0 }">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Formulario de Filtros -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium">Filtrar Reportes</h3>
                        <form action="{{ route('admin.reports') }}" method="GET"
                            class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <label for="user_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Empleado</label>
                                <select name="user_id" id="user_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                    <option value="">Todos los empleados</option>
                                    @foreach ($allUsers as $user)
                                        <option value="{{ $user->id }}"
                                            {{ ($filters['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="start_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de
                                    Inicio</label>
                                <input type="date" name="start_date" id="start_date"
                                    value="{{ $filters['start_date'] ?? '' }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="end_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de
                                    Fin</label>
                                <input type="date" name="end_date" id="end_date"
                                    value="{{ $filters['end_date'] ?? '' }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 6h16M4 12h8m-8 6h16" />
                                    </svg>
                                    Filtrar
                                </button>
                                <button type="submit" formaction="{{ route('admin.reports.export') }}"
                                    formtarget="_blank"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 5v9m0 0l-3-3m3 3l3-3m-9 8h12" />
                                    </svg>
                                    Exportar PDF
                                </button>
                                <a href="{{ route('admin.reports') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 6h18M9 6v12m6-12v12M4 18h16" />
                                    </svg>
                                    Limpiar
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Pestañas -->
                    <div x-data="{ tab: 'general' }" class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="border-b border-gray-200 dark:border-gray-700">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                <button @click="tab = 'general'"
                                    :class="{ 'border-indigo-500 text-indigo-600': tab === 'general', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'general' }"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Vista General (Gráficos)
                                </button>
                                <button @click="tab = 'detallado'"
                                    :class="{ 'border-indigo-500 text-indigo-600': tab === 'detallado', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'detallado' }"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                    Vista Detallada
                                </button>
                            </nav>
                        </div>

                        <!-- Contenido Pestaña General -->
                        <div x-show="tab === 'general'" class="mt-6">
                            @if (empty($reportData))
                                <p class="text-center text-gray-500 dark:text-gray-400">No hay datos suficientes para
                                    generar gráficos.</p>
                            @else
                                <h3 class="text-lg font-medium">Horas Totales por Empleado</h3>
                                <div class="mt-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <canvas id="hoursChart"></canvas>
                                </div>
                            @endif
                        </div>

                        <!-- Contenido Pestaña Detallada -->
                        <div x-show="tab === 'detallado'" class="mt-6">
                            @forelse ($reportData as $data)
                                <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <h4 class="text-md font-semibold">{{ $data['user_name'] }} - Total:
                                        {{ number_format($data['total_hours'], 2) }} horas</h4>

                                    @foreach ($data['shifts_by_day'] as $day => $dayData)
                                        <div class="mt-4">
                                            <p class="font-semibold text-sm mb-2">Día:
                                                {{ Carbon\Carbon::parse($day)->format('d/m/Y') }} - Total:
                                                {{ number_format($dayData['total_hours'], 2) }}
                                                horas</p>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full text-sm">
                                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th class="px-4 py-2 text-left font-medium">Entrada</th>
                                                            <th class="px-4 py-2 text-left font-medium">Ubic.</th>
                                                            <th class="px-4 py-2 text-left font-medium">Salida</th>
                                                            <th class="px-4 py-2 text-left font-medium">Ubic.</th>
                                                            <th class="px-4 py-2 text-left font-medium">Duración</th>
                                                            <th class="px-4 py-2 text-left font-medium">Alerta</th>
                                                            <th class="px-4 py-2 text-left font-medium">Acciones</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($dayData['segments'] as $shift)
                                                            <tr class="border-b dark:border-gray-700">
                                                                <td class="px-4 py-2">
                                                                    {{ $shift['entrada_at']->format('H:i:s') }}
                                                                </td>
                                                                <td class="px-4 py-2">
                                                                    <button type="button"
                                                                        @click="showModal = true; lat = {{ $shift['entrada']->latitude }}; lng = {{ $shift['entrada']->longitude }}; $nextTick(() => initMap(lat, lng))"
                                                                        class="text-indigo-600 hover:text-indigo-900 text-xs">Ver</button>
                                                                </td>
                                                                <td class="px-4 py-2">
                                                                    {{ $shift['salida_at']->format('H:i:s') }}
                                                                </td>
                                                                <td class="px-4 py-2">
                                                                    <button type="button"
                                                                        @click="showModal = true; lat = {{ $shift['salida']->latitude }}; lng = {{ $shift['salida']->longitude }}; $nextTick(() => initMap(lat, lng))"
                                                                        class="text-indigo-600 hover:text-indigo-900 text-xs">Ver</button>
                                                                </td>
                                                                <td class="px-4 py-2">
                                                                    {{ number_format($shift['duration_in_hours'], 2) }}
                                                                    hrs</td>
                                                                <td class="px-4 py-2">
                                                                    @if ($shift['entrada']->is_suspicious)
                                                                        <span
                                                                            title="Posible simulación de GPS detectada.">
                                                                            <svg class="w-5 h-5 text-yellow-500"
                                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd"
                                                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.21 3.03-1.742 3.03H4.42c-1.532 0-2.492-1.696-1.742-3.03l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                                    clip-rule="evenodd" />
                                                                            </svg>
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="px-4 py-2">
                                                                    <a href="{{ route('admin.attendance.edit', ['entry' => $shift['entrada'], 'exit' => $shift['salida']]) }}"
                                                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 text-xs">
                                                                        Editar
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @empty
                                <p class="text-center text-gray-500 dark:text-gray-400">No hay registros que coincidan
                                    con los filtros.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Map -->
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                style="display: none;">
                <div @click.away="showModal = false" x-show="showModal" x-transition
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
    </div>

    <!-- Incluir Chart.js y script del gráfico -->
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <script>
            let map = null;
            let marker = null;

            function initMap(lat, lng) {
                if (map === null) {
                    map = L.map('map').setView([lat, lng], 16);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);
                    marker = L.marker([lat, lng]).addTo(map);
                } else {
                    map.setView([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                }
                setTimeout(() => map.invalidateSize(), 100);
            }

            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('hoursChart');
                if (ctx) {
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: {!! $chartLabels !!},
                            datasets: [{
                                label: 'Horas Trabajadas',
                                data: {!! $chartData !!},
                                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                                borderColor: 'rgba(79, 70, 229, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
