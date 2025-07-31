<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Panel de Administración
        </h2>

    
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
                            @if ($user->attendances->isNotEmpty())
                                <div x-data="{ open: false }"
                                    class="border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <button @click="open = !open"
                                        class="w-full flex items-center justify-between p-4 text-left">
                                        <span
                                            class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                                        <svg class="w-5 h-5 text-gray-500 transform transition-transform"
                                            :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="open" x-transition
                                        class="p-4 border-t border-gray-200 dark:border-gray-700">
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-700">
                                                    <tr>
                                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">
                                                            Tipo</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">
                                                            Fecha y Hora</th>
                                                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">
                                                            Acciones</th>
                                                        <th scope="col"
                                                            class="px-6 py-3 text-left text-xs font-medium ...">Alerta
                                                        </th>
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
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                                <a href="{{ route('admin.attendance.editSingle', $item) }}"
                                                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">Editar</a>
                                                            </td>
                                                            @if ($item->is_suspicious)
                                                                <span title="Posible simulación de GPS detectada.">
                                                                    <svg class="w-6 h-6 text-yellow-500" fill="none"
                                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                                    </svg>
                                                                </span>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
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
        </div>
    </div>
</x-app-layout>
