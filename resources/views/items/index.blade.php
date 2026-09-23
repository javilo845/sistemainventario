<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Catálogo Maestro de Ítems') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('categorias.index') }}" class="px-3 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                    Categorías
                </a>
                <a href="{{ route('proveedores.index') }}" class="px-3 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md text-sm font-medium transition">
                    Proveedores
                </a>
                @can('create', App\Models\Item::class)
                <a href="{{ route('items.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nuevo Ítem
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <!-- Filtros -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('items.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre o SKU..." class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    @if($categorias->isNotEmpty())
                        <div class="w-full sm:w-64">
                            <select name="categoria_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todas las categorías</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="w-full sm:w-48">
                        <select name="estado" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Todos los estados</option>
                            <option value="activo" {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-700 transition">
                        Filtrar
                    </button>
                    @if(request()->anyFilled(['buscar', 'categoria_id', 'estado']))
                        <a href="{{ route('items.index') }}" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium text-center transition">
                            Limpiar
                        </a>
                    @endif
                </form>
            </div>

            <!-- Tabla -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                            <tr>
                                <th class="px-6 py-3">Ítem / SKU</th>
                                <th class="px-6 py-3">Categoría</th>
                                <th class="px-6 py-3 text-right">Costo Unit.</th>
                                <th class="px-6 py-3 text-center">Stock Mín.</th>
                                <th class="px-6 py-3 text-center">Stock Físico Total</th>
                                <th class="px-6 py-3 text-center">Estado</th>
                                <th class="px-6 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($items as $item)
                                @php
                                    $stockTotal = $item->stock_total;
                                    $minimo = $item->stock_minimo;
                                    $isAlert = $stockTotal < $minimo;
                                @endphp
                                <tr class="hover:bg-gray-50 transition {{ $isAlert ? 'bg-red-50/30' : '' }}">
                                    <td class="px-6 py-4 flex items-center gap-3">
                                        @if($item->imagen)
                                            <img src="{{ asset('storage/' . $item->imagen) }}" alt="Foto" class="w-10 h-10 rounded object-cover border">
                                        @else
                                            <div class="w-10 h-10 rounded bg-gray-100 text-gray-500 flex items-center justify-center font-mono font-bold text-xs uppercase border">
                                                {{ substr($item->sku, 0, 3) }}
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('items.show', $item) }}" class="font-bold text-gray-900 hover:text-indigo-600">
                                                {{ $item->nombre }}
                                            </a>
                                            <div class="text-xs font-mono text-gray-500">{{ $item->sku }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $item->categoria ? $item->categoria->nombre : 'Sin categoría' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-medium text-gray-700">
                                        ${{ number_format($item->costo_unitario, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium text-gray-500">
                                        {{ $item->stock_minimo }} {{ $item->unidadMedida ? $item->unidadMedida->abreviatura : '' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $stockTotal <= 0 ? 'bg-red-100 text-red-800 border border-red-200' : ($stockTotal < $minimo ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200') }}">
                                            <span class="w-2 h-2 rounded-full {{ $stockTotal <= 0 ? 'bg-red-500' : ($stockTotal < $minimo ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500') }}"></span>
                                            {{ number_format($stockTotal, 2) }} {{ $item->unidadMedida ? $item->unidadMedida->abreviatura : '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $item->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($item->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('items.show', $item) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Ver</a>
                                        @can('update', $item)
                                        <a href="{{ route('items.edit', $item) }}" class="text-blue-600 hover:text-blue-900 font-medium">Editar</a>
                                        @endcan
                                        @can('delete', $item)
                                        <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro de eliminar este ítem?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Eliminar</button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                                        No se encontraron ítems en el catálogo.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($items->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $items->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
