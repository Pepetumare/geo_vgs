<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Turno de {{ $entry->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium">Editando el turno del día {{ $entry->created_at->format('d/m/Y') }}</h3>

                    <form action="{{ route('admin.attendance.update', ['entry' => $entry, 'exit' => $exit]) }}" method="POST" class="mt-6 space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Campo de Hora de Entrada -->
                        <div>
                            <label for="entry_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hora de Entrada</label>
                            <input type="datetime-local" name="entry_time" id="entry_time"
                                   value="{{ $entry->created_at->format('Y-m-d\TH:i') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                            @error('entry_time')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Campo de Hora de Salida -->
                        <div>
                            <label for="exit_time" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Hora de Salida</label>
                            <input type="datetime-local" name="exit_time" id="exit_time"
                                   value="{{ $exit->created_at->format('Y-m-d\TH:i') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                            @error('exit_time')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">Guardar Cambios</button>
                            <a href="{{ route('admin.reports') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>