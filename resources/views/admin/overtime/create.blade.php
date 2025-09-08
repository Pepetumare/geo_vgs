<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Añadir Horas Extras
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                    {{-- Muestra errores de validación si los hay --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">¡Error!</strong>
                            <span class="block sm:inline">Por favor, corrige los siguientes errores:</span>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.overtime.store') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <!-- Selector de Empleado -->
                            <div>
                                <label for="user_id_overtime" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Empleado</label>
                                <select name="user_id" id="user_id_overtime" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="" disabled selected>Selecciona un empleado</option>
                                    @foreach ($allUsers as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Selector de Fecha -->
                            <div>
                                <label for="date_overtime" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha</label>
                                <input type="date" name="date" id="date_overtime" required value="{{ old('date', now()->format('Y-m-d')) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Input de Horas -->
                            <div>
                                <label for="hours_overtime" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cantidad de Horas</label>
                                <input type="number" name="hours" id="hours_overtime" required step="0.01" min="0.01" value="{{ old('hours') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Ej: 2.5">
                            </div>

                            <!-- Descripción (opcional) -->
                            <div>
                                <label for="description_overtime" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción (Opcional)</label>
                                <input type="text" name="description" id="description_overtime" value="{{ old('description') }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Ej: Apoyo en cierre mensual">
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end space-x-3">
                            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300">Cancelar</a>
                            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-500">Guardar Horas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
