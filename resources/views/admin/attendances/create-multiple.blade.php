<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Añadir Registros Manuales (Varios días)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form action="{{ route('admin.attendance.storeMultiple') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Selección de Empleado -->
                        <div>
                            <label for="user_id" class="block text-sm font-medium">Empleado</label>
                            <select name="user_id" id="user_id"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm"
                                required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Número de días -->
                        <div>
                            <label for="days_count" class="block text-sm font-medium">Número de días a marcar</label>
                            <input type="number" min="1" max="30" id="days_count"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm"
                                placeholder="Ej: 4">
                            <button type="button" id="generate_days"
                                class="mt-2 px-3 py-2 bg-indigo-600 text-white rounded">Generar</button>
                        </div>

                        <!-- Contenedor dinámico de días -->
                        <div id="days_container" class="space-y-6"></div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                Guardar Registros
                            </button>
                            <a href="{{ route('admin.dashboard') }}"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('generate_days').addEventListener('click', function() {
                const count = parseInt(document.getElementById('days_count').value);
                const container = document.getElementById('days_container');
                container.innerHTML = ''; // limpiar contenido previo

                if (!count || count < 1) return;

                for (let i = 0; i < count; i++) {
                    const dayBlock = document.createElement('div');
                    dayBlock.classList.add('border', 'p-4', 'rounded', 'bg-gray-50', 'dark:bg-gray-700');

                    dayBlock.innerHTML = `
                        <h4 class="font-semibold mb-2">Día ${i + 1}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium">Fecha</label>
                                <input type="date" name="days[${i}][date]" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Hora de Entrada</label>
                                <input type="time" name="days[${i}][entry_time]" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium">Hora de Salida</label>
                                <input type="time" name="days[${i}][exit_time]" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm">
                            </div>
                        </div>
                    `;

                    container.appendChild(dayBlock);
                }
            });
        </script>
    @endpush
</x-app-layout>
