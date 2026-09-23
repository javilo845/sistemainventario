<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Proveedores') }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('items.index') }}" class="px-3 py-1.5 bg-gray-100 text-gray-700 text-sm font-medium rounded hover:bg-gray-200 transition">
                    Ver Catálogo
                </a>
                <a href="{{ route('proveedores.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700 transition">
                    + Nuevo Proveedor
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100 flex justify-between gap-3">
                <form method="GET" action="{{ route('proveedores.index') }}" class="flex-1 flex gap-2">
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre, persona de contacto o correo..." class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm font-medium hover:bg-gray-700">Filtrar</button>
                    @if(request('buscar'))
                        <a href="{{ route('proveedores.index') }}" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md text-sm">Limpiar</a>
                    @endif
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-3">Proveedor</th>
                            <th class="px-6 py-3">Contacto Directo</th>
                            <th class="px-6 py-3">Teléfono / Correo</th>
                            <th class="px-6 py-3 text-center">Ítems Suministrados</th>
                            <th class="px-6 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($proveedores as $prov)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $prov->nombre }}</td>
                                <td class="px-6 py-4 text-gray-700">{{ $prov->contacto ?: 'N/A' }}</td>
                                <td class="px-6 py-4 text-gray-600">
                                    <div>{{ $prov->correo ?: 'N/A' }}</div>
                                    <div class="text-xs text-gray-400">Tel: {{ $prov->telefono ?: 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 font-medium">
                                        {{ $prov->items_count }} ítems
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('proveedores.edit', $prov) }}" class="text-blue-600 hover:text-blue-900 font-medium">Editar</a>
                                    <form action="{{ route('proveedores.destroy', $prov) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro de eliminar este proveedor?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-400">No hay proveedores registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @if($proveedores->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $proveedores->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
