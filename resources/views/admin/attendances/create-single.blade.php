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

                    <form action="{{ route('admin.attendance.storeSingle') }}" method="POST" class="mt-6 space-y-6">
                        @csrf

                        <!-- Selección de Empleado -->
                        <div>
                            <label for="user_id" class="block text-sm font-medium">Empleado</label>
                            <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tipo de Marcaje -->
                        <div>
                            <label for="type" class="block text-sm font-medium">Tipo de Marcaje</label>
                            <select name="type" id="type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm" required>
                                <option value="entrada">Entrada</option>
                                <option value="salida">Salida</option>
                            </select>
                        </div>

                        <!-- Fecha y Hora -->
                        <div>
                            <label for="timestamp" class="block text-sm font-medium">Fecha y Hora del Registro</label>
                            <input type="datetime-local" name="timestamp" id="timestamp" value="{{ now()->format('Y-m-d\TH:i') }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm" required>
                        </div>

                        <!-- Ubicación -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="latitude" class="block text-sm font-medium">Latitud</label>
                                <input type="text" name="latitude" id="latitude" placeholder="Ej: -39.81422" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm" required>
                            </div>
                            <div>
                                <label for="longitude" class="block text-sm font-medium">Longitud</label>
                                <input type="text" name="longitude" id="longitude" placeholder="Ej: -73.24589" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm" required>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">Guardar Registro</button>
                            <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>