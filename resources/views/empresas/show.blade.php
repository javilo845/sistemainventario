<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $empresa->nombre }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('empresas.edit', $empresa) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 transition">
                    Editar Empresa
                </a>
                <a href="{{ route('empresas.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-200 transition">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Ficha informativa -->
            <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 flex flex-col sm:flex-row gap-6 items-start">
                @if($empresa->logo)
                    <img src="{{ asset('storage/' . $empresa->logo) }}" alt="Logo" class="w-24 h-24 rounded-lg object-cover border">
                @else
                    <div class="w-24 h-24 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-2xl uppercase">
                        {{ substr($empresa->nombre, 0, 2) }}
                    </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 flex-1 text-sm">
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Identificación Fiscal / RTN</span>
                        <span class="font-mono text-gray-800 font-medium">{{ $empresa->identificacion_fiscal }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Teléfono</span>
                        <span class="text-gray-800 font-medium">{{ $empresa->telefono ?: 'No registrado' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Correo Electrónico</span>
                        <span class="text-gray-800 font-medium">{{ $empresa->correo ?: 'No registrado' }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Dirección</span>
                        <span class="text-gray-800 font-medium">{{ $empresa->direccion ?: 'No registrada' }}</span>
                    </div>
                    <div>
                        <span class="block text-gray-400 text-xs uppercase font-semibold">Estado</span>
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $empresa->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($empresa->estado) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sucursales asociadas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Sucursales y Áreas</h3>
                    <a href="{{ route('sucursales.create', ['empresa_id' => $empresa->id]) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                        + Nueva Sucursal
                    </a>
                </div>

                @if($empresa->sucursales->isEmpty())
                    <p class="text-gray-400 text-sm py-4">No hay sucursales registradas para esta empresa.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($empresa->sucursales as $sucursal)
                            <div class="border rounded-lg p-4 bg-gray-50 hover:bg-white transition shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-bold text-gray-900 text-base">{{ $sucursal->nombre }}</h4>
                                    <span class="text-xs px-2 py-0.5 rounded {{ $sucursal->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ ucfirst($sucursal->estado) }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mb-3">{{ $sucursal->direccion ?: 'Sin dirección' }} • Tel: {{ $sucursal->telefono ?: 'N/A' }}</p>

                                <div class="border-t pt-2 mt-2">
                                    <span class="text-xs font-semibold text-gray-600 uppercase">Áreas ({{ $sucursal->areas->count() }}):</span>
                                    <ul class="mt-1 space-y-1">
                                        @forelse($sucursal->areas as $area)
                                            <li class="text-xs text-gray-700 flex justify-between bg-white px-2 py-1 rounded border">
                                                <span>{{ $area->nombre }}</span>
                                                <span class="text-gray-500">Resp: {{ $area->encargado ? $area->encargado->name : 'Sin asignar' }}</span>
                                            </li>
                                        @empty
                                            <li class="text-xs text-gray-400 italic">Sin áreas registradas.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
