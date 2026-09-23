<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Bitácora de Movimientos de Inventario') }}
                </h2>
                <p class="text-xs text-gray-500">Historial oficial de trazabilidad inmutable y auditoría de existencias.</p>
            </div>
            <a href="{{ route('movimientos.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 transition">
                + Nuevo Movimiento
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <!-- Filtros -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('movimientos.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                    <div>
                        <select name="tipo" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos los tipos</option>
                            <option value="entrada" {{ request('tipo') === 'entrada' ? 'selected' : '' }}>Entrada</option>
                            <option value="salida" {{ request('tipo') === 'salida' ? 'selected' : '' }}>Salida</option>
                            <option value="traslado" {{ request('tipo') === 'traslado' ? 'selected' : '' }}>Traslado</option>
                            <option value="ajuste" {{ request('tipo') === 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                        </select>
                    </div>

                    <div>
                        <select name="item_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos los ítems</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->nombre }} ({{ $item->sku }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="area_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todas las áreas</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Desde">
                    </div>

                    <div class="flex gap-2">
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Hasta">
                        <button type="submit" class="px-3 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-700 transition">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabla de Bitácora -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs text-left">
                        <thead class="bg-gray-50 text-gray-600 uppercase font-semibold">
                            <tr>
                                <th class="px-5 py-3">Fecha y Hora</th>
                                <th class="px-5 py-3">Tipo</th>
                                <th class="px-5 py-3">Ítem / SKU</th>
                                <th class="px-5 py-3 text-right">Cantidad</th>
                                <th class="px-5 py-3">Área Origen</th>
                                <th class="px-5 py-3">Área Destino</th>
                                <th class="px-5 py-3">Ejecutado por</th>
                                <th class="px-5 py-3">Motivo / Trazabilidad</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($movimientos as $mov)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3.5 font-mono text-gray-500 whitespace-nowrap">
                                        {{ $mov->created_at->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-5 py-3.5 whitespace-nowrap">
                                        @php
                                            $tipoClasses = [
                                                'entrada' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                                'salida' => 'bg-red-100 text-red-800 border-red-200',
                                                'traslado' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                                                'ajuste' => 'bg-amber-100 text-amber-800 border-amber-200',
                                            ];
                                        @endphp
                                        <span class="inline-flex px-2 py-0.5 rounded border text-[11px] font-bold uppercase {{ $tipoClasses[$mov->tipo] ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $mov->tipo }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('items.show', $mov->item) }}" class="font-bold text-gray-900 hover:text-indigo-600 block">
                                            {{ $mov->item->nombre }}
                                        </a>
                                        <span class="font-mono text-gray-400 text-[10px]">{{ $mov->item->sku }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right font-bold text-sm text-gray-900 whitespace-nowrap">
                                        {{ number_format($mov->cantidad, 2) }} {{ $mov->item->unidadMedida ? $mov->item->unidadMedida->abreviatura : 'u' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-700 whitespace-nowrap">
                                        @if($mov->areaOrigen)
                                            <span class="font-medium">{{ $mov->areaOrigen->nombre }}</span>
                                            <span class="block text-[10px] text-gray-400">({{ $mov->areaOrigen->sucursal->nombre }})</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-700 whitespace-nowrap">
                                        @if($mov->areaDestino)
                                            <span class="font-medium text-indigo-900">{{ $mov->areaDestino->nombre }}</span>
                                            <span class="block text-[10px] text-gray-400">({{ $mov->areaDestino->sucursal->nombre }})</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-600 font-medium whitespace-nowrap">
                                        {{ $mov->usuario ? $mov->usuario->name : 'N/A' }}
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-600 max-w-xs truncate" title="{{ $mov->motivo }}">
                                        {{ $mov->motivo ?: 'Sin observación' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                                        No se han registrado movimientos de inventario con los filtros seleccionados.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($movimientos->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $movimientos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
