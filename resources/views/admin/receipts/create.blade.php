<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Emitir Boleta de Venta
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium">Nueva Boleta (N° {{ $nextCorrelative }})</h3>
                    <form action="{{ route('admin.receipts.store') }}" method="POST" class="mt-6 space-y-6">
                        @csrf
                        <div>
                            <label for="client_name" class="block text-sm font-medium">Nombre del Cliente</label>
                            <input type="text" name="client_name" id="client_name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm" required>
                        </div>
                        <div>
                            <label for="client_rut" class="block text-sm font-medium">RUT del Cliente (Opcional)</label>
                            <input type="text" name="client_rut" id="client_rut" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm">
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium">Descripción del Servicio/Producto</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm" required></textarea>
                        </div>
                        <div>
                            <label for="net_amount" class="block text-sm font-medium">Monto Neto ($)</label>
                            <input type="number" name="net_amount" id="net_amount" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm" required>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                Generar e Imprimir
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
