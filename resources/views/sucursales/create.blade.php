<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Sucursal') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                <form method="POST" action="{{ route('sucursales.store') }}" class="space-y-4">
                    @csrf

                    @if(auth()->user()->hasRole('Super Administrador'))
                        <div>
                            <x-input-label for="empresa_id" :value="__('Empresa')" />
                            <select id="empresa_id" name="empresa_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">Selecciona una empresa</option>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}" {{ old('empresa_id', request('empresa_id')) == $empresa->id ? 'selected' : '' }}>{{ $empresa->nombre }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('empresa_id')" class="mt-2" />
                        </div>
                    @endif

                    <div>
                        <x-input-label for="nombre" :value="__('Nombre de la Sucursal')" />
                        <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" placeholder="Ej. Sucursal Central, Sucursal Norte..." required autofocus />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="direccion" :value="__('Dirección')" />
                        <x-text-input id="direccion" class="block mt-1 w-full" type="text" name="direccion" :value="old('direccion')" placeholder="Calle, número, ciudad..." />
                        <x-input-error :messages="$errors->get('direccion')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="telefono" :value="__('Teléfono')" />
                            <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono')" />
                            <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="estado" :value="__('Estado')" />
                            <select id="estado" name="estado" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('sucursales.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                            Cancelar
                        </a>
                        <x-primary-button>
                            {{ __('Guardar Sucursal') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
