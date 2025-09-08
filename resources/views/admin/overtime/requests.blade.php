<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Gestionar Solicitudes de Horas Extras
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('status'))
                <div class="mb-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded relative"
                    role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Solicitudes Pendientes -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium">Solicitudes Pendientes</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Empleado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Horas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Descripción</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($pendingRequests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($request->date)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $request->hours }}</td>
                                        <td class="px-6 py-4">{{ $request->description ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <!-- Formulario para Aprobar -->
                                                <form action="{{ route('admin.overtime.update', $request) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="px-3 py-1 text-xs bg-green-500 text-white rounded-md hover:bg-green-600">Aprobar</button>
                                                </form>
                                                <!-- Formulario para Rechazar -->
                                                <form action="{{ route('admin.overtime.update', $request) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="px-3 py-1 text-xs bg-red-500 text-white rounded-md hover:bg-red-600">Rechazar</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay solicitudes pendientes.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Historial de Solicitudes -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium">Historial de Solicitudes (Últimas 50)</h3>
                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                             <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Empleado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Fecha</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Horas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($processedRequests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $request->user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($request->date)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $request->hours }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($request->status == 'approved')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aprobado</span>
                                            @elseif ($request->status == 'rejected')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rechazado</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay solicitudes procesadas en el historial.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

