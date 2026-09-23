<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Http\Request;

class SucursalController extends Controller
{
    private function resolveEmpresaId(Request $request): ?int
    {
        if (auth()->user()->hasRole('Super Administrador')) {
            return $request->filled('empresa_id') ? (int) $request->get('empresa_id') : null;
        }
        return auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Sucursal::class);

        $query = Sucursal::query()->with('empresa')->withCount('areas');

        $empresaId = $this->resolveEmpresaId($request);
        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        if ($request->filled('buscar')) {
            $search = $request->get('buscar');
            $query->where('nombre', 'like', "%{$search}%");
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        $sucursales = $query->latest()->paginate(10)->withQueryString();
        $empresas = auth()->user()->hasRole('Super Administrador') ? Empresa::all() : collect();

        return view('sucursales.index', compact('sucursales', 'empresas'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', Sucursal::class);

        $empresas = auth()->user()->hasRole('Super Administrador') ? Empresa::where('estado', 'activo')->get() : collect([auth()->user()->empresa]);

        return view('sucursales.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Sucursal::class);

        $empresaId = auth()->user()->hasRole('Super Administrador') ? $request->empresa_id : auth()->user()->empresa_id;

        $request->merge(['empresa_id' => $empresaId]);

        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'estado' => 'required|in:activo,inactivo',
        ]);

        Sucursal::create($validated);

        return redirect()->route('sucursales.index')->with('success', 'Sucursal creada exitosamente.');
    }

    public function show(Sucursal $sucursal)
    {
        $this->authorize('view', $sucursal);
        $sucursal->load(['empresa', 'areas.encargado', 'areas.inventarios.item']);
        return view('sucursales.show', compact('sucursal'));
    }

    public function edit(Sucursal $sucursal)
    {
        $this->authorize('update', $sucursal);

        $empresas = auth()->user()->hasRole('Super Administrador') ? Empresa::where('estado', 'activo')->get() : collect([auth()->user()->empresa]);

        return view('sucursales.edit', compact('sucursal', 'empresas'));
    }

    public function update(Request $request, Sucursal $sucursal)
    {
        $this->authorize('update', $sucursal);

        $empresaId = auth()->user()->hasRole('Super Administrador') ? $request->empresa_id : auth()->user()->empresa_id;
        $request->merge(['empresa_id' => $empresaId]);

        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $sucursal->update($validated);

        return redirect()->route('sucursales.index')->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function destroy(Sucursal $sucursal)
    {
        $this->authorize('delete', $sucursal);

        // Validar si alguna de sus áreas tiene stock > 0
        $hasStock = $sucursal->areas()->whereHas('inventarios', function ($q) {
            $q->where('cantidad', '>', 0);
        })->exists();

        if ($hasStock) {
            return back()->with('error', 'No se puede eliminar la sucursal porque tiene áreas con inventario activo.');
        }

        $sucursal->delete();

        return redirect()->route('sucursales.index')->with('success', 'Sucursal eliminada lógicamente.');
    }
}
