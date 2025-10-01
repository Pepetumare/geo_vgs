<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Añadir Registro Manual
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium">Nuevo Registro de Asistencia</h3>

                    <form action="{{ route('admin.attendance.storeSingle') }}" method="POST" class="mt-6 space-y-6"
                        novalidate>
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="user_id" class="block text-sm font-medium">Empleado</label>
                                <select name="user_id" id="user_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm"
                                    required aria-required="true">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                                            {{ $user->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium">Tipo de Marcaje</label>
                                <select name="type" id="type"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm"
                                    required aria-required="true">
                                    <option value="entrada" @selected(old('type') === 'entrada')>Entrada</option>
                                    <option value="salida" @selected(old('type') === 'salida')>Salida</option>
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="timestamp" class="block text-sm font-medium">Fecha y Hora del Registro</label>
                            <input type="datetime-local" name="timestamp" id="timestamp"
                                value="{{ old('timestamp', now()->timezone(config('app.timezone'))->format('Y-m-d\TH:i')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm"
                                required aria-required="true">
                            @error('timestamp')
                                <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>


                        <!-- Mapa Interactivo -->
                        <div>
                            <label class="block text-sm font-medium">Ubicación (arrastra el marcador si es
                                necesario)</label>
                            <div id="map"
                                class="mt-1 h-64 w-full rounded-md border border-gray-300 dark:border-gray-700"></div>
                        </div>

                        <!-- Campos de Coordenadas (ocultos o visibles para depuración) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="latitude" class="block text-sm font-medium">Latitud</label>
                                <input type="text" name="latitude" id="latitude" readonly
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm bg-gray-100 dark:bg-gray-800"
                                    required>
                            </div>
                            <div>
                                <label for="longitude" class="block text-sm font-medium">Longitud</label>
                                <input type="text" name="longitude" id="longitude" readonly
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm bg-gray-100 dark:bg-gray-800"
                                    required>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">Guardar
                                Registro</button>
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancelar</a>
                        </div>

                        <a href="{{ route('admin.attendance.createMultiple') }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                            Marcación múltiple
                        </a>
                    </form>
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
            document.addEventListener('DOMContentLoaded', function() {
                const latInput = document.getElementById('latitude');
                const lngInput = document.getElementById('longitude');

                // Coordenadas iniciales desde el controlador
                const initialLat = {{ $companyLocation['lat'] }};
                const initialLng = {{ $companyLocation['lng'] }};

                // Llenar los campos con las coordenadas iniciales
                latInput.value = initialLat;
                lngInput.value = initialLng;

                // Inicializar el mapa
                const map = L.map('map').setView([initialLat, initialLng], 16);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Crear un marcador arrastrable
                const marker = L.marker([initialLat, initialLng], {
                    draggable: true
                }).addTo(map);

                // Evento que se dispara cuando se termina de arrastrar el marcador
                marker.on('dragend', function(e) {
                    const newLatLng = e.target.getLatLng();
                    latInput.value = newLatLng.lat.toFixed(7);
                    lngInput.value = newLatLng.lng.toFixed(7);
                });
            });
        </script>
    @endpush
</x-app-layout>
