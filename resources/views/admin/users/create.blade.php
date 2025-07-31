<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Crear Nuevo Usuario
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Complete los datos para crear una nueva cuenta. La contraseña inicial se establecerá como <span
                            class="font-mono bg-gray-200 dark:bg-gray-700 px-1 rounded">password</span>. El usuario
                        deberá cambiarla en su primer inicio de sesión.
                    </p>

                    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Nombre -->
                        <div>
                            <x-input-label for="name" value="Nombre Completo" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name')" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <!-- Correo Electrónico -->
                        <div>
                            <x-input-label for="email" value="Correo Electrónico" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email')" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <!-- Rol -->
                        <div>
                            <x-input-label for="role" value="Rol del Usuario" />
                            <select name="role" id="role"
                                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                                required>
                                <option value="user">Usuario</option>
                                <option value="admin">Administrador</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('role')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>
                                {{ __('Crear Usuario') }}
                            </x-primary-button>
                            <a href="{{ route('admin.users.index') }}"
                                class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
