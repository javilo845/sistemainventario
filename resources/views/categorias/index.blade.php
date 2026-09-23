<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Categorías de Inventario') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('items.index') }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded hover:bg-gray-200 transition">
                    Ver Catálogo
                </a>
                <a href="{{ route('categorias.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700 transition">
                    + Nueva Categoría
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex justify-between gap-3">
                <form method="GET" action="{{ route('categorias.index') }}" class="flex-1 flex gap-2">
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre..." class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-700">Filtrar</button>
                    @if(request('buscar'))
                        <a href="{{ route('categorias.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md text-sm">Limpiar</a>
                    @endif
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3">Nombre</th>
                            <th class="px-6 py-3 text-center">Ítems Asociados</th>
                            <th class="px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($categorias as $categoria)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $categoria->nombre }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 font-medium">
                                        {{ $categoria->items_count }} ítems
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('categorias.edit', $categoria) }}" class="text-blue-600 hover:text-blue-900 font-medium">Editar</a>
                                    <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro de eliminar esta categoría?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400">No hay categorías registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($categorias->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $categorias->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
