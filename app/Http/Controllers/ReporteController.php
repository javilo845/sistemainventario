<?php

namespace App\Http\Controllers;

use App\Exports\InventarioExport;
use App\Exports\MovimientosExport;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\Sucursal;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    private function resolveEmpresaId(): ?int
    {
        return auth()->user()->hasRole('Super Administrador') ? null : auth()->user()->empresa_id;
    }

    private function getInventarioQuery(Request $request)
    {
        $empresaId = $this->resolveEmpresaId();

        $query = InventarioArea::query()
            ->with(['item.categoria', 'item.unidadMedida', 'area.sucursal', 'area.encargado'])
            ->where('cantidad', '>', 0);

        if ($empresaId) {
            $query->whereHas('item', fn($q) => $q->where('empresa_id', $empresaId));
        }

        if ($request->filled('sucursal_id')) {
            $sucursalId = $request->get('sucursal_id');
            $query->whereHas('area', fn($q) => $q->where('sucursal_id', $sucursalId));
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->get('area_id'));
        }

        if ($request->filled('categoria_id')) {
            $categoriaId = $request->get('categoria_id');
            $query->whereHas('item', fn($q) => $q->where('categoria_id', $categoriaId));
        }

        return $query;
    }

    public function inventario(Request $request)
    {
        $empresaId = $this->resolveEmpresaId();

        $query = $this->getInventarioQuery($request);
        $inventarios = (clone $query)->paginate(20)->withQueryString();

        $totalValor = (clone $query)->get()->sum(function ($inv) {
            return $inv->cantidad * ($inv->item ? $inv->item->costo_unitario : 0);
        });

        $sucursales = Sucursal::query()->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();
        $areas = Area::query()->when($empresaId, fn($q) => $q->whereHas('sucursal', fn($sq) => $sq->where('empresa_id', $empresaId)))->get();
        $categorias = Categoria::query()->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();

        return view('reportes.inventario', compact('inventarios', 'sucursales', 'areas', 'categorias', 'totalValor'));
    }

    public function exportarInventarioExcel(Request $request)
    {
        $query = $this->getInventarioQuery($request);
        return Excel::download(new InventarioExport($query), 'reporte-inventario-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function exportarInventarioPdf(Request $request)
    {
        $query = $this->getInventarioQuery($request);
        $inventarios = $query->get();

        $totalValor = $inventarios->sum(function ($inv) {
            return $inv->cantidad * ($inv->item ? $inv->item->costo_unitario : 0);
        });

        $pdf = Pdf::loadView('reportes.pdf-inventario', compact('inventarios', 'totalValor'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('reporte-inventario-' . now()->format('Ymd-His') . '.pdf');
    }

    private function getMovimientosQuery(Request $request)
    {
        $empresaId = $this->resolveEmpresaId();

        $query = MovimientoInventario::query()
            ->with(['item.unidadMedida', 'areaOrigen', 'areaDestino', 'usuario']);

        if ($empresaId) {
            $query->whereHas('item', fn($q) => $q->where('empresa_id', $empresaId));
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->get('tipo'));
        }

        if ($request->filled('item_id')) {
            $query->where('item_id', $request->get('item_id'));
        }

        if ($request->filled('area_id')) {
            $areaId = $request->get('area_id');
            $query->where(fn($q) => $q->where('area_origen_id', $areaId)->orWhere('area_destino_id', $areaId));
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->get('fecha_desde'));
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->get('fecha_hasta'));
        }

        return $query->latest('created_at');
    }

    public function movimientos(Request $request)
    {
        $empresaId = $this->resolveEmpresaId();

        $query = $this->getMovimientosQuery($request);
        $movimientos = (clone $query)->paginate(20)->withQueryString();

        $items = Item::query()->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();
        $areas = Area::query()->when($empresaId, fn($q) => $q->whereHas('sucursal', fn($sq) => $sq->where('empresa_id', $empresaId)))->get();

        return view('reportes.movimientos', compact('movimientos', 'items', 'areas'));
    }

    public function exportarMovimientosExcel(Request $request)
    {
        $query = $this->getMovimientosQuery($request);
        return Excel::download(new MovimientosExport($query), 'reporte-movimientos-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function exportarMovimientosPdf(Request $request)
    {
        $query = $this->getMovimientosQuery($request);
        $movimientos = $query->get();

        $pdf = Pdf::loadView('reportes.pdf-movimientos', compact('movimientos'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('reporte-movimientos-' . now()->format('Ymd-His') . '.pdf');
    }
}
