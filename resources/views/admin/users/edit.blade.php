<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Usuario: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Nombre -->
                        <div>
                            <label for="name" class="block text-sm font-medium">Nombre Completo</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm"
                                required>
                        </div>

                        <!-- Correo -->
                        <div>
                            <label for="email" class="block text-sm font-medium">Correo Electrónico</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm"
                                required>
                        </div>

                        <!-- Rol -->
                        <div>
                            <label for="role" class="block text-sm font-medium">Rol</label>
                            <select name="role" id="role"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm"
                                required>
                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>Usuario</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrador
                                </option>
                            </select>
                        </div>

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Deja los siguientes campos en blanco si
                                no deseas cambiar la contraseña.</p>
                        </div>

                        <!-- Contraseña -->
                        <div>
                            <label for="password" class="block text-sm font-medium">Nueva Contraseña</label>
                            <input type="password" name="password" id="password"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm">
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium">Confirmar Nueva
                                Contraseña</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 shadow-sm">
                        </div>

                        <div class="flex items-center gap-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">Guardar
                                Cambios</button>
                            <a href="{{ route('admin.users.index') }}"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancelar</a>
                        </div>
                    </form>
                    <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-6">
                        <h3 class="text-lg font-medium text-red-600 dark:text-red-400">Zona de Peligro</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Una vez que la cuenta sea eliminada, todos sus datos y registros de asistencia se borrarán
                            permanentemente. Esta acción no se puede deshacer.
                        </p>

                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-4">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                onclick="return confirm('¿Estás seguro de que quieres eliminar a este usuario? Esta acción es irreversible.')"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                                Eliminar Usuario Permanentemente
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
