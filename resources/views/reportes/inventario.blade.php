<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Reporte de Inventario Físico Actual') }}
                </h2>
                <p class="text-xs text-gray-500">Consulta y exportación consolidada de stock por área y categoría.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reportes.inventario.excel', request()->all()) }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white rounded-md text-xs font-semibold hover:bg-emerald-700 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar Excel
                </a>
                <a href="{{ route('reportes.inventario.pdf', request()->all()) }}" class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-700 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Exportar PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <!-- Barra de navegación de reportes y Filtros -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 space-y-4">
                <div class="flex border-b pb-2 gap-4 text-sm font-semibold">
                    <a href="{{ route('reportes.inventario') }}" class="text-indigo-600 border-b-2 border-indigo-600 pb-1">Reporte de Inventario</a>
                    <a href="{{ route('reportes.movimientos') }}" class="text-gray-500 hover:text-gray-700 pb-1">Reporte de Movimientos</a>
                </div>

                <form method="GET" action="{{ route('reportes.inventario') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <select name="sucursal_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todas las sucursales</option>
                            @foreach($sucursales as $suc)
                                <option value="{{ $suc->id }}" {{ request('sucursal_id') == $suc->id ? 'selected' : '' }}>{{ $suc->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="area_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todas las áreas</option>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->nombre }} ({{ $area->sucursal->nombre }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <select name="categoria_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todas las categorías</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-700">
                            Aplicar Filtros
                        </button>
                        @if(request()->anyFilled(['sucursal_id', 'area_id', 'categoria_id']))
                            <a href="{{ route('reportes.inventario') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md text-sm font-medium">Limpiar</a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Resumen de Valorización -->
            <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 flex justify-between items-center text-sm">
                <span class="text-indigo-900 font-medium">Valorización total calculada para esta consulta:</span>
                <span class="text-xl font-bold text-indigo-900 font-mono">${{ number_format($totalValor, 2) }}</span>
            </div>

            <!-- Tabla de datos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs text-left">
                        <thead class="bg-gray-50 text-gray-600 uppercase font-semibold">
                            <tr>
                                <th class="px-5 py-3">Código/SKU</th>
                                <th class="px-5 py-3">Ítem</th>
                                <th class="px-5 py-3">Categoría</th>
                                <th class="px-5 py-3">Sucursal</th>
                                <th class="px-5 py-3">Área de Ubicación</th>
                                <th class="px-5 py-3">Responsable</th>
                                <th class="px-5 py-3 text-right">Cantidad Física</th>
                                <th class="px-5 py-3 text-right">Costo Unit.</th>
                                <th class="px-5 py-3 text-right">Valor Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($inventarios as $inv)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 font-mono text-gray-500">{{ $inv->item ? $inv->item->sku : '-' }}</td>
                                    <td class="px-5 py-3 font-bold text-gray-900">
                                        <a href="{{ route('items.show', $inv->item_id) }}" class="hover:text-indigo-600">
                                            {{ $inv->item ? $inv->item->nombre : '-' }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600">{{ $inv->item && $inv->item->categoria ? $inv->item->categoria->nombre : '-' }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $inv->area && $inv->area->sucursal ? $inv->area->sucursal->nombre : '-' }}</td>
                                    <td class="px-5 py-3 font-medium text-gray-800">{{ $inv->area ? $inv->area->nombre : '-' }}</td>
                                    <td class="px-5 py-3 text-gray-600">
                                        {{ $inv->area && $inv->area->encargado ? $inv->area->encargado->name : 'Sin asignar' }}
                                    </td>
                                    <td class="px-5 py-3 text-right font-bold text-sm text-gray-900 font-mono">
                                        {{ number_format($inv->cantidad, 2) }} {{ $inv->item && $inv->item->unidadMedida ? $inv->item->unidadMedida->abreviatura : 'u' }}
                                    </td>
                                    <td class="px-5 py-3 text-right text-gray-600">${{ number_format($inv->item ? $inv->item->costo_unitario : 0, 2) }}</td>
                                    <td class="px-5 py-3 text-right font-bold text-gray-900 font-mono">
                                        ${{ number_format($inv->cantidad * ($inv->item ? $inv->item->costo_unitario : 0), 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-8 text-center text-gray-400">
                                        No hay registros de inventario que coincidan con la búsqueda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($inventarios->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $inventarios->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
