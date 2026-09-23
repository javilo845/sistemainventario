<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Nuevo Ítem en Catálogo') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-100">
                <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nombre" :value="__('Nombre del Ítem')" />
                            <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre')" placeholder="Ej. Papel Bond Carta 75g, Aceite de Motor..." required autofocus />
                            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="sku" :value="__('Código / SKU (Dejar vacío para autogenerar)')" />
                            <x-text-input id="sku" class="block mt-1 w-full font-mono uppercase" type="text" name="sku" :value="old('sku')" placeholder="Ej. PAP-001, REP-458" />
                            <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="categoria_id" :value="__('Categoría')" />
                            <select id="categoria_id" name="categoria_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">Selecciona una categoría</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('categoria_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="unidad_medida_id" :value="__('Unidad de Medida')" />
                            <select id="unidad_medida_id" name="unidad_medida_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">Selecciona unidad</option>
                                @foreach($unidades as $u)
                                    <option value="{{ $u->id }}" {{ old('unidad_medida_id') == $u->id ? 'selected' : '' }}>{{ $u->nombre }} ({{ $u->abreviatura }})</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('unidad_medida_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="proveedor_id" :value="__('Proveedor Habitual (Opcional)')" />
                            <select id="proveedor_id" name="proveedor_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">Sin proveedor asignado</option>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov->id }}" {{ old('proveedor_id') == $prov->id ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('proveedor_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="costo_unitario" :value="__('Costo Unitario ($)')" />
                            <x-text-input id="costo_unitario" class="block mt-1 w-full" type="number" step="0.01" min="0" name="costo_unitario" :value="old('costo_unitario', '0.00')" />
                            <x-input-error :messages="$errors->get('costo_unitario')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="stock_minimo" :value="__('Stock Mínimo (Alerta de reposición)')" />
                            <x-text-input id="stock_minimo" class="block mt-1 w-full" type="number" min="0" name="stock_minimo" :value="old('stock_minimo', '5')" required />
                            <p class="text-xs text-gray-500 mt-1">El sistema alertará en rojo/amarillo cuando el stock consolidado sea menor a este valor.</p>
                            <x-input-error :messages="$errors->get('stock_minimo')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="estado" :value="__('Estado')" />
                            <select id="estado" name="estado" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="descripcion" :value="__('Descripción o Especificaciones')" />
                            <textarea id="descripcion" name="descripcion" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Detalles técnicos, marca, compatibilidad...">{{ old('descripcion') }}</textarea>
                            <x-input-error :messages="$errors->get('descripcion')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="imagen" :value="__('Imagen del Ítem (Opcional)')" />
                            <input id="imagen" name="imagen" type="file" accept="image/*" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                            <x-input-error :messages="$errors->get('imagen')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t">
                        <a href="{{ route('items.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                            Cancelar
                        </a>
                        <x-primary-button>
                            {{ __('Guardar Ítem') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
