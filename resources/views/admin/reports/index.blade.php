<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Panel de Reportes y Horas
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Formulario de Filtros -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium">Filtrar Reportes</h3>
                        <form action="{{ route('admin.reports') }}" method="GET"
                            class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <!-- Filtros de empleado y fecha -->
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
                            <div class="flex space-x-2">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">Filtrar</button>
                                <a href="{{ route('admin.reports') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500">Limpiar</a>
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

                                    @foreach ($data['shifts_by_day'] as $day => $shifts)
                                        <div class="mt-4">
                                            <p class="font-semibold text-sm mb-2">Día:
                                                {{ Carbon\Carbon::parse($day)->format('d/m/Y') }} - Total:
                                                {{ number_format(array_sum(array_column($shifts, 'duration_in_hours')), 2) }}
                                                horas</p>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full text-sm">
                                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th class="px-4 py-2 text-left font-medium">Entrada</th>
                                                            <th class="px-4 py-2 text-left font-medium">Salida</th>
                                                            <th class="px-4 py-2 text-left font-medium">Duración</th>
                                                            <th class="px-4 py-2 text-left font-medium">Alerta</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($shifts as $shift)
                                                            <tr class="border-b dark:border-gray-700">
                                                                <!-- --- CAMBIO CLAVE AQUÍ --- -->
                                                                <!-- Accedemos a la fecha a través de la propiedad 'created_at' -->
                                                                <td class="px-4 py-2">
                                                                    {{ $shift['entrada']->created_at->format('H:i:s') }}
                                                                </td>
                                                                <td class="px-4 py-2">
                                                                    {{ $shift['salida']->created_at->format('H:i:s') }}
                                                                </td>
                                                                <td class="px-4 py-2">
                                                                    {{ number_format($shift['duration_in_hours'], 2) }}
                                                                    hrs</td>
                                                                <td class="px-4 py-2">
                                                                    @if ($shift['entrada']->is_suspicious)
                                                                        <span
                                                                            title="Posible simulación de GPS detectada.">
                                                                            <svg class="w-5 h-5 text-yellow-500"
                                                                                fill="none" viewBox="0 0 24 24"
                                                                                stroke="currentColor">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round"
                                                                                    stroke-width="2"
                                                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                                            </svg>
                                                                        </span>
                                                                    @endif
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
        </div>
    </div>

    <!-- Incluir Chart.js y script del gráfico -->
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
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
