<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-2xl text-gray-900 leading-tight">
                    {{ __('Dashboard de Inventario') }}
                </h2>
                <p class="text-xs text-gray-500">Resumen ejecutivo y control en tiempo real para Mipymes.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('movimientos.create', ['tipo' => 'entrada']) }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Entrada
                </a>
                <a href="{{ route('movimientos.create', ['tipo' => 'traslado']) }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    Traslado
                </a>
                <a href="{{ route('reportes.inventario') }}" class="px-3.5 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-md text-xs font-bold uppercase tracking-wider flex items-center gap-1.5 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Reportes
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- KPIs Principales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- KPI 1 -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ítems en Catálogo</span>
                        <div class="text-3xl font-black text-gray-900 mt-1">{{ number_format($totalItems) }}</div>
                        <span class="text-xs text-indigo-600 font-medium mt-1 inline-block">Artículos registrados</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                </div>

                <!-- KPI 2 -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Stock Físico Total</span>
                        <div class="text-3xl font-black text-emerald-600 mt-1">{{ number_format($totalStockUnidades, 2) }}</div>
                        <span class="text-xs text-gray-500 font-medium mt-1 inline-block">Unidades consolidadas</span>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    </div>
                </div>

                <!-- KPI 3 -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Alertas de Stock Mínimo</span>
                        <div class="text-3xl font-black {{ $itemsBajoMinimo->count() > 0 ? 'text-red-600' : 'text-gray-900' }} mt-1">
                            {{ $itemsBajoMinimo->count() }}
                        </div>
                        <span class="text-xs {{ $itemsBajoMinimo->count() > 0 ? 'text-red-500 font-bold animate-pulse' : 'text-gray-400' }} mt-1 inline-block">
                            {{ $itemsBajoMinimo->count() > 0 ? 'Requieren reposición inmediata' : 'Inventario en niveles óptimos' }}
                        </span>
                    </div>
                    <div class="w-12 h-12 rounded-xl {{ $itemsBajoMinimo->count() > 0 ? 'bg-red-50 text-red-600' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Gráficos y Desglose por Sucursal -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Gráfico Chart.js: Categorías -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 lg:col-span-2">
                    <h3 class="font-bold text-gray-900 text-base mb-1">Distribución de Catálogo por Categoría</h3>
                    <p class="text-xs text-gray-400 mb-4">Concentración de ítems activos registrados por tipo de producto.</p>
                    <div class="h-64 relative">
                        <canvas id="categoriasChart"></canvas>
                    </div>
                </div>

                <!-- Stock Físico por Sucursal -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base mb-1">Stock por Sucursal</h3>
                        <p class="text-xs text-gray-400 mb-4">Unidades físicas acumuladas en cada sede.</p>
                        <div class="space-y-3">
                            @forelse($stockPorSucursal as $itemSuc)
                                <div class="p-3 rounded-lg bg-gray-50 border border-gray-100 flex justify-between items-center">
                                    <span class="font-semibold text-sm text-gray-800">{{ $itemSuc['sucursal'] }}</span>
                                    <span class="font-mono font-bold text-sm text-indigo-600">{{ number_format($itemSuc['total_stock'], 2) }} u</span>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic">Sin sucursales registradas.</p>
                            @endforelse
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t text-right">
                        <a href="{{ route('sucursales.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Ver todas las sucursales &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Alerta Visual: Ítems por debajo del stock mínimo -->
            @if($itemsBajoMinimo->isNotEmpty())
                <div class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden">
                    <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-red-500 animate-ping"></span>
                            <h3 class="font-bold text-red-900 text-sm">Alerta Crítica: Ítems con Stock por debajo del Mínimo</h3>
                        </div>
                        <span class="text-xs text-red-700 font-semibold">{{ $itemsBajoMinimo->count() }} ítems requieren reposición</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-red-100 text-xs text-left">
                            <thead class="bg-red-50/50 text-red-800 font-semibold uppercase">
                                <tr>
                                    <th class="px-6 py-3">Código/SKU</th>
                                    <th class="px-6 py-3">Ítem</th>
                                    <th class="px-6 py-3">Categoría</th>
                                    <th class="px-6 py-3 text-center">Stock Mínimo</th>
                                    <th class="px-6 py-3 text-center">Stock Físico Actual</th>
                                    <th class="px-6 py-3 text-center">Déficit</th>
                                    <th class="px-6 py-3 text-right">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-red-50">
                                @foreach($itemsBajoMinimo as $item)
                                    @php
                                        $stock = $item->stock_total;
                                        $deficit = $item->stock_minimo - $stock;
                                    @endphp
                                    <tr class="hover:bg-red-50/30">
                                        <td class="px-6 py-3 font-mono font-bold text-red-900">{{ $item->sku }}</td>
                                        <td class="px-6 py-3 font-bold text-gray-900">{{ $item->nombre }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $item->categoria ? $item->categoria->nombre : '-' }}</td>
                                        <td class="px-6 py-3 text-center font-semibold text-gray-700">{{ $item->stock_minimo }}</td>
                                        <td class="px-6 py-3 text-center font-bold text-red-600 text-sm">{{ number_format($stock, 2) }}</td>
                                        <td class="px-6 py-3 text-center">
                                            <span class="px-2 py-0.5 rounded bg-red-100 text-red-800 font-bold text-[10px]">
                                                -{{ number_format($deficit, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-right">
                                            <a href="{{ route('movimientos.create', ['tipo' => 'entrada', 'item_id' => $item->id]) }}" class="px-2.5 py-1 bg-emerald-600 text-white rounded text-[11px] font-semibold hover:bg-emerald-700">
                                                Reabastecer
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- Últimos 10 Movimientos -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Últimos Movimientos de Inventario</h3>
                        <p class="text-xs text-gray-400">Actividad reciente registrada en la bitácora oficial.</p>
                    </div>
                    <a href="{{ route('movimientos.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Ver bitácora completa &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-xs text-left">
                        <thead class="bg-gray-50 text-gray-600 uppercase font-semibold">
                            <tr>
                                <th class="px-4 py-2.5">Fecha</th>
                                <th class="px-4 py-2.5">Tipo</th>
                                <th class="px-4 py-2.5">Ítem</th>
                                <th class="px-4 py-2.5 text-right">Cantidad</th>
                                <th class="px-4 py-2.5">Origen &rarr; Destino</th>
                                <th class="px-4 py-2.5">Usuario</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($ultimosMovimientos as $mov)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 font-mono text-gray-500 whitespace-nowrap">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $clases = [
                                                'entrada' => 'bg-emerald-100 text-emerald-800',
                                                'salida' => 'bg-red-100 text-red-800',
                                                'traslado' => 'bg-indigo-100 text-indigo-800',
                                                'ajuste' => 'bg-amber-100 text-amber-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $clases[$mov->tipo] ?? 'bg-gray-100' }}">
                                            {{ $mov->tipo }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $mov->item ? $mov->item->nombre : 'N/A' }}</td>
                                    <td class="px-4 py-3 text-right font-bold font-mono">{{ number_format($mov->cantidad, 2) }}</td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $mov->areaOrigen ? $mov->areaOrigen->nombre : '-' }}
                                        &rarr;
                                        {{ $mov->areaDestino ? $mov->areaDestino->nombre : '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">{{ $mov->usuario ? $mov->usuario->name : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-400">Sin movimientos registrados recientemente.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('categoriasChart');
            if (!ctx) return;

            const labels = @json($categoriasChart->pluck('nombre'));
            const dataValues = @json($categoriasChart->pluck('total'));

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.length ? labels : ['Sin datos'],
                    datasets: [{
                        label: 'Número de Ítems',
                        data: dataValues.length ? dataValues : [0],
                        backgroundColor: '#6366f1',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
