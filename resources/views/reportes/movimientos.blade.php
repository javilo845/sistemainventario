<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Reporte de Movimientos de Inventario') }}
                </h2>
                <p class="text-xs text-gray-500">Auditoría detallada con filtros por fechas, tipo y actores.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reportes.movimientos.excel', request()->all()) }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white rounded-md text-xs font-semibold hover:bg-emerald-700 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Exportar Excel
                </a>
                <a href="{{ route('reportes.movimientos.pdf', request()->all()) }}" class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-md text-xs font-semibold hover:bg-red-700 transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Exportar PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <!-- Barra de pestañas y Filtros -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 space-y-4">
                <div class="flex border-b pb-2 gap-4 text-sm font-semibold">
                    <a href="{{ route('reportes.inventario') }}" class="text-gray-500 hover:text-gray-700 pb-1">Reporte de Inventario</a>
                    <a href="{{ route('reportes.movimientos') }}" class="text-indigo-600 border-b-2 border-indigo-600 pb-1">Reporte de Movimientos</a>
                </div>

                <form method="GET" action="{{ route('reportes.movimientos') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
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
                                <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-700">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabla de datos -->
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
                                <th class="px-5 py-3">Usuario</th>
                                <th class="px-5 py-3">Motivo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($movimientos as $mov)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-5 py-3 font-mono text-gray-500 whitespace-nowrap">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-5 py-3 uppercase font-bold text-[10px]">{{ $mov->tipo }}</td>
                                    <td class="px-5 py-3 font-bold text-gray-900">{{ $mov->item ? $mov->item->nombre : 'N/A' }}</td>
                                    <td class="px-5 py-3 text-right font-bold text-sm text-gray-900 font-mono">{{ number_format($mov->cantidad, 2) }}</td>
                                    <td class="px-5 py-3 text-gray-700">{{ $mov->areaOrigen ? $mov->areaOrigen->nombre : '-' }}</td>
                                    <td class="px-5 py-3 text-gray-700">{{ $mov->areaDestino ? $mov->areaDestino->nombre : '-' }}</td>
                                    <td class="px-5 py-3 text-gray-600">{{ $mov->usuario ? $mov->usuario->name : 'N/A' }}</td>
                                    <td class="px-5 py-3 text-gray-500 italic max-w-xs truncate">{{ $mov->motivo ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-400">No se encontraron movimientos registrados.</td>
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
