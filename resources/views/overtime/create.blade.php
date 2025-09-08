    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Solicitar Horas Extras
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">

                        @if ($errors->any())
                            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                                <strong class="font-bold">¡Error!</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>- {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('overtime.store') }}" method="POST">
                            @csrf
                            <div class="space-y-6">
                                <div>
                                    <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha</label>
                                    <input type="date" name="date" id="date" required value="{{ old('date', now()->format('Y-m-d')) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label for="hours" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cantidad de Horas</label>
                                    <input type="number" name="hours" id="hours" required step="0.01" min="0.01" value="{{ old('hours') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm"
                                        placeholder="Ej: 2.5">
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Motivo (Opcional)</label>
                                    <input type="text" name="description" id="description" value="{{ old('description') }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm"
                                        placeholder="Ej: Apoyo en cierre mensual">
                                </div>
                            </div>
                            <div class="mt-8 flex justify-end space-x-3">
                                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded-md">Cancelar</a>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500">Enviar Solicitud</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
    
