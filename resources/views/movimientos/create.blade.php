<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registrar Movimiento de Inventario') }}
        </h2>
    </x-slot>

    <div class="py-6" x-data="{
        tab: '{{ $tipo ?? 'traslado' }}',
        itemId: '{{ $selectedItemId ?? '' }}',
        areaOrigenId: '{{ $selectedAreaOrigenId ?? '' }}',
        stockDisponible: null,
        loadingStock: false,
        consultarStock(item, area) {
            if (!item || !area) {
                this.stockDisponible = null;
                return;
            }
            this.loadingStock = true;
            fetch(`/movimientos/consultar-stock?item_id=${item}&area_id=${area}`)
                .then(res => res.json())
                .then(data => {
                    this.stockDisponible = data.stock_disponible;
                    this.loadingStock = false;
                })
                .catch(() => {
                    this.loadingStock = false;
                });
        }
    }" x-init="if (itemId && areaOrigenId) { consultarStock(itemId, areaOrigenId); }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Pestañas de tipo de movimiento -->
            <div class="flex border-b border-gray-200 bg-white rounded-t-lg px-4 pt-2">
                <button type="button" @click="tab = 'traslado'" :class="tab === 'traslado' ? 'border-indigo-600 text-indigo-600 border-b-2 font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-3 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Traslado entre Áreas
                </button>
                <button type="button" @click="tab = 'entrada'" :class="tab === 'entrada' ? 'border-emerald-600 text-emerald-600 border-b-2 font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-3 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Entrada (Nuevo Stock)
                </button>
                <button type="button" @click="tab = 'salida'" :class="tab === 'salida' ? 'border-red-600 text-red-600 border-b-2 font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-3 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                    Salida / Despacho
                </button>
                <button type="button" @click="tab = 'ajuste'" :class="tab === 'ajuste' ? 'border-amber-600 text-amber-600 border-b-2 font-bold' : 'text-gray-500 hover:text-gray-700'" class="px-5 py-3 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Ajuste Manual
                </button>
            </div>

            <!-- 1. FORMULARIO TRASLADO -->
            <div x-show="tab === 'traslado'" class="bg-white p-6 rounded-b-lg shadow-sm border border-gray-100">
                <div class="mb-4 bg-indigo-50 border border-indigo-100 rounded-lg p-3 text-xs text-indigo-800">
                    <strong>Regla de negocio:</strong> Al trasladar inventario a otra área, el responsable pasa a ser automáticamente el encargado de dicha área de destino. Se valida que el área origen tenga saldo suficiente.
                </div>

                <form method="POST" action="{{ route('movimientos.traslado') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="t_item_id" :value="__('Ítem a Trasladar')" />
                            <select id="t_item_id" name="item_id" x-model="itemId" @change="consultarStock(itemId, areaOrigenId)" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">Selecciona el ítem</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ ($selectedItemId ?? old('item_id')) == $item->id ? 'selected' : '' }}>
                                        {{ $item->nombre }} ({{ $item->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="t_area_origen_id" :value="__('Área de Origen (De donde sale)')" />
                            <select id="t_area_origen_id" name="area_origen_id" x-model="areaOrigenId" @change="consultarStock(itemId, areaOrigenId)" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">Selecciona el área de origen</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ ($selectedAreaOrigenId ?? old('area_origen_id')) == $area->id ? 'selected' : '' }}>
                                        {{ $area->nombre }} ({{ $area->sucursal->nombre }})
                                    </option>
                                @endforeach
                            </select>

                            <!-- Indicador en tiempo real de stock disponible -->
                            <div class="mt-2 text-xs">
                                <span x-show="loadingStock" class="text-gray-400">Consultando stock disponible...</span>
                                <template x-if="stockDisponible !== null && !loadingStock">
                                    <div class="p-2 rounded bg-gray-50 border flex justify-between items-center">
                                        <span class="text-gray-600">Stock disponible en origen:</span>
                                        <span class="font-bold text-sm" :class="stockDisponible > 0 ? 'text-emerald-600' : 'text-red-600'" x-text="stockDisponible.toFixed(2)"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="t_area_destino_id" :value="__('Área de Destino (Hacia donde va)')" />
                            <select id="t_area_destino_id" name="area_destino_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">Selecciona el área de destino</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('area_destino_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->nombre }} ({{ $area->sucursal->nombre }}) - Custodia: {{ $area->encargado ? $area->encargado->name : 'Sin asignar' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="t_cantidad" :value="__('Cantidad a Trasladar')" />
                            <x-text-input id="t_cantidad" class="block mt-1 w-full" type="number" step="0.01" min="0.01" name="cantidad" :value="old('cantidad')" required placeholder="0.00" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="t_motivo" :value="__('Motivo / Observación del Traslado')" />
                        <textarea id="t_motivo" name="motivo" rows="2" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Ej. Reabastecimiento de insumos para cocina...">{{ old('motivo') }}</textarea>
                    </div>

                    <div class="flex justify-end pt-4 border-t">
                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-700">
                            {{ __('Confirmar y Ejecutar Traslado') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <!-- 2. FORMULARIO ENTRADA -->
            <div x-show="tab === 'entrada'" class="bg-white p-6 rounded-b-lg shadow-sm border border-gray-100">
                <form method="POST" action="{{ route('movimientos.entrada') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="e_item_id" :value="__('Ítem a Ingresar')" />
                            <select id="e_item_id" name="item_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">Selecciona el ítem</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nombre }} ({{ $item->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="e_area_destino_id" :value="__('Área de Destino (Donde se ingresa)')" />
                            <select id="e_area_destino_id" name="area_destino_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">Selecciona el área de recepción</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('area_destino_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->nombre }} ({{ $area->sucursal->nombre }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="e_cantidad" :value="__('Cantidad a Ingresar')" />
                            <x-text-input id="e_cantidad" class="block mt-1 w-full" type="number" step="0.01" min="0.01" name="cantidad" :value="old('cantidad')" required placeholder="0.00" />
                        </div>

                        <div>
                            <x-input-label for="e_motivo" :value="__('Motivo (Ej. Factura de compra, donación, etc.)')" />
                            <x-text-input id="e_motivo" class="block mt-1 w-full" type="text" name="motivo" :value="old('motivo')" placeholder="Compra a proveedor según factura..." />
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-md text-sm font-semibold hover:bg-emerald-700 transition">
                            Registrar Entrada de Stock
                        </button>
                    </div>
                </form>
            </div>

            <!-- 3. FORMULARIO SALIDA -->
            <div x-show="tab === 'salida'" class="bg-white p-6 rounded-b-lg shadow-sm border border-gray-100">
                <form method="POST" action="{{ route('movimientos.salida') }}" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="s_item_id" :value="__('Ítem a Descargar')" />
                            <select id="s_item_id" name="item_id" x-model="itemId" @change="consultarStock(itemId, areaOrigenId)" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">Selecciona el ítem</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nombre }} ({{ $item->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="s_area_origen_id" :value="__('Área de Origen')" />
                            <select id="s_area_origen_id" name="area_origen_id" x-model="areaOrigenId" @change="consultarStock(itemId, areaOrigenId)" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">Selecciona el área de egreso</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('area_origen_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->nombre }} ({{ $area->sucursal->nombre }})
                                    </option>
                                @endforeach
                            </select>

                            <template x-if="stockDisponible !== null && !loadingStock">
                                <div class="mt-2 text-xs p-2 rounded bg-gray-50 border flex justify-between">
                                    <span class="text-gray-600">Stock disponible:</span>
                                    <span class="font-bold" :class="stockDisponible > 0 ? 'text-emerald-600' : 'text-red-600'" x-text="stockDisponible.toFixed(2)"></span>
                                </div>
                            </template>
                        </div>

                        <div>
                            <x-input-label for="s_cantidad" :value="__('Cantidad a Descargar')" />
                            <x-text-input id="s_cantidad" class="block mt-1 w-full" type="number" step="0.01" min="0.01" name="cantidad" :value="old('cantidad')" required placeholder="0.00" />
                        </div>

                        <div>
                            <x-input-label for="s_motivo" :value="__('Motivo de Salida (Venta, Consumo, Merma, etc.)')" />
                            <x-text-input id="s_motivo" class="block mt-1 w-full" type="text" name="motivo" :value="old('motivo')" placeholder="Consumo de operaciones diarias..." required />
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 border-t">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm font-semibold hover:bg-red-700 transition">
                            Procesar Salida de Stock
                        </button>
                    </div>
                </form>
            </div>

            <!-- 4. FORMULARIO AJUSTE -->
            <div x-show="tab === 'ajuste'" class="bg-white p-6 rounded-b-lg shadow-sm border border-gray-100">
                <form method="POST" action="{{ route('movimientos.ajuste') }}" class="space-y-4">
                    @csrf
                    <div class="mb-3 bg-amber-50 border border-amber-200 rounded p-3 text-xs text-amber-800">
                        <strong>Atención:</strong> Ingrese una cantidad positiva (ej. <code>+5</code>) si encontró más stock del registrado en sistema, o negativa (ej. <code>-2</code>) si hubo faltante. El motivo es <strong>obligatorio</strong>.
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="a_item_id" :value="__('Ítem')" />
                            <select id="a_item_id" name="item_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">Selecciona el ítem</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nombre }} ({{ $item->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="a_area_id" :value="__('Área')" />
                            <select id="a_area_id" name="area_id" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                                <option value="">Selecciona el área</option>
                                @foreach($areas as $area)
                                    <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                        {{ $area->nombre }} ({{ $area->sucursal->nombre }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="a_cantidad" :value="__('Diferencia de Ajuste (+ / -)')" />
                            <x-text-input id="a_cantidad" class="block mt-1 w-full" type="number" step="0.01" name="cantidad" :value="old('cantidad')" required placeholder="Ej: +3 o -2" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="a_motivo" :value="__('Motivo Obligatorio del Ajuste')" />
                        <textarea id="a_motivo" name="motivo" rows="2" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Explicación detallada de la discrepancia encontrada durante la auditoría..." required>{{ old('motivo') }}</textarea>
                    </div>

                    <div class="flex justify-end pt-4 border-t">
                        <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded-md text-sm font-semibold hover:bg-amber-700 transition">
                            Registrar Ajuste en Bitácora
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
