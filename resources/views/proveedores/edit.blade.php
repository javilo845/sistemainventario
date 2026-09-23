<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Proveedor: ') }} {{ $proveedor->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                <form method="POST" action="{{ route('proveedores.update', $proveedor) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nombre" :value="__('Nombre de la Empresa Proveedora')" />
                        <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre', $proveedor->nombre)" required autofocus />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="contacto" :value="__('Persona de Contacto')" />
                        <x-text-input id="contacto" class="block mt-1 w-full" type="text" name="contacto" :value="old('contacto', $proveedor->contacto)" />
                        <x-input-error :messages="$errors->get('contacto')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="telefono" :value="__('Teléfono')" />
                            <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono', $proveedor->telefono)" />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="correo" :value="__('Correo Electrónico')" />
                            <x-text-input id="correo" class="block mt-1 w-full" type="email" name="correo" :value="old('correo', $proveedor->correo)" />
                            <x-input-error :messages="$errors->get('correo')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('proveedores.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                            Cancelar
                        </a>
                        <x-primary-button>
                            {{ __('Actualizar Proveedor') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
