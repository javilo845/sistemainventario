<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Sucursales') }}
            </h2>
            <a href="{{ route('sucursales.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nueva Sucursal
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <!-- Filtros -->
            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                <form method="GET" action="{{ route('sucursales.index') }}" class="flex flex-col sm:flex-row gap-3">
                    <div class="flex-1">
                        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre de sucursal..." class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    @if(auth()->user()->hasRole('Super Administrador') && $empresas->isNotEmpty())
                        <div class="w-full sm:w-64">
                            <select name="empresa_id" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todas las empresas</option>
                                @foreach($empresas as $emp)
                                    <option value="{{ $emp->id }}" {{ request('empresa_id') == $emp->id ? 'selected' : '' }}>{{ $emp->nombre }}</option>
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
                    @if(request()->anyFilled(['buscar', 'empresa_id', 'estado']))
                        <a href="{{ route('sucursales.index') }}" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium text-center transition">
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
                                <th class="px-6 py-3">Sucursal</th>
                                @if(auth()->user()->hasRole('Super Administrador'))
                                    <th class="px-6 py-3">Empresa</th>
                                @endif
                                <th class="px-6 py-3">Dirección y Teléfono</th>
                                <th class="px-6 py-3 text-center">Áreas Asociadas</th>
                                <th class="px-6 py-3 text-center">Estado</th>
                                <th class="px-6 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($sucursales as $sucursal)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        <a href="{{ route('sucursales.show', $sucursal) }}" class="hover:text-indigo-600">
                                            {{ $sucursal->nombre }}
                                        </a>
                                    </td>
                                    @if(auth()->user()->hasRole('Super Administrador'))
                                        <td class="px-6 py-4 text-gray-600">{{ $sucursal->empresa ? $sucursal->empresa->nombre : 'N/A' }}</td>
                                    @endif
                                    <td class="px-6 py-4 text-gray-600">
                                        <div>{{ $sucursal->direccion ?: 'Sin dirección' }}</div>
                                        <div class="text-xs text-gray-400">Tel: {{ $sucursal->telefono ?: 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $sucursal->areas_count }} áreas
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sucursal->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($sucursal->estado) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="{{ route('sucursales.show', $sucursal) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">Ver</a>
                                        <a href="{{ route('sucursales.edit', $sucursal) }}" class="text-blue-600 hover:text-blue-900 font-medium">Editar</a>
                                        <form action="{{ route('sucursales.destroy', $sucursal) }}" method="POST" class="inline" onsubmit="return confirm('¿Seguro que deseas eliminar esta sucursal?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 font-medium">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                                        No se encontraron sucursales registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sucursales->hasPages())
                    <div class="p-4 border-t border-gray-100">
                        {{ $sucursales->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
