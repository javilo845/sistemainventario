<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $item->nombre }}
                </h2>
                <span class="font-mono text-xs px-2.5 py-1 bg-gray-200 text-gray-700 rounded-md font-semibold">
                    {{ $item->sku }}
                </span>
            </div>
            <div class="space-x-2">
                @can('update', $item)
                <a href="{{ route('items.edit', $item) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition">
                    Editar Ítem
                </a>
                @endcan
                <a href="{{ route('items.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition">
                    Volver al Catálogo
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Ficha general del ítem -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col md:flex-row gap-6 items-start">
                @if($item->imagen)
                    <img src="{{ asset('storage/' . $item->imagen) }}" alt="{{ $item->nombre }}" class="w-32 h-32 rounded-lg object-cover border shadow-sm">
                @else
                    <div class="w-32 h-32 rounded-lg bg-gray-100 text-gray-400 flex flex-col items-center justify-center border font-mono">
                        <svg class="w-8 h-8 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span class="text-xs">Sin imagen</span>
                    </div>
                @endif

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 flex-1 text-sm">
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Categoría</span>
                        <span class="font-medium text-gray-800">{{ $item->categoria ? $item->categoria->nombre : 'Sin categoría' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Unidad de Medida</span>
                        <span class="font-medium text-gray-800">{{ $item->unidadMedida ? $item->unidadMedida->nombre : 'Unidad' }} ({{ $item->unidadMedida ? $item->unidadMedida->abreviatura : 'u' }})</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Costo Unitario</span>
                        <span class="font-medium text-gray-800">${{ number_format($item->costo_unitario, 2) }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Proveedor Habitual</span>
                        <span class="font-medium text-gray-800">{{ $item->proveedor ? $item->proveedor->nombre : 'Sin proveedor' }}</span>
                    </div>

                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Stock Mínimo de Alerta</span>
                        <span class="font-bold text-amber-600">{{ $item->stock_minimo }} {{ $item->unidadMedida ? $item->unidadMedida->abreviatura : '' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Stock Físico Total</span>
                        @php $stockTotal = $item->stock_total; @endphp
                        <span class="font-bold text-base {{ $stockTotal < $item->stock_minimo ? 'text-red-600' : 'text-emerald-600' }}">
                            {{ number_format($stockTotal, 2) }} {{ $item->unidadMedida ? $item->unidadMedida->abreviatura : '' }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Estado en Catálogo</span>
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $item->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($item->estado) }}
                        </span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Valorización en Stock</span>
                        <span class="font-bold text-gray-900">${{ number_format($stockTotal * $item->costo_unitario, 2) }}</span>
                    </div>

                    @if($item->descripcion)
                        <div class="col-span-2 md:col-span-4 border-t pt-3">
                            <span class="block text-gray-400 text-xs uppercase font-semibold mb-1">Descripción</span>
                            <p class="text-gray-700">{{ $item->descripcion }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Desglose de Stock por Área -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Distribución de Stock por Área</h3>
                        <p class="text-xs text-gray-500">Ubicación física exacta de las existencias y encargado responsable asignado.</p>
                    </div>
                    <div class="flex gap-2">
                        @if(Route::has('movimientos.create'))
                        <a href="{{ route('movimientos.create', ['tipo' => 'entrada', 'item_id' => $item->id]) }}" class="px-3 py-1.5 bg-emerald-600 text-white rounded text-xs font-semibold hover:bg-emerald-700 transition">
                            + Entrada de Stock
                        </a>
                        <a href="{{ route('movimientos.create', ['tipo' => 'traslado', 'item_id' => $item->id]) }}" class="px-3 py-1.5 bg-indigo-600 text-white rounded text-xs font-semibold hover:bg-indigo-700 transition">
                            ↔ Trasladar Stock
                        </a>
                        @endif
                    </div>
                </div>

                @if($item->inventarios->isEmpty() || $item->inventarios->where('cantidad', '>', 0)->isEmpty())
                    <div class="p-6 text-center text-gray-400 border rounded-lg bg-gray-50 text-sm">
                        Actualmente no hay existencias físicas registradas en ninguna área para este ítem.
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                                <tr>
                                    <th class="px-4 py-3">Sucursal</th>
                                    <th class="px-4 py-3">Área de Ubicación</th>
                                    <th class="px-4 py-3">Encargado Responsable</th>
                                    <th class="px-4 py-3 text-right">Cantidad Física</th>
                                    <th class="px-4 py-3 text-center">Unidad</th>
                                    <th class="px-4 py-3 text-right">Valorización</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($item->inventarios as $inv)
                                    @if($inv->cantidad > 0)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $inv->area->sucursal->nombre }}</td>
                                            <td class="px-4 py-3 text-gray-700 font-semibold">{{ $inv->area->nombre }}</td>
                                            <td class="px-4 py-3 text-gray-700">
                                                @if($inv->area->encargado)
                                                    <span class="inline-flex items-center gap-1.5 font-medium">
                                                        <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                                        {{ $inv->area->encargado->name }}
                                                    </span>
                                                @else
                                                    <span class="text-xs text-amber-600 italic">Sin encargado</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold text-base text-gray-900">
                                                {{ number_format($inv->cantidad, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-gray-500">{{ $item->unidadMedida ? $item->unidadMedida->abreviatura : 'u' }}</td>
                                            <td class="px-4 py-3 text-right text-gray-700 font-medium">
                                                ${{ number_format($inv->cantidad * $item->costo_unitario, 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Últimos Movimientos del Ítem -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-3">Historial de Movimientos de este Ítem</h3>
                @if($item->movimientos->isEmpty())
                    <p class="text-gray-400 text-sm py-4">No hay movimientos registrados para este ítem.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs text-left">
                            <thead class="bg-gray-50 text-gray-600 uppercase font-semibold">
                                <tr>
                                    <th class="px-4 py-2.5">Fecha y Hora</th>
                                    <th class="px-4 py-2.5">Tipo</th>
                                    <th class="px-4 py-2.5 text-right">Cantidad</th>
                                    <th class="px-4 py-2.5">Origen</th>
                                    <th class="px-4 py-2.5">Destino</th>
                                    <th class="px-4 py-2.5">Ejecutado por</th>
                                    <th class="px-4 py-2.5">Motivo / Observación</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($item->movimientos as $mov)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2.5 font-mono text-gray-500">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-4 py-2.5">
                                            @php
                                                $badgeColors = [
                                                    'entrada' => 'bg-emerald-100 text-emerald-800',
                                                    'salida' => 'bg-red-100 text-red-800',
                                                    'traslado' => 'bg-indigo-100 text-indigo-800',
                                                    'ajuste' => 'bg-amber-100 text-amber-800',
                                                ];
                                            @endphp
                                            <span class="inline-flex px-2 py-0.5 rounded uppercase font-bold text-[10px] {{ $badgeColors[$mov->tipo] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ $mov->tipo }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2.5 text-right font-bold text-gray-900">{{ number_format($mov->cantidad, 2) }}</td>
                                        <td class="px-4 py-2.5 text-gray-600">{{ $mov->areaOrigen ? $mov->areaOrigen->nombre : '-' }}</td>
                                        <td class="px-4 py-2.5 text-gray-600">{{ $mov->areaDestino ? $mov->areaDestino->nombre : '-' }}</td>
                                        <td class="px-4 py-2.5 text-gray-600 font-medium">{{ $mov->usuario ? $mov->usuario->name : 'N/A' }}</td>
                                        <td class="px-4 py-2.5 text-gray-500 italic truncate max-w-xs">{{ $mov->motivo ?: 'Sin observación' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
