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
                    
                    <!-- Formulario de Marcaje -->
                    <div id="attendance-box">
                        <p class="mb-4">Usa los botones para registrar tu entrada o salida. Se te pedirá permiso para acceder a tu ubicación.</p>
                        <form id="attendance-form" action="{{ route('attendance.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <input type="hidden" name="type" id="type">

                            <div class="flex space-x-4">
                                <button type="button" id="clock-in" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
                                    Marcar Entrada
                                </button>
                                <button type="button" id="clock-out" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out">
                                    Marcar Salida
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Mensaje de estado -->
                    @if (session('status'))
                        <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">¡Éxito!</strong>
                            <span class="block sm:inline">{{ session('status') }}</span>
                        </div>
                    @endif
                    
                    <!-- Historial de Registros -->
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900">Mis Últimos Registros</h3>
                        <div class="mt-4 border-t border-gray-200">
                            <ul class="divide-y divide-gray-200">
                                @forelse ($attendances as $item)
                                    <li class="p-4">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <strong class="text-indigo-600">{{ ucfirst($item->type) }}</strong>
                                                <span class="text-sm text-gray-600 ms-2">{{ $item->created_at->format('d/m/Y H:i:s') }}</span>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                (Lat: {{ number_format($item->latitude, 4) }}, Lon: {{ number_format($item->longitude, 4) }})
                                            </div>
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
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('attendance-form');
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            const typeInput = document.getElementById('type');
            const clockInBtn = document.getElementById('clock-in');
            const clockOutBtn = document.getElementById('clock-out');

            function disableButtons() {
                clockInBtn.disabled = true;
                clockOutBtn.disabled = true;
                clockInBtn.innerText = 'Procesando...';
                clockOutBtn.innerText = 'Procesando...';
                clockInBtn.classList.add('opacity-50', 'cursor-not-allowed');
                clockOutBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }

            function getLocationAndSubmit(attendanceType) {
                if (!navigator.geolocation) {
                    alert('La geolocalización no es soportada por tu navegador.');
                    return;
                }
                
                disableButtons();

                navigator.geolocation.getCurrentPosition(position => {
                    latitudeInput.value = position.coords.latitude;
                    longitudeInput.value = position.coords.longitude;
                    typeInput.value = attendanceType;
                    form.submit();
                }, () => {
                    alert('No se pudo obtener tu ubicación. Asegúrate de haber dado los permisos necesarios y vuelve a intentarlo.');
                    // Reactivar botones si hay error
                    window.location.reload();
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });
            }

            clockInBtn.addEventListener('click', () => getLocationAndSubmit('entrada'));
            clockOutBtn.addEventListener('click', () => getLocationAndSubmit('salida'));
        });
    </script>
    @endpush
</x-app-layout>
