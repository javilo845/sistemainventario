<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function resolveEmpresaId(): ?int
    {
        return auth()->user()->hasRole('Super Administrador') ? null : auth()->user()->empresa_id;
    }

    public function index()
    {
        $empresaId = $this->resolveEmpresaId();

        // 1. Total ítems en catálogo
        $totalItems = Item::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->count();

        // 2. Total unidades físicas en stock consolidado
        $totalStockUnidades = InventarioArea::query()
            ->when($empresaId, function ($q) use ($empresaId) {
                $q->whereHas('item', fn($iq) => $iq->where('empresa_id', $empresaId));
            })
            ->sum('cantidad');

        // 3. Stock físico por sucursal
        $stockPorSucursal = Sucursal::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->with(['areas.inventarios'])
            ->get()
            ->map(function ($sucursal) {
                $total = 0;
                foreach ($sucursal->areas as $area) {
                    $total += $area->inventarios->sum('cantidad');
                }
                return [
                    'sucursal' => $sucursal->nombre,
                    'total_stock' => (float) $total,
                ];
            });

        // 4. Ítems por debajo del stock mínimo (alerta visual)
        $items = Item::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->with(['unidadMedida', 'categoria', 'inventarios'])
            ->get();

        $itemsBajoMinimo = $items->filter(function ($item) {
            return $item->stock_total < $item->stock_minimo;
        })->values();

        // 5. Últimos 10 movimientos registrados
        $ultimosMovimientos = MovimientoInventario::query()
            ->when($empresaId, function ($q) use ($empresaId) {
                $q->whereHas('item', fn($iq) => $iq->where('empresa_id', $empresaId));
            })
            ->with(['item.unidadMedida', 'areaOrigen', 'areaDestino', 'usuario'])
            ->latest('created_at')
            ->take(10)
            ->get();

        // 6. Datos para gráfico Chart.js: Distribución por categoría
        $categoriasChart = Categoria::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->withCount('items')
            ->get()
            ->map(fn($cat) => [
                'nombre' => $cat->nombre,
                'total' => $cat->items_count,
            ]);

        return view('dashboard', compact(
            'totalItems',
            'totalStockUnidades',
            'stockPorSucursal',
            'itemsBajoMinimo',
            'ultimosMovimientos',
            'categoriasChart'
        ));
    }
}
