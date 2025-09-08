<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Panel de Administración
        </h2>
        <div class="flex items-center space-x-2">
            {{-- CAMBIO: El botón ahora es un enlace a la nueva página. --}}
            <a href="{{ route('admin.overtime.create') }}"
                class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-500">
                Añadir Horas Extras
            </a>
            <a href="{{ route('admin.attendance.createSingle') }}"
                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                Añadir Registro Manual
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('status'))
                        <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative"
                            role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="mb-6">
                        <h3 class="text-lg font-medium">Filtrar Registros</h3>
                        <form action="{{ route('admin.dashboard') }}" method="GET"
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
                            @if (isset($user->combinedRecords) && $user->combinedRecords->isNotEmpty())
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

                                        <div x-show="open && !showCalendar">
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium uppercase">
                                                                Tipo</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium uppercase">
                                                                Fecha y Hora / Horas</th>
                                                            <th
                                                                class="px-6 py-3 text-left text-xs font-medium uppercase">
                                                                Ubicación / Descripción</th>
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
                                                        @foreach ($user->combinedRecords as $item)
                                                            @if ($item instanceof \App\Models\Attendance)
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
                                                                        {{ $item->created_at->format('d/m/Y H:i:s') }}
                                                                    </td>
                                                                    <td
                                                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                        <button type="button"
                                                                            @click="$store.modals.openMap({{ $item->latitude }}, {{ $item->longitude }})"
                                                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Ver
                                                                            en Mapa</button>
                                                                    </td>
                                                                    <td
                                                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                        <div class="flex items-center space-x-4">
                                                                            <a href="{{ route('admin.attendance.editSingle', $item) }}"
                                                                                class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Editar</a>
                                                                            <form
                                                                                action="{{ route('admin.attendance.destroySingle', $item) }}"
                                                                                method="POST"
                                                                                onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro?');">
                                                                                @csrf @method('DELETE')
                                                                                <button type="submit"
                                                                                    class="text-red-600 hover:text-red-900 dark:text-red-400">Eliminar</button>
                                                                            </form>
                                                                        </div>
                                                                    </td>
                                                                    <td
                                                                        class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                                        @if ($item->is_suspicious)
                                                                            <span
                                                                                title="Posible simulación de GPS detectada."
                                                                                class="inline-flex items-center">
                                                                                <svg class="w-6 h-6 text-yellow-500"
                                                                                    fill="currentColor"
                                                                                    viewBox="0 0 20 20">
                                                                                    <path fill-rule="evenodd"
                                                                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.21 3.03-1.742 3.03H4.42c-1.532 0-2.492-1.696-1.742-3.03l5.58-9.92zM10 13a1 1 0 110-2 1 1 0 010 2zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                                                        clip-rule="evenodd" />
                                                                                </svg>
                                                                            </span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @elseif ($item instanceof \App\Models\Overtime)
                                                                <tr class="bg-purple-50 dark:bg-purple-900/20">
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                                        <span
                                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">Horas
                                                                            Extras</span>
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                                        <span class="font-bold">{{ $item->hours }}
                                                                            horas</span>
                                                                        <span
                                                                            class="block text-xs text-gray-500">{{ Carbon\Carbon::parse($item->date)->format('d/m/Y') }}</span>
                                                                    </td>
                                                                    <td
                                                                        class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                                                        {{ $item->description ?: 'N/A' }}</td>
                                                                    <td
                                                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                        <form
                                                                            action="{{ route('admin.overtime.destroy', $item) }}"
                                                                            method="POST"
                                                                            onsubmit="return confirm('¿Estás seguro de que deseas eliminar estas horas extras?');">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit"
                                                                                class="text-red-600 hover:text-red-900 dark:text-red-400">Eliminar</button>
                                                                        </form>
                                                                    </td>
                                                                    <td class="px-6 py-4"></td>
                                                                </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

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
                                            <div :id="`calendar-grid-{{ $user->id }}`"
                                                class="grid grid-cols-7 gap-2 text-center">
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

            <!-- Modal del Mapa -->
            <div x-show="$store.modals.map.show" x-init="$watch('$store.modals.map.show', value => { if (value) { $store.modals.initMap() } })" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                style="display: none;" x-cloak>
                <div @click.away="$store.modals.map.show = false"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-4 max-w-2xl w-full mx-4">
                    <div class="flex justify-between items-center pb-3 border-b dark:border-gray-700">
                        <p class="text-2xl font-bold dark:text-white">Ubicación del Registro</p>
                        <button @click="$store.modals.map.show = false"
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

            {{-- EL MODAL DE HORAS EXTRAS HA SIDO ELIMINADO DE AQUÍ --}}

        </div>
    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
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

                let html = '';
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

            document.addEventListener('alpine:init', () => {
                // CAMBIO: Se elimina la propiedad 'overtime' del store, ya no es necesaria.
                Alpine.store('modals', {
                    map: {
                        show: false,
                        lat: 0,
                        lng: 0,
                        leafletMap: null,
                        leafletMarker: null,
                    },
                    openMap(lat, lng) {
                        this.map.lat = lat;
                        this.map.lng = lng;
                        this.map.show = true;
                    },
                    initMap() {
                        if (this.map.leafletMap === null) {
                            this.map.leafletMap = L.map('map').setView([this.map.lat, this.map.lng], 16);
                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                            }).addTo(this.map.leafletMap);
                            this.map.leafletMarker = L.marker([this.map.lat, this.map.lng]).addTo(this.map
                                .leafletMap);
                        } else {
                            this.map.leafletMap.setView([this.map.lat, this.map.lng], 16);
                            this.map.leafletMarker.setLatLng([this.map.lat, this.map.lng]);
                        }
                        setTimeout(() => this.map.leafletMap.invalidateSize(), 100);
                    }
                });

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
