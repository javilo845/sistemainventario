<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Item;
use App\Models\Proveedor;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    private function resolveEmpresaId(): ?int
    {
        return auth()->user()->hasRole('Super Administrador') ? null : auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Item::class);

        $query = Item::query()->with(['categoria', 'unidadMedida', 'proveedor', 'inventarios']);

        $empresaId = $this->resolveEmpresaId();
        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        if ($request->filled('buscar')) {
            $search = $request->get('buscar');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->get('categoria_id'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        $items = $query->latest()->paginate(10)->withQueryString();

        $categorias = Categoria::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->get();

        return view('items.index', compact('items', 'categorias'));
    }

    public function create()
    {
        $this->authorize('create', Item::class);

        $empresaId = $this->resolveEmpresaId();

        $categorias = Categoria::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->get();

        $unidades = UnidadMedida::all();

        $proveedores = Proveedor::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->get();

        return view('items.create', compact('categorias', 'unidades', 'proveedores'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Item::class);

        $empresaId = auth()->user()->empresa_id;

        if (!$request->filled('sku')) {
            $request->merge(['sku' => 'SKU-' . strtoupper(Str::random(6))]);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'required|string|max:50',
            'categoria_id' => 'required|exists:categorias,id',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'descripcion' => 'nullable|string',
            'costo_unitario' => 'nullable|numeric|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'estado' => 'required|in:activo,inactivo',
            'imagen' => 'nullable|image|max:2048',
        ]);

        // Validar SKU único por empresa
        $existsSku = Item::where('empresa_id', $empresaId)->where('sku', $validated['sku'])->exists();
        if ($existsSku) {
            return back()->withInput()->withErrors(['sku' => 'El código/SKU ya existe en esta empresa.']);
        }

        $validated['empresa_id'] = $empresaId;
        $validated['costo_unitario'] = $validated['costo_unitario'] ?? 0;
        $validated['stock_minimo'] = $validated['stock_minimo'] ?? 0;

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('items', 'public');
        }

        $item = Item::create($validated);

        return redirect()->route('items.index')->with('success', 'Ítem registrado con éxito en el catálogo.');
    }

    public function show(Item $item)
    {
        $this->authorize('view', $item);

        $item->load([
            'categoria',
            'unidadMedida',
            'proveedor',
            'inventarios.area.sucursal',
            'inventarios.area.encargado',
            'movimientos' => fn($q) => $q->latest('created_at')->take(10)->with(['areaOrigen', 'areaDestino', 'usuario'])
        ]);

        return view('items.show', compact('item'));
    }

    public function edit(Item $item)
    {
        $this->authorize('update', $item);

        $empresaId = $this->resolveEmpresaId();

        $categorias = Categoria::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->get();

        $unidades = UnidadMedida::all();

        $proveedores = Proveedor::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->get();

        return view('items.edit', compact('item', 'categorias', 'unidades', 'proveedores'));
    }

    public function update(Request $request, Item $item)
    {
        $this->authorize('update', $item);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'required|string|max:50',
            'categoria_id' => 'required|exists:categorias,id',
            'unidad_medida_id' => 'required|exists:unidades_medida,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'descripcion' => 'nullable|string',
            'costo_unitario' => 'nullable|numeric|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'estado' => 'required|in:activo,inactivo',
            'imagen' => 'nullable|image|max:2048',
        ]);

        // Validar SKU único por empresa excluyendo el actual
        $existsSku = Item::where('empresa_id', $item->empresa_id)
            ->where('sku', $validated['sku'])
            ->where('id', '!=', $item->id)
            ->exists();

        if ($existsSku) {
            return back()->withInput()->withErrors(['sku' => 'El código/SKU ya está en uso por otro ítem en esta empresa.']);
        }

        if ($request->hasFile('imagen')) {
            if ($item->imagen) {
                Storage::disk('public')->delete($item->imagen);
            }
            $validated['imagen'] = $request->file('imagen')->store('items', 'public');
        }

        $item->update($validated);

        return redirect()->route('items.index')->with('success', 'Ítem actualizado con éxito.');
    }

    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);

        // Validar si tiene stock físico activo > 0 en alguna área
        $stockTotal = $item->inventarios()->sum('cantidad');

        if ($stockTotal > 0) {
            return back()->with('error', "No se puede eliminar el ítem '{$item->nombre}' porque tiene {$stockTotal} unidades de stock activo en inventario. Debe dar salida o trasladar primero.");
        }

        $item->delete();

        return redirect()->route('items.index')->with('success', 'Ítem eliminado del catálogo (soft delete).');
    }

    public function inventario(Item $item)
    {
        $this->authorize('view', $item);

        $inventarios = $item->inventarios()
            ->with(['area.sucursal', 'area.encargado'])
            ->where('cantidad', '>', 0)
            ->get();

        return response()->json([
            'item' => $item->only(['id', 'nombre', 'sku', 'stock_minimo']),
            'stock_total' => $item->stock_total,
            'areas' => $inventarios->map(function ($inv) {
                return [
                    'area_id' => $inv->area_id,
                    'area_nombre' => $inv->area->nombre,
                    'sucursal_nombre' => $inv->area->sucursal->nombre,
                    'encargado' => $inv->area->encargado ? $inv->area->encargado->name : 'Sin asignar',
                    'cantidad' => (float) $inv->cantidad,
                ];
            })
        ]);
    }
}
