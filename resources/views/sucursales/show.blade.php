<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $sucursal->nombre }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('sucursales.edit', $sucursal) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition">
                    Editar Sucursal
                </a>
                <a href="{{ route('sucursales.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition">
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
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Empresa</span>
                        <span class="text-gray-800 font-medium">{{ $sucursal->empresa ? $sucursal->empresa->nombre : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Teléfono</span>
                        <span class="text-gray-800 font-medium">{{ $sucursal->telefono ?: 'No registrado' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Dirección</span>
                        <span class="text-gray-800 font-medium">{{ $sucursal->direccion ?: 'No registrada' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Estado</span>
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sucursal->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($sucursal->estado) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Áreas de la sucursal -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Áreas de esta Sucursal</h3>
                    <a href="{{ route('areas.create') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                        + Nueva Área
                    </a>
                </div>

                @if($sucursal->areas->isEmpty())
                    <p class="text-gray-400 text-sm py-4">No hay áreas configuradas en esta sucursal.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($sucursal->areas as $area)
                            <div class="border rounded-lg p-4 bg-gray-50 hover:bg-white transition shadow-sm flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-bold text-gray-900">{{ $area->nombre }}</h4>
                                        <span class="text-xs px-2 py-0.5 rounded {{ $area->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ ucfirst($area->estado) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-3">{{ $area->descripcion ?: 'Sin descripción adicional' }}</p>
                                    <div class="text-xs text-gray-600 mb-2">
                                        <span class="font-semibold">Encargado:</span> {{ $area->encargado ? $area->encargado->name : 'Sin asignar' }}
                                    </div>
                                    <div class="text-xs text-gray-600">
                                        <span class="font-semibold">Ítems con stock:</span> {{ $area->inventarios->where('cantidad', '>', 0)->count() }}
                                    </div>
                                </div>
                                <div class="mt-4 pt-2 border-t flex justify-end gap-2 text-xs font-semibold">
                                    <a href="{{ route('areas.show', $area) }}" class="text-indigo-600 hover:text-indigo-900">Ver Área</a>
                                    <a href="{{ route('areas.edit', $area) }}" class="text-blue-600 hover:text-blue-900">Editar</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
