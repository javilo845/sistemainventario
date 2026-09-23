<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Services\InventarioService;
use Exception;
use Illuminate\Http\Request;

class MovimientoInventarioController extends Controller
{
    public function __construct(
        protected InventarioService $inventarioService
    ) {}

    private function resolveEmpresaId(): ?int
    {
        return auth()->user()->hasRole('Super Administrador') ? null : auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', MovimientoInventario::class);

        $query = MovimientoInventario::query()->with(['item.unidadMedida', 'areaOrigen', 'areaDestino', 'usuario']);

        $empresaId = $this->resolveEmpresaId();
        if ($empresaId) {
            $query->whereHas('item', fn($q) => $q->where('empresa_id', $empresaId));
        }

        // Si es Encargado de Área, filtrar solo donde su área sea origen o destino
        if (auth()->user()->hasRole('Encargado de Área')) {
            $areaIds = auth()->user()->areas()->pluck('id');
            $query->where(function ($q) use ($areaIds) {
                $q->whereIn('area_origen_id', $areaIds)
                  ->orWhereIn('area_destino_id', $areaIds);
            });
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

        $movimientos = $query->latest('created_at')->paginate(15)->withQueryString();

        $items = Item::query()->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();
        $areas = Area::query()
            ->when($empresaId, fn($q) => $q->whereHas('sucursal', fn($sq) => $sq->where('empresa_id', $empresaId)))
            ->get();

        return view('movimientos.index', compact('movimientos', 'items', 'areas'));
    }

    public function create(Request $request)
    {
        $empresaId = $this->resolveEmpresaId();

        $items = Item::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->where('estado', 'activo')
            ->get();

        $areas = Area::query()
            ->with(['sucursal', 'encargado'])
            ->when($empresaId, fn($q) => $q->whereHas('sucursal', fn($sq) => $sq->where('empresa_id', $empresaId)))
            ->where('estado', 'activo')
            ->get();

        $tipo = $request->get('tipo', 'traslado');
        $selectedItemId = $request->get('item_id');
        $selectedAreaOrigenId = $request->get('area_origen_id');

        return view('movimientos.create', compact('items', 'areas', 'tipo', 'selectedItemId', 'selectedAreaOrigenId'));
    }

    public function storeEntrada(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'area_destino_id' => 'required|exists:areas,id',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'nullable|string|max:500',
        ]);

        try {
            $this->inventarioService->registrarEntrada(
                $validated['item_id'],
                $validated['area_destino_id'],
                (float) $validated['cantidad'],
                auth()->id(),
                $validated['motivo']
            );

            return redirect()->route('movimientos.index')->with('success', 'Entrada de inventario registrada con éxito.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function storeSalida(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'area_origen_id' => 'required|exists:areas,id',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'nullable|string|max:500',
        ]);

        try {
            $this->inventarioService->registrarSalida(
                $validated['item_id'],
                $validated['area_origen_id'],
                (float) $validated['cantidad'],
                auth()->id(),
                $validated['motivo']
            );

            return redirect()->route('movimientos.index')->with('success', 'Salida de inventario procesada correctamente.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function storeTraslado(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'area_origen_id' => 'required|exists:areas,id|different:area_destino_id',
            'area_destino_id' => 'required|exists:areas,id',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'nullable|string|max:500',
        ]);

        try {
            $this->inventarioService->registrarTraslado(
                $validated['item_id'],
                $validated['area_origen_id'],
                $validated['area_destino_id'],
                (float) $validated['cantidad'],
                auth()->id(),
                $validated['motivo']
            );

            return redirect()->route('movimientos.index')->with('success', 'Traslado completado. Stock e inventario reasignados al responsable del área destino.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function storeAjuste(Request $request)
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'area_id' => 'required|exists:areas,id',
            'cantidad' => 'required|numeric',
            'motivo' => 'required|string|min:5|max:500',
        ]);

        try {
            $this->inventarioService->registrarAjuste(
                $validated['item_id'],
                $validated['area_id'],
                (float) $validated['cantidad'],
                auth()->id(),
                $validated['motivo']
            );

            return redirect()->route('movimientos.index')->with('success', 'Ajuste manual de inventario registrado en bitácora.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Endpoint AJAX para consultar stock disponible en tiempo real en un área.
     */
    public function consultarStock(Request $request)
    {
        $itemId = (int) $request->get('item_id');
        $areaId = (int) $request->get('area_id');

        $stock = $this->inventarioService->obtenerStock($itemId, $areaId);

        return response()->json([
            'item_id' => $itemId,
            'area_id' => $areaId,
            'stock_disponible' => $stock,
        ]);
    }
}
