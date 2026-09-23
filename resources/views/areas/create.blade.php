<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nueva Área') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                <form method="POST" action="{{ route('areas.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="sucursal_id" :value="__('Sucursal')" />
                        <select id="sucursal_id" name="sucursal_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                            <option value="">Selecciona la sucursal de pertenencia</option>
                            @foreach($sucursales as $sucursal)
                                <option value="{{ $sucursal->id }}" {{ old('sucursal_id', request('sucursal_id')) == $sucursal->id ? 'selected' : '' }}>
                                    {{ $sucursal->nombre }} ({{ $sucursal->empresa ? $sucursal->empresa->nombre : '' }})
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('sucursal_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="nombre" :value="__('Nombre del Área')" />
                        <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" placeholder="Ej. Bodega Principal, Recepción, Cocina, Despacho..." required autofocus />
                        <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="descripcion" :value="__('Descripción')" />
                        <textarea id="descripcion" name="descripcion" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Detalles de ubicación o función del área...">{{ old('descripcion') }}</textarea>
                        <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="encargado_id" :value="__('Encargado Responsable')" />
                        <select id="encargado_id" name="encargado_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">Sin encargado asignado</option>
                            @foreach($usuarios as $user)
                                <option value="{{ $user->id }}" {{ old('encargado_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">El área solo puede tener un único encargado activo a la vez. Al realizar traslados a esta área, el inventario quedará bajo su custodia.</p>
                        <x-input-error :messages="$errors->get('encargado_id')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="estado" :value="__('Estado')" />
                        <select id="estado" name="estado" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('areas.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                            Cancelar
                        </a>
                        <x-primary-button>
                            {{ __('Guardar Área') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
