<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    private function resolveEmpresaId(): ?int
    {
        return auth()->user()->hasRole('Super Administrador') ? null : auth()->user()->empresa_id;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Proveedor::class);

        $query = Proveedor::query()->withCount('items');

        $empresaId = $this->resolveEmpresaId();
        if ($empresaId) {
            $query->where('empresa_id', $empresaId);
        }

        if ($request->filled('buscar')) {
            $search = $request->get('buscar');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('contacto', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%");
            });
        }

        $proveedores = $query->latest()->paginate(10)->withQueryString();

        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        $this->authorize('create', Proveedor::class);
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Proveedor::class);

        $empresaId = auth()->user()->empresa_id;

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'correo' => 'nullable|email|max:255',
        ]);

        $validated['empresa_id'] = $empresaId;

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor registrado con éxito.');
    }

    public function edit(Proveedor $proveedor)
    {
        $this->authorize('update', $proveedor);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $this->authorize('update', $proveedor);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:30',
            'correo' => 'nullable|email|max:255',
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')->with('success', 'Proveedor actualizado con éxito.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $this->authorize('delete', $proveedor);

        if ($proveedor->items()->exists()) {
            return back()->with('error', 'No se puede eliminar el proveedor porque tiene ítems asociados.');
        }

        $proveedor->delete();

        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado lógicamente.');
    }
}
