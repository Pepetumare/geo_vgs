<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Nuevo Proveedor
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('admin.providers.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required
                                autofocus value="{{ old('name') }}" />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contact_name" value="Nombre del contacto" />
                            <x-text-input id="contact_name" name="contact_name" type="text" class="mt-1 block w-full"
                                value="{{ old('contact_name') }}" />
                            <x-input-error :messages="$errors->get('contact_name')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="email" value="Correo" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                    value="{{ old('email') }}" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="phone" value="Teléfono" />
                                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full"
                                    value="{{ old('phone') }}" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="address" value="Dirección" />
                            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full"
                                value="{{ old('address') }}" />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="notes" value="Notas" />
                            <textarea id="notes" name="notes" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:border-gray-700">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('admin.providers.index') }}"
                                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                                Cancelar
                            </a>
                            <x-primary-button>
                                Guardar
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
