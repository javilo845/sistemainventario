<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Empresa: ') }} {{ $empresa->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                <form method="POST" action="{{ route('empresas.update', $empresa) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nombre" :value="__('Nombre de la Empresa')" />
                        <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre', $empresa->nombre)" required autofocus />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="identificacion_fiscal" :value="__('RTN / Identificación Fiscal')" />
                        <x-text-input id="identificacion_fiscal" class="block mt-1 w-full" type="text" name="identificacion_fiscal" :value="old('identificacion_fiscal', $empresa->identificacion_fiscal)" required />
                        <x-input-error :messages="$errors->get('identificacion_fiscal')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="telefono" :value="__('Teléfono')" />
                            <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono', $empresa->telefono)" />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="correo" :value="__('Correo Electrónico')" />
                            <x-text-input id="correo" class="block mt-1 w-full" type="email" name="correo" :value="old('correo', $empresa->correo)" />
                            <x-input-error :messages="$errors->get('correo')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="direccion" :value="__('Dirección')" />
                        <x-text-input id="direccion" class="block mt-1 w-full" type="text" name="direccion" :value="old('direccion', $empresa->direccion)" />
                        <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="estado" :value="__('Estado')" />
                            <select id="estado" name="estado" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="activo" {{ old('estado', $empresa->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado', $empresa->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="logo" :value="__('Logo (dejar en blanco para conservar)')" />
                            @if($empresa->logo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $empresa->logo) }}" alt="Logo actual" class="w-12 h-12 rounded object-cover border">
                                </div>
                            @endif
                            <input id="logo" name="logo" type="file" accept="image/*" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('empresas.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                            Cancelar
                        </a>
                        <x-primary-button>
                            {{ __('Actualizar Empresa') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
