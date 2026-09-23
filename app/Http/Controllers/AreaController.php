<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    private function resolveEmpresaId(): ?int
    {
        return auth()->user()->hasRole('Super Administrador') ? null : auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Area::class);

        $query = Area::query()->with(['sucursal.empresa', 'encargado']);

        $empresaId = $this->resolveEmpresaId();
        if ($empresaId) {
            $query->whereHas('sucursal', function ($q) use ($empresaId) {
                $q->where('empresa_id', $empresaId);
            });
        }

        // Si es Encargado de Área, solo ve sus áreas asignadas
        if (auth()->user()->hasRole('Encargado de Área')) {
            $query->where('encargado_id', auth()->id());
        }

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->get('sucursal_id'));
        }

        if ($request->filled('buscar')) {
            $search = $request->get('buscar');
            $query->where('nombre', 'like', "%{$search}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        $areas = $query->latest()->paginate(10)->withQueryString();

        $sucursalesQuery = Sucursal::query();
        if ($empresaId) {
            $sucursalesQuery->where('empresa_id', $empresaId);
        }
        $sucursales = $sucursalesQuery->where('estado', 'activo')->get();

        return view('areas.index', compact('areas', 'sucursales'));
    }

    public function create()
    {
        $this->authorize('create', Area::class);

        $empresaId = $this->resolveEmpresaId();

        $sucursales = Sucursal::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->where('estado', 'activo')
            ->get();

        $usuarios = User::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->where('estado', 'activo')
            ->get();

        return view('areas.create', compact('sucursales', 'usuarios'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Area::class);

        $validated = $request->validate([
            'sucursal_id' => 'required|exists:sucursales,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'encargado_id' => 'nullable|exists:users,id',
            'estado' => 'required|in:activo,inactivo',
        ]);

        Area::create($validated);

        return redirect()->route('areas.index')->with('success', 'Área creada exitosamente.');
    }

    public function show(Area $area)
    {
        $this->authorize('view', $area);
        $area->load(['sucursal.empresa', 'encargado', 'inventarios.item.unidadMedida', 'inventarios.item.categoria']);
        return view('areas.show', compact('area'));
    }

    public function edit(Area $area)
    {
        $this->authorize('update', $area);

        $empresaId = $this->resolveEmpresaId();

        $sucursales = Sucursal::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->where('estado', 'activo')
            ->get();

        $usuarios = User::query()
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->where('estado', 'activo')
            ->get();

        return view('areas.edit', compact('area', 'sucursales', 'usuarios'));
    }

    public function update(Request $request, Area $area)
    {
        $this->authorize('update', $area);

        $validated = $request->validate([
            'sucursal_id' => 'required|exists:sucursales,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'encargado_id' => 'nullable|exists:users,id',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $area->update($validated);

        return redirect()->route('areas.index')->with('success', 'Área actualizada exitosamente.');
    }

    public function destroy(Area $area)
    {
        $this->authorize('delete', $area);

        // Validar si tiene stock activo en el inventario
        $hasStock = $area->inventarios()->where('cantidad', '>', 0)->exists();

        if ($hasStock) {
            return back()->with('error', 'No se puede eliminar el área porque tiene stock activo en inventario. Traslade o dé salida primero.');
        }

        $area->delete();

        return redirect()->route('areas.index')->with('success', 'Área eliminada lógicamente.');
    }
}
