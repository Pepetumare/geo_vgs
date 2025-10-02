<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $provider->name }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.providers.edit', $provider) }}"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                    Editar
                </a>
                <a href="{{ route('admin.providers.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-transparent rounded-md font-semibold text-xs text-gray-800 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative"
                    role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg h-full">
                        <div class="p-6 text-gray-900 dark:text-gray-100 space-y-4">
                            <h3 class="text-lg font-semibold">Información del proveedor</h3>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Contacto</p>
                                <p>{{ $provider->contact_name ?? 'No registrado' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Correo</p>
                                <p>{{ $provider->email ?? 'No registrado' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Teléfono</p>
                                <p>{{ $provider->phone ?? 'No registrado' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Dirección</p>
                                <p>{{ $provider->address ?? 'No registrada' }}</p>
                            </div>
                            @if ($provider->notes)
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Notas</p>
                                    <p>{{ $provider->notes }}</p>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Insumos registrados</p>
                                <p>{{ $provider->supplies->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-4">Agregar insumo</h3>
                            <form method="POST" action="{{ route('admin.providers.supplies.store', $provider) }}"
                                class="space-y-4">
                                @csrf

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="name" value="Nombre del insumo" />
                                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                            value="{{ old('name') }}" required />
                                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="unit" value="Unidad" />
                                        <x-text-input id="unit" name="unit" type="text" class="mt-1 block w-full"
                                            value="{{ old('unit') }}" />
                                        <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="unit_price" value="Precio unitario" />
                                        <x-text-input id="unit_price" name="unit_price" type="number" min="0" step="0.01"
                                            class="mt-1 block w-full" value="{{ old('unit_price') }}" />
                                        <x-input-error :messages="$errors->get('unit_price')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="stock" value="Stock" />
                                        <x-text-input id="stock" name="stock" type="number" min="0" step="1"
                                            class="mt-1 block w-full" value="{{ old('stock') }}" />
                                        <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="description" value="Descripción" />
                                    <textarea id="description" name="description" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:border-gray-700">{{ old('description') }}</textarea>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </div>

                                <div class="flex justify-end">
                                    <x-primary-button>
                                        Agregar insumo
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Insumos registrados</h3>
                    @if ($provider->supplies->isEmpty())
                        <p class="text-sm text-gray-500">No hay insumos registrados para este proveedor.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Nombre
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Unidad
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Precio unitario
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Stock
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Acciones
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($provider->supplies as $supply)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                <div>{{ $supply->name }}</div>
                                                @if ($supply->description)
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $supply->description }}</p>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $supply->unit ?? '—' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                S/ {{ number_format($supply->unit_price, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                                {{ $supply->stock }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <form method="POST"
                                                    action="{{ route('admin.providers.supplies.destroy', [$provider, $supply]) }}"
                                                    onsubmit="return confirm('¿Deseas eliminar este insumo?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-600 hover:text-red-800 dark:text-red-400">
                                                        Eliminar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
