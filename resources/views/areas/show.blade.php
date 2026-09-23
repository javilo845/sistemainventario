<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $area->nombre }}
            </h2>
            <div class="space-x-2">
                @can('update', $area)
                <a href="{{ route('areas.edit', $area) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition">
                    Editar Área
                </a>
                @endcan
                <a href="{{ route('areas.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Ficha informativa -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-sm">
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Sucursal</span>
                        <span class="text-gray-800 font-medium">{{ $area->sucursal ? $area->sucursal->nombre : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Empresa</span>
                        <span class="text-gray-800 font-medium">{{ $area->sucursal && $area->sucursal->empresa ? $area->sucursal->empresa->nombre : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Encargado Responsable</span>
                        @if($area->encargado)
                            <div class="font-medium text-gray-900">{{ $area->encargado->name }}</div>
                            <div class="text-xs text-gray-400">{{ $area->encargado->email }}</div>
                        @else
                            <span class="text-amber-600 italic">Sin encargado asignado</span>
                        @endif
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Estado</span>
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $area->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($area->estado) }}
                        </span>
                    </div>
                    @if($area->descripcion)
                        <div class="md:col-span-4 border-t pt-3">
                            <span class="block text-gray-400 text-xs uppercase font-semibold mb-1">Descripción</span>
                            <p class="text-gray-700">{{ $area->descripcion }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Stock de inventario en esta área -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Stock Actual en esta Área</h3>
                        <p class="text-xs text-gray-500">Ítems con existencia física en {{ $area->nombre }} bajo custodia de {{ $area->encargado ? $area->encargado->name : 'Nadie' }}</p>
                    </div>
                    @if(Route::has('movimientos.traslado.create'))
                    <a href="{{ route('movimientos.traslado.create', ['area_origen_id' => $area->id]) }}" class="px-3 py-1.5 bg-purple-600 text-white rounded text-xs font-semibold hover:bg-purple-700 transition">
                        Trasladar desde aquí
                    </a>
                    @endif
                </div>

                @if($area->inventarios->isEmpty())
                    <p class="text-gray-400 text-sm py-4 text-center">No hay ítems registrados en el inventario de esta área.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3">Código/SKU</th>
                                    <th class="px-4 py-3">Ítem</th>
                                    <th class="px-4 py-3">Categoría</th>
                                    <th class="px-4 py-3 text-right">Cantidad Física</th>
                                    <th class="px-4 py-3 text-center">Unidad</th>
                                    <th class="px-4 py-3 text-center">Estado de Stock</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($area->inventarios as $inv)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $inv->item->sku }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $inv->item->nombre }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $inv->item->categoria ? $inv->item->categoria->nombre : 'N/A' }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-base {{ $inv->cantidad <= 0 ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ number_format($inv->cantidad, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $inv->item->unidadMedida ? $inv->item->unidadMedida->abreviatura : 'u' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($inv->cantidad <= 0)
                                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700">Agotado</span>
                                            @elseif($inv->cantidad < $inv->item->stock_minimo)
                                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-amber-100 text-amber-700">Cerca del Mínimo</span>
                                            @else
                                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-700">Normal</span>
                                            @endif
                                        </td>
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
