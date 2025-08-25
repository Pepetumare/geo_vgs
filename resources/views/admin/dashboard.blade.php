<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Panel de Administración
        </h2>
        <a href="{{ route('admin.attendance.createSingle') }}"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
            Añadir Registro Manual
        </a>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="{ showModal: false, lat: 0, lng: 0 }">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('status'))
                        <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative"
                            role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Formulario de Filtros -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium">Filtrar Registros</h3>
                        <form action="{{ route('admin.dashboard') }}" method="GET"
                            class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            {{-- ... tu formulario de filtros (sin cambios) ... --}}
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
                                <a href="{{ route('admin.dashboard') }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-500">Limpiar</a>
                            </div>
                        </form>
                    </div>

                    <h3 class="text-lg font-medium border-t border-gray-200 dark:border-gray-700 pt-6">Resultados</h3>

                    <div class="mt-4 space-y-2">
                        @forelse ($users as $user)
                            @if ($user->attendances->isNotEmpty())
                                <div x-data="calendar({{ $user->id }})"
                                    class="border border-gray-200 dark:border-gray-700 rounded-lg">

                                    <button @click="open = !open; showCalendar = false"
                                        class="w-full flex items-center justify-between p-4 text-left">
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                                        <svg class="w-5 h-5 text-gray-500 transform transition-transform"
                                            :class="{ 'rotate-180': open || showCalendar }" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="open || showCalendar" x-transition x-cloak
                                        class="p-4 border-t border-gray-200 dark:border-gray-700">

                                        <div class="mb-4">
                                            <button
                                                @click="showCalendar = !showCalendar; open = !showCalendar; if(showCalendar) { fetchCalendarData() }"
                                                class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-500 text-white rounded-md transition-colors">
                                                <span
                                                    x-text="showCalendar ? 'Ver Tabla de Registros' : 'Ver Calendario'"></span>
                                            </button>
                                        </div>

                                        <!-- Vista de TABLA de Registros -->
                                        <div x-show="open && !showCalendar">
                                            {{-- ... tu tabla de registros (sin cambios) ... --}}
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium uppercase">
                                                                Tipo</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium uppercase">
                                                                Fecha y Hora</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium uppercase">
                                                                Ubicación</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium uppercase">
                                                                Acciones</th>
                                                            <th
                                                                class="px-6 py-3 text-center text-xs font-medium uppercase">
                                                                Alerta</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody
                                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                        @foreach ($user->attendances as $item)
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
                                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                                    {{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                                                                <td
                                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                    <button type="button"
                                                                        @click="showModal = true; lat = {{ $item->latitude }}; lng = {{ $item->longitude }}; $nextTick(() => initMap(lat, lng))"
                                                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                                                        Ver en Mapa
                                                                    </button>
                                                                </td>
                                                                <td
                                                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                    <a href="{{ route('admin.attendance.editSingle', $item) }}"
                                                                        class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Editar</a>
                                                                </td>
                                                                <td
                                                                    class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                                    @if ($item->is_suspicious)
                                                                        <span
                                                                            title="Posible simulación de GPS detectada."
                                                                            class="inline-flex items-center">
                                                                            <svg class="w-6 h-6 text-yellow-500"
                                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd"
                                                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.21 3.03-1.742 3.03H4.42c-1.532 0-2.492-1.696-1.742-3.03l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                                    clip-rule="evenodd" />
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

                                        <!-- CAMBIO: Vista de CALENDARIO. Ahora la navegación está aquí. -->
                                        <div x-show="showCalendar">
                                            <div class="flex items-center justify-between mb-4">
                                                <button @click="changeMonth(-1)"
                                                    class="px-3 py-1 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500">&lt;
                                                    Ant</button>
                                                <h3 class="text-lg font-semibold capitalize"
                                                    x-text="currentDate.toLocaleString('es-ES', { month: 'long', year: 'numeric' })">
                                                </h3>
                                                <button @click="changeMonth(1)"
                                                    class="px-3 py-1 bg-gray-200 dark:bg-gray-600 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500">Sig
                                                    &gt;</button>
                                            </div>
                                            <!-- CAMBIO: El contenedor de la cuadrícula ahora está separado. -->
                                            <div :id="`calendar-grid-{{ $user->id }}`"
                                                class="grid grid-cols-7 gap-2 text-center">
                                                <!-- La cuadrícula del calendario se generará aquí -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <p class="text-center text-gray-500 dark:text-gray-400">No hay registros que coincidan con
                                los filtros.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Modal del Mapa (sin cambios) -->
            <div x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                style="display: none;">
                {{-- ... contenido del modal (sin cambios) ... --}}
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

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

        <script>
            // Lógica del Mapa (sin cambios)
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

            // CAMBIO: La función calendarRenderer ahora solo dibuja la cuadrícula de días.
            function calendarRenderer(containerId, year, month, data) {
                const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                const container = document.getElementById(containerId);
                if (!container) return;

                const firstDay = new Date(year, month - 1, 1).getDay();
                const daysInMonth = new Date(year, month, 0).getDate();

                const statusClasses = {
                    'asistencia': 'bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-100',
                    'inasistencia': 'bg-red-100 dark:bg-red-800 text-red-800 dark:text-red-100',
                    'feriado': 'bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-100',
                    'weekend': 'bg-gray-100 dark:bg-gray-700 opacity-60'
                };

                let html = ''; // Empezamos con un string vacío

                daysOfWeek.forEach(day => {
                    html += `<div class="font-bold text-xs p-2">${day}</div>`;
                });

                for (let i = 0; i < firstDay; i++) {
                    html += '<div></div>';
                }

                for (let day = 1; day <= daysInMonth; day++) {
                    const dateStr = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const dayStatus = data[dateStr] || '';
                    const dayOfWeek = new Date(year, month - 1, day).getDay();
                    let dayClass = (dayOfWeek === 0 || dayOfWeek === 6) && !dayStatus ? statusClasses['weekend'] : (
                        statusClasses[dayStatus] || 'bg-gray-50 dark:bg-gray-700');

                    html += `<div class="p-2 rounded-lg h-16 flex flex-col justify-center items-center text-sm ${dayClass}">
                                <span class="font-bold">${day}</span>
                                <span class="text-xs capitalize mt-1">${dayStatus.replace('_', ' ')}</span>
                             </div>`;
                }

                container.innerHTML = html;
            }

            document.addEventListener('alpine:initializing', () => {
                Alpine.data('calendar', (userId) => ({
                    open: false,
                    showCalendar: false,
                    calendarData: {},
                    currentDate: new Date(),
                    fetchCalendarData() {
                        const year = this.currentDate.getFullYear();
                        const month = this.currentDate.getMonth() + 1;
                        fetch(`/admin/calendar-data/${userId}/${year}/${month}`)
                            .then(res => res.json())
                            .then(data => {
                                this.calendarData = data;
                                this.renderCalendar();
                            }).catch(err => console.error('Error fetching calendar data:', err));
                    },
                    renderCalendar() {
                        const year = this.currentDate.getFullYear();
                        const month = this.currentDate.getMonth() + 1;
                        // CAMBIO: Apunta al nuevo contenedor de la cuadrícula
                        calendarRenderer(`calendar-grid-${userId}`, year, month, this.calendarData);
                    },
                    changeMonth(offset) {
                        const newDate = new Date(this.currentDate);
                        newDate.setDate(1);
                        newDate.setMonth(newDate.getMonth() + offset);
                        this.currentDate = newDate;
                        this.fetchCalendarData();
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
