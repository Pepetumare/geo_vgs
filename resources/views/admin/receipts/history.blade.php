<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Historial de Boletas por RUT
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium">Buscar Boletas por RUT</h3>
                    <form action="{{ route('admin.receipts.history') }}" method="GET" class="mt-4 flex items-end gap-4">
                        <div>
                            <label for="client_rut" class="block text-sm font-medium">RUT del Cliente</label>
                            <input type="text" name="client_rut" id="client_rut" value="{{ $searchRut ?? '' }}" class="mt-1 block w-full md:w-64 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm" required>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                Buscar
                            </button>
                        </div>
                    </form>

                    @if($searchRut)
                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-md font-semibold">Resultados para el RUT: {{ $searchRut }}</h4>
                            <div class="mt-4 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">N° Boleta</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Fecha</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Total</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium uppercase">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse ($receipts as $receipt)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ str_pad($receipt->correlative_number, 6, '0', STR_PAD_LEFT) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $receipt->created_at->format('d/m/Y H:i') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">$ {{ number_format($receipt->total_amount, 0, ',', '.') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('admin.receipts.show', $receipt) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                                        Ver / Imprimir
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-4 text-center text-gray-500">No se encontraron boletas para este RUT.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
